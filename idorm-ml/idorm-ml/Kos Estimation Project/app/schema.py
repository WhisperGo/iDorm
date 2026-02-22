from pydantic import BaseModel, Field
from typing import Literal, Optional, Dict, Any
from enum import Enum

# 1. Definisi Tipe Kos
class TipeKos(str, Enum):
    putra = "putra"
    putri = "putri"
    campur = "campur"

# 2. Schema Request (Data Masuk dari Laravel)
class KosRequest(BaseModel):
    luas_kamar: float = Field(..., gt=0, lt=100)
    # Dibuat Optional agar kalau Maps belum pilih lokasi, API tidak langsung mati
    jarak_ke_bca: Optional[float] = Field(0.0, ge=0, lt=50)

    tipe_kos: TipeKos

    # Kita pakai Literal[0, 1] karena Model ML biasanya ditraining pakai angka 0/1
    is_km_dalam: Literal[0, 1]
    is_water_heater: Literal[0, 1]
    is_furnished: Literal[0, 1]
    is_listrik_free: Literal[0, 1]
    is_parkir_mobil: Literal[0, 1]
    is_mesin_cuci: Literal[0, 1]
    
    # Tambahan Penting: Supaya data dari Laravel (Maps & Input Harga) diterima
    latitude: Optional[float] = 0.0
    longitude: Optional[float] = 0.0
    harga_tawaran: Optional[float] = 0.0

# 3. Schema Response (Data Keluar balik ke Laravel)
class PredictionResponse(BaseModel):
    region: str
    predicted_price: float
    model_version: str
    
    # Tambahan Penting: Tanpa ini, Laravel kamu akan dapet data 'null' untuk kartu analisis
    metadata: Optional[Dict[str, Any]] = None
    fair_range: Optional[Dict[str, float]] = None
    analysis: Optional[Dict[str, str]] = None