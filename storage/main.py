import joblib
import os
import uvicorn
import math
import pandas as pd 
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import Optional

app = FastAPI(title="iDorm Price Prediction Service")

# --- 1. CONFIG & COORDINATES ---
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
    "yogyakarta": 235182,
    "default": 300000
}

# --- 2. FUNGSI HAVERSINE ---
def calculate_haversine(lat1, lon1, lat2, lon2):
    R = 6371.0
    dlat = math.radians(lat2 - lat1)
    dlon = math.radians(lon2 - lon1)
    a = math.sin(dlat / 2)**2 + \
        math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * \
        math.sin(dlon / 2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return R * c

# --- 3. SCHEMAS ---
class PriceRequest(BaseModel):
    region: str
    tipe_kos: str
    harga_tawaran: float 
    luas_kamar: float     
    latitude: Optional[float] = 0.0
    longitude: Optional[float] = 0.0
    jarak_ke_bca: Optional[float] = 0.0 
    is_km_dalam: int = 0
    is_water_heater: int = 0
    is_furnished: int = 0
    is_listrik_free: int = 0
    is_parkir_mobil: int = 0
    is_mesin_cuci: int = 0

# --- 4. MODEL LOADING ---
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
ML_BASE_PATH = os.path.join(BASE_DIR, "idorm-ml", "Kos Estimation Project", "models")
kos_models = {}

@app.on_event("startup")
def load_models():
    regions = ["jakarta_pusat", "jakarta_selatan", "jakarta_utara", "yogyakarta"]
    for reg in regions:
        try:
            model_path = os.path.join(ML_BASE_PATH, reg, "v1.0.0", "model.pkl")
            if os.path.exists(model_path):
                kos_models[reg] = joblib.load(model_path)
                print(f"✅ AKTIF: Model {reg}")
        except Exception as e:
            print(f"⚠️ Error load {reg}: {str(e)}")

# --- 5. ENDPOINT PREDIKSI ---
@app.post("/predict-price")
async def predict_price(request: PriceRequest):
    region = request.region.lower().replace(" ", "_")
    
    if region not in kos_models:
        raise HTTPException(status_code=400, detail=f"Model {region} tidak ditemukan.")
    
    try:
        model = kos_models[region]
        mae = MAE_CONFIG.get(region, 300000)
        target_coords = CENTRAL_COORDS.get(region, {"lat": -6.2000, "lng": 106.8166})

        # 1. Hitung Jarak
        final_dist = request.jarak_ke_bca
        if request.latitude != 0.0 and request.longitude != 0.0:
            final_dist = calculate_haversine(
                request.latitude, request.longitude,
                target_coords["lat"], target_coords["lng"]
            )

        # --- TAMBAHAN BARU: HITUNG AMENITIES COUNT ---
        # Menghitung jumlah fasilitas yang bernilai 1
        facilities_list = [
            request.is_km_dalam, request.is_water_heater, request.is_furnished,
            request.is_listrik_free, request.is_parkir_mobil, request.is_mesin_cuci
        ]
        amenities_count = sum(facilities_list)
        # ----------------------------------------------

        # 2. SIAPKAN DATA
        input_dict = {
            "luas_kamar": float(request.luas_kamar),
            "jarak_ke_bca": float(final_dist),
            "tipe_kos": str(request.tipe_kos.lower()),
            "is_km_dalam": float(request.is_km_dalam),
            "is_water_heater": float(request.is_water_heater),
            "is_furnished": float(request.is_furnished),
            "is_listrik_free": float(request.is_listrik_free),
            "is_parkir_mobil": float(request.is_parkir_mobil),
            "is_mesin_cuci": float(request.is_mesin_cuci),
            "amenities_count": float(amenities_count) # <--- MASUKKAN KE DICT
        }

        # 3. BUAT DATAFRAME
        df_input = pd.DataFrame([input_dict])
        
        # --- LOGIKA KHUSUS JAKARTA UTARA ---
        # Karena model Jakut cuma minta 9 fitur, kita hapus amenities_count khusus Jakut
        if region == "jakarta_utara":
            df_input = df_input.drop(columns=["amenities_count"])
            print(f"⚠️ Kolom amenities_count dihapus khusus untuk {region}")
        # -----------------------------------

        # Cukup pastikan kolom angka adalah angka
        numeric_cols = [col for col in df_input.columns if col != "tipe_kos"]
        for col in numeric_cols:
            df_input[col] = pd.to_numeric(df_input[col])

        # 4. PREDIKSI
        print(f"📊 Mencoba prediksi untuk {region} (Amenities: {amenities_count})")
        prediction = model.predict(df_input)[0]
        
        # --- LOGIKA VERDICT ---
        offered = request.harga_tawaran
        lower_bound = max(0, prediction - mae)
        upper_bound = prediction + mae
        
        verdict, color, desc = "Harga Wajar", "primary", "Harga sesuai standar pasar."
        if offered < lower_bound:
            verdict, color, desc = "Good Deal", "success", "Harga di bawah rata-rata pasar."
        elif offered > upper_bound:
            verdict, color, desc = "Overpriced", "danger", "Harga di atas batas wajar."

        return {
            "status": "success",
            "metadata": {
                "region": region,
                "mae_margin": mae, 
                "calculated_distance": round(final_dist, 2)},
            "result": {
                "base_prediction": round(float(prediction), 2),
                "fair_range": {
                    "min": round(float(lower_bound), 2),
                    "max": round(float(upper_bound), 2)
                    },
                "offered_price": offered,
                "analysis": {
                    "verdict": verdict,
                    "color_code": color, 
                    "description": desc}
            }
        }

    except Exception as e:
        print("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!")
        print(f"CRASH DI PYTHON: {str(e)}")
        import traceback
        traceback.print_exc() 
        print("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8002)