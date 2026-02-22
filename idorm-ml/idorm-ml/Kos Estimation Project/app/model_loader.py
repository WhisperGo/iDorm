import mlflow.pyfunc
import threading
import logging
import json
import os
from pathlib import Path

logger = logging.getLogger("inference")

class ModelProvider:
    _instance = None
    _lock = threading.Lock()

    def __new__(cls, *args, **kwargs):
        with cls._lock:
            if cls._instance is None:
                cls._instance = super(ModelProvider, cls).__new__(cls)
                cls._instance._initialized = False
        return cls._instance

    def __init__(self):
        if self._initialized:
            return
        self.models = {}
        self.models_lock = threading.Lock()
        self._initialized = True

    def load_models(self):
        # 1. Mapping Run ID hasil training (Sesuai investigasi kita sebelumnya)
        region_to_run = {
            "jakarta_pusat": "44146418c5c94c5f9cc3d493897a4956",
            "jakarta_selatan": "d11c75fa6e704c93a52ae0663fccbe84",
            "jakarta_utara": "480833815cc34ddbb607a077904c6336",
            "yogyakarta": "1e874a83c4f54e2bb58f60f019e8ba76"
        }
        
        base_dir = Path(__file__).resolve().parent.parent
        
        with self.models_lock:
            for region, run_id in region_to_run.items():
                # Jalur Artifacts: notebooks -> mlruns -> 0 -> models -> m-[id] -> artifacts
                model_path = base_dir / "notebooks" / "mlruns" / "0" / "models" / f"m-{run_id}" / "artifacts"

                try:
                    if not model_path.exists():
                        print(f"⚠️ [{region}] FOLDER TIDAK DITEMUKAN: {model_path}")
                        continue

                    # Load model menggunakan MLflow
                    model_uri = str(model_path.absolute())
                    model = mlflow.pyfunc.load_model(model_uri=model_uri)

                    # KONSISTENSI CHECK (Mengambil 'Old Loader' logic)
                    # Kita ambil metadata dari MLflow model object
                    mlflow_metadata = getattr(model, "metadata", None)
                    
                    self.models[region] = {
                        "model": model,
                        "version": "local_final_v2",
                        "run_id": run_id,
                        "metadata": mlflow_metadata
                    }
                    
                    # Update metrics jika ada
                    try:
                        from app.prometheus_metrics import MODEL_LOAD_STATUS
                        MODEL_LOAD_STATUS.labels(region=region).set(1)
                    except ImportError:
                        pass

                    print(f"✅ [{region}] Model loaded & validated (Run: {run_id[:8]})")

                except Exception as e:
                    logger.error("model_load_error", extra={"region": region, "error": str(e)})
                    print(f"❌ [{region}] GAGAL LOAD: {e}")

    def get_model(self, region: str):
        """Ambil data model lengkap (dict)"""
        with self.models_lock:
            return self.models.get(region)

    def get_metadata(self, region: str):
        """Ambil metadata saja (untuk kompatibilitas router lama)"""
        with self.models_lock:
            region_data = self.models.get(region)
            if region_data and region_data["metadata"]:
                # Mengembalikan signature model dalam bentuk dict jika tersedia
                return region_data["metadata"].to_dict() if hasattr(region_data["metadata"], "to_dict") else {}
            return {}

    def get_model_info(self, region: str):
        """Ambil info versi dan metadata untuk API endpoint"""
        with self.models_lock:
            info = self.models.get(region)
            if info:
                return {
                    "version": info["version"],
                    "run_id": info["run_id"],
                    "metadata": info["metadata"].to_dict() if hasattr(info["metadata"], "to_dict") else {}
                }
            return None

# Inisialisasi Singleton
model_loader = ModelProvider()