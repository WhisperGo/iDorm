import mlflow.pyfunc
from mlflow.tracking import MlflowClient
import threading
import logging
import os

logger = logging.getLogger("inference")

class ModelProvider:
    _instance = None
    _lock = threading.Lock()

    def __new__(cls):
        with cls._lock:
            if cls._instance is None:
                cls._instance = super(ModelProvider, cls).__new__(cls)
                cls._instance._initialized = False
        return cls._instance

    def __init__(self):
        if self._initialized: return
        self.models = {}  # Struktur: {"region": {"model": obj, "version": "1", "run_id": "abc"}}
        self.models_lock = threading.Lock()
        
        self.tracking_uri = os.getenv("MLFLOW_TRACKING_URI", "http://mlflow:5000")
        mlflow.set_tracking_uri(self.tracking_uri)
        
        # Inisialisasi client untuk mengambil metadata
        try:
            self.client = MlflowClient()
        except Exception as e:
            logger.error(f"Gagal inisialisasi MlflowClient: {e}")
            self.client = None
            
        self._initialized = True

    def load_production_models(self, regions: list, max_retries: int = 5, retry_delay: int = 3):
        if not self.client:
            logger.error("MlflowClient tidak tersedia. Gagal memuat model.")
            return

        for region in regions:
            loaded = False
            for attempt in range(1, max_retries + 1):
                try:
                    # 1. Ambil info versi dari @production alias
                    v_info = self.client.get_model_version_by_alias(region, "production")
                    actual_version = v_info.version

                    # 2. Muat model fisiknya via alias URI
                    model_uri = f"models:/{region}@production"
                    model = mlflow.pyfunc.load_model(model_uri)

                    with self.models_lock:
                        self.models[region] = {
                            "model": model,
                            "version": actual_version,
                            "run_id": v_info.run_id,
                            "status": "ready"
                        }

                    logger.info(f"Berhasil memuat model {region} [Version: {actual_version}]")
                    loaded = True
                    break
                except Exception as e:
                    logger.warning(f"Attempt {attempt}/{max_retries} gagal memuat model {region}: {str(e)}")
                    if attempt < max_retries:
                        import time
                        time.sleep(retry_delay)

            if not loaded:
                logger.error(f"Gagal memuat model {region} setelah {max_retries} percobaan.")

    def get_model_data(self, region: str):
        """Mengambil dictionary lengkap berisi model dan metadata"""
        with self.models_lock:
            return self.models.get(region)

    def get_model(self, region: str):
        """Helper untuk mengambil objek model saja (untuk backward compatibility)"""
        data = self.get_model_data(region)
        return data["model"] if data else None

model_loader = ModelProvider()