import sys
import os
from fastapi import FastAPI, Response
from fastapi.middleware.cors import CORSMiddleware

# --- STEP 1: DAFTARKAN ALAMAT DULU (WAJIB DI ATAS) ---
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# Pastikan nama folder ini SAMA PERSIS dengan yang ada di Windows (Kos_Estimation_Project)
PROJECT_PATH = os.path.join(BASE_DIR, "idorm-ml", "Kos_Estimation_Project")

if PROJECT_PATH not in sys.path:
    sys.path.append(PROJECT_PATH)

# --- STEP 2: BARU BOLEH IMPORT MODULNYA ---
try:
    # Python sekarang sudah tahu jalan menuju folder Kos_Estimation_Project/app
    from app.router import router as predict_router
    from app.router_chatbot import router as chat_router
    from app.model_loader import model_loader
    from app.logging_config import setup_logging
    from prometheus_client import generate_latest, CONTENT_TYPE_LATEST
    print("✅ Berhasil memuat semua modul AI iDorm!")
except ImportError as e:
    print(f"❌ GAGAL IMPORT: {e}")
    print(f"Python mencari di: {PROJECT_PATH}")
    raise e

# --- STEP 3: INISIALISASI ---
setup_logging()
app = FastAPI(title="iDorm Unified AI Server")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(predict_router, prefix="/predict", tags=["Prediction"])
app.include_router(chat_router, prefix="/chat", tags=["Chatbot"])

@app.on_event("startup")
def startup_event():
    model_loader.load_models()
    print("🚀 Semua Model AI (Prediksi & Chatbot) Berhasil Dimuat!")

@app.get("/")
def index():
    return {"status": "online", "msg": "iDorm Unified Server running from Storage"}

@app.get("/metrics")
def metrics():
    return Response(content=generate_latest(), media_type=CONTENT_TYPE_LATEST)