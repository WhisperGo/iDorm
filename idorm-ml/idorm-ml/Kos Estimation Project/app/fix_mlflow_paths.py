import sqlite3
import os
from pathlib import Path

# 1. OTOMATIS CARI LOKASI DATABASE
# Mencari file mlflow.db di folder 'notebooks' yang sejajar dengan folder 'app'
base_path = Path(__file__).resolve().parent.parent # Naik satu tingkat dari folder 'app'
db_path = base_path / "notebooks" / "mlflow.db"

if not db_path.exists():
    # Coba cari di folder saat ini kalau di atas gak ketemu
    db_path = Path(__file__).resolve().parent / "notebooks" / "mlflow.db"

if not db_path.exists():
    print(f"❌ File database tetap tidak ditemukan di: {db_path}")
    print("Pastikan script ini ditaruh di dalam folder 'app' atau di root project.")
    exit()

# 2. AMBIL LOKASI FOLDER PROJECT SEKARANG
# Kita ambil path sampai folder 'notebooks'
current_project_root = db_path.parent.as_posix()
# Artifact root adalah folder mlruns yang ada di dalam notebooks
new_artifact_root = f"file:///{current_project_root}/mlruns"

conn = sqlite3.connect(str(db_path))
cursor = conn.cursor()

print(f"🔍 Database ditemukan di: {db_path}")
print(f"🔄 Mengupdate database ke arah laptop Yusuf: {new_artifact_root}")

try:
    # Update tabel experiments
    cursor.execute("UPDATE experiments SET artifact_location = ?", (new_artifact_root,))
    
    # Update tabel runs (sesuaikan URI-nya)
    # Kita arahkan ke folder notebooks (tempat folder mlruns berada)
    cursor.execute("""
        UPDATE runs 
        SET artifact_uri = REPLACE(artifact_uri, SUBSTR(artifact_uri, 1, INSTR(artifact_uri, '/mlruns') - 1), ?)
        WHERE artifact_uri LIKE '%/mlruns%'
    """, (f"file:///{current_project_root}",))

    # Update tabel model_versions
    cursor.execute("""
        UPDATE model_versions 
        SET source = REPLACE(source, SUBSTR(source, 1, INSTR(source, '/mlruns') - 1), ?)
        WHERE source LIKE '%/mlruns%'
    """, (f"file:///{current_project_root}",))

    conn.commit()
    print("✅ BERHASIL! Database sudah mengenali folder laptop kamu.")
    print("Sekarang silakan jalankan kembali: uvicorn app.main:app")
except Exception as e:
    print(f"❌ Gagal update database: {e}")
finally:
    conn.close()