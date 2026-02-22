import math
import time
import logging
import statistics
import pandas as pd
from collections import defaultdict, deque
from fastapi import APIRouter, HTTPException, Request

from app.schema import KosRequest, PredictionResponse
from app.model_loader import model_loader

# Import Prometheus Metrics (Gunakan yang sesuai dengan file metrics kamu)
try:
    from app.prometheus_metrics import (
        REQUEST_COUNT, ERROR_COUNT, REQUEST_LATENCY, LATEST_PREDICTION
    )
except ImportError:
    from app.metrics import (
        REQUEST_COUNT, ERROR_COUNT, REQUEST_LATENCY, LATEST_PREDICTION
    )

# 1. KONFIGURASI iDorm (Penting untuk Batas Wajar)
CENTRAL_COORDS = {
    "jakarta_pusat": {"lat": -6.196844836209056, "lng": 106.82268156977236},
    "jakarta_selatan": {"lat": -6.286856557630776, "lng": 106.77897447928012},
    "jakarta_utara": {"lat": -6.1595513214368856, "lng": 106.90515773880134},
    "yogyakarta": {"lat": -7.782649992051992, "lng": 110.37567189279605},
}

MAE_CONFIG = {
    "jakarta_pusat": 537550,
    "jakarta_selatan": 528010,
    "jakarta_utara": 348762,
    "yogyakarta": 235182
}

def calculate_haversine(lat1, lon1, lat2, lon2):
    R = 6371.0
    dlat, dlon = math.radians(lat2 - lat1), math.radians(lon2 - lon1)
    a = math.sin(dlat / 2)**2 + math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * math.sin(dlon / 2)**2
    return R * (2 * math.atan2(math.sqrt(a), math.sqrt(1 - a)))

# Monitoring Store
prediction_store = defaultdict(lambda: deque(maxlen=1000))
latency_store = defaultdict(lambda: deque(maxlen=1000))
error_counter = 0
logger = logging.getLogger("inference")

router = APIRouter()

@router.post("/predict/{region}", response_model=PredictionResponse)
def predict(region: str, request: KosRequest, http_request: Request):
    global error_counter
    
    clean_region = region.lower().replace(" ", "_")
    model_info = model_loader.get_model(clean_region)

    if model_info is None:
        raise HTTPException(status_code=404, detail=f"Region {region} tidak ditemukan")

    model = model_info["model"]
    version = model_info["version"]
    request_id = getattr(http_request.state, "request_id", "unknown")

    REQUEST_COUNT.labels(region=clean_region).inc()

    try:
        start_time = time.time()
        with REQUEST_LATENCY.labels(region=clean_region).time():
            
            # A. Hitung jarak (Tetap Float/Double)
            target = CENTRAL_COORDS.get(clean_region, {"lat": -6.2, "lng": 106.8})
            final_dist = request.jarak_ke_bca
            if request.latitude != 0 and request.longitude != 0:
                final_dist = calculate_haversine(request.latitude, request.longitude, target["lat"], target["lng"])
            
            # B. Hitung amenities_count (PASTIKAN JADI INT)
            amenities_count = int(request.is_furnished + request.is_water_heater + request.is_km_dalam + 
                               request.is_listrik_free + request.is_mesin_cuci + request.is_parkir_mobil)
            
            # C. Siapkan DataFrame dengan tipe data yang SANGAT KETAT
            input_dict = {
                "luas_kamar": float(request.luas_kamar),
                "jarak_ke_bca": float(final_dist),
                "tipe_kos": str(request.tipe_kos.value if hasattr(request.tipe_kos, 'value') else request.tipe_kos),
                "is_km_dalam": int(request.is_km_dalam),      # Ubah ke int
                "is_water_heater": int(request.is_water_heater), # Ubah ke int
                "is_furnished": int(request.is_furnished),       # Ubah ke int
                "is_listrik_free": int(request.is_listrik_free), # Ubah ke int
                "is_parkir_mobil": int(request.is_parkir_mobil), # Ubah ke int
                "is_mesin_cuci": int(request.is_mesin_cuci),     # Ubah ke int
                "amenities_count": int(amenities_count)          # WAJIB INT
            }
            
            df = pd.DataFrame([input_dict])
            
            # D. SMART FEATURE ALIGNMENT & TYPE CASTING
            required_features = None
            if hasattr(model, "metadata") and model.metadata and getattr(model.metadata, "signature", None):
                required_features = [inp.name for inp in model.metadata.signature.inputs]
            elif hasattr(model, "feature_names_in_"):
                required_features = list(model.feature_names_in_)

            if required_features:
                df = df.reindex(columns=required_features, fill_value=0)
                
                # Tambahan: Paksa kolom amenities_count jadi int32 jika ada
                if "amenities_count" in df.columns:
                    df["amenities_count"] = df["amenities_count"].astype("int32")
                
                # Paksa kolom is_xxx jadi int64 (long) agar sesuai schema MLflow
                for col in df.columns:
                    if col.startswith("is_"):
                        df[col] = df[col].astype("int64")

            # E. Eksekusi Prediksi
            prediction = float(model.predict(df)[0])
            
            # F. LOGIKA iDorm: Analisis Harga Wajar (Batas Atas & Bawah)
            mae = MAE_CONFIG.get(clean_region, 300000)
            low = max(0, prediction - mae) # Batas bawah tidak boleh minus
            high = prediction + mae
            offered = request.harga_tawaran
            
            verdict, color, desc = "Harga Wajar", "primary", "Harga sesuai dengan standar fasilitas dan lokasi."
            if offered < low:
                verdict, color, desc = "Good Deal", "success", "Harga di bawah rata-rata pasar wilayah ini."
            elif offered > high:
                verdict, color, desc = "Overpriced", "danger", "Harga di atas batas wajar fasilitas tersebut."

            # Update Metrics
            prediction_store[clean_region].append(prediction)
            LATEST_PREDICTION.labels(region=clean_region).set(prediction)
            latency_store[clean_region].append((time.time() - start_time) * 1000)

        logger.info("inference_event", extra={"region": clean_region, "predicted_price": prediction})

        # G. Return Full Response (Sesuai Schema iDorm)
        return PredictionResponse(
            region=clean_region,
            predicted_price=round(prediction, 2),
            model_version=version,
            metadata={"mae_margin": mae, "calculated_distance": round(final_dist, 2)},
            fair_range={"min": round(low, 2), "max": round(high, 2)},
            analysis={"verdict": verdict, "color_code": color, "description": desc}
        )

    except Exception as e:
        error_counter += 1
        ERROR_COUNT.labels(region=clean_region).inc()
        logger.error("inference_error", extra={"region": clean_region, "error": str(e)})
        raise HTTPException(status_code=500, detail=str(e))

# --- Endpoint Monitoring (Tetap Ada) ---
@router.get("/health")
def health_check():
    return {"status": "healthy", "models_loaded": list(model_loader.models.keys())}

@router.get("/prediction-monitor/{region}")
def prediction_monitor(region: str):
    preds = prediction_store.get(region, [])
    if not preds: return {"message": "Belum ada data."}
    return {
        "region": region,
        "mean_prediction": round(statistics.mean(preds), 2),
        "max": round(max(preds), 2),
        "min": round(min(preds), 2),
    }