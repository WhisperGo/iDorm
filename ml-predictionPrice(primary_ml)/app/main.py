from fastapi import FastAPI
from contextlib import asynccontextmanager
from app.router import router
from app.model_loader import model_loader
from app.prometheus_metrics import metrics_router
import logging

@asynccontextmanager
async def lifespan(app: FastAPI):
    logging.info("Memulai pemuatan model dari MLflow...")
    regions = ["jakarta_pusat", "jakarta_selatan", "jakarta_utara", "yogyakarta"]
    model_loader.load_production_models(regions)
    yield
    logging.info("Mematikan aplikasi...")

app = FastAPI(title="iDorm Price Prediction API", lifespan=lifespan)

app.include_router(router)
app.include_router(metrics_router)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)