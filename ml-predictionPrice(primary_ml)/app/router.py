from fastapi import APIRouter, HTTPException, Request
from app.schema import KosRequest, PredictionResponse
from app.model_loader import model_loader
import pandas as pd
import time
import logging
import statistics
from collections import defaultdict, deque

from app.metrics import (
    REQUEST_COUNT,
    ERROR_COUNT,
    REQUEST_LATENCY,
    LATEST_PREDICTION,
    PREDICTION_VALUE_SUMMARY
)

prediction_store = defaultdict(lambda: deque(maxlen=1000))
latency_store = defaultdict(lambda: deque(maxlen=1000))
anomaly_store = defaultdict(lambda: deque(maxlen=1000))

error_counter = 0
logger = logging.getLogger("inference")
router = APIRouter()

@router.post("/predict/{region}", response_model=PredictionResponse)
def predict(region: str, request: KosRequest, http_request: Request):
    global error_counter
    start_time = time.perf_counter()

    model_data = model_loader.get_model_data(region)
    if not model_data:
        raise HTTPException(status_code=404, detail=f"Model untuk region {region} belum dimuat")

    model = model_data["model"]
    version = model_data["version"]
    request_id = getattr(http_request.state, "request_id", "unknown-id")
    
    REQUEST_COUNT.labels(region=region).inc()

    try:
        with REQUEST_LATENCY.labels(region=region).time():
            data = request.model_dump()

            # Feature Engineering: amenities count
            data["amenities_count"] = sum([
                data.get("is_furnished", 0),
                data.get("is_water_heater", 0),
                data.get("is_km_dalam", 0),
                data.get("is_listrik_free", 0),
                data.get("is_mesin_cuci", 0),
                data.get("is_parkir_mobil", 0)
            ])

            df = pd.DataFrame([data])
            
            # Smart Feature Alignment
            required_features = None
            if hasattr(model, "metadata") and model.metadata and getattr(model.metadata, "signature", None):
                required_features = [inp.name for inp in model.metadata.signature.inputs]
            elif hasattr(model, "feature_names_in_"):
                required_features = list(model.feature_names_in_)
                
            if required_features:
                for f in required_features:
                    if f not in df.columns:
                        df[f] = 0.0
                df = df[required_features]

            if "amenities_count" in df.columns:
                df["amenities_count"] = df["amenities_count"].astype("int32")

            prediction = model.predict(df)[0]
            pred_val = float(prediction)

        latency_sec = time.perf_counter() - start_time
        latency_ms = latency_sec * 1000
        
        prediction_store[region].append(pred_val)
        latency_store[region].append(latency_ms)
        
        LATEST_PREDICTION.labels(region=region).set(pred_val)
        PREDICTION_VALUE_SUMMARY.labels(region=region).observe(pred_val)

        logger.info(
            f"Inference success for {region}",
            extra={
                "event": "inference_event",
                "region": region,
                "model_version": version,
                "request_id": request_id,
                "output": pred_val,
                "latency_sec": round(latency_sec, 4) 
            }
        )

        return PredictionResponse(
            region=region,
            predicted_price=pred_val,
            model_version=version
        )

    except Exception as e:
        error_counter += 1
        ERROR_COUNT.labels(region=region).inc()
        latency_sec = time.perf_counter() - start_time
        
        logger.error(
            f"Inference failed for {region}: {str(e)}",
            extra={
                "event": "inference_error",
                "region": region,
                "error": str(e),
                "request_id": request_id,
                "latency_sec": round(latency_sec, 4)
            }
        )
        raise HTTPException(status_code=500, detail="Prediction failed")

@router.get("/healthy")
async def health_check():
    # Mengambil key dari models dictionary yang sudah diisi di startup
    loaded_models = list(model_loader.models.keys())
    if not loaded_models:
        return {
            "status": "partial", 
            "message": "API Running, but NO models loaded.",
            "loaded_regions": []
        }
    return {"status": "healthy", "loaded_regions": loaded_models}

@router.get("/model-info/{region}")
def model_info(region: str):
    data = model_loader.get_model_data(region)
    if not data:
        raise HTTPException(status_code=404, detail="Region not found")
    return {
        "region": region,
        "version": data["version"],
        "run_id": data["run_id"]
    }

@router.get("/internal-metrics")
def internal_metrics():
    result = {}
    for region, latencies in latency_store.items():
        if not latencies: continue
        sorted_lat = sorted(latencies)
        result[region] = {
            "count": len(latencies),
            "mean_ms": round(statistics.mean(latencies), 4),
            "p50_ms": round(sorted_lat[int(0.50 * len(sorted_lat))], 4),
            "p90_ms": round(sorted_lat[int(0.90 * len(sorted_lat))], 4),
            "p95_ms": round(sorted_lat[int(0.95 * len(sorted_lat))], 4),
            "max_ms": round(max(latencies), 4),
        }
    return {"regions": result, "total_errors": error_counter}