"""
[DEPRECATED] - This script is no longer needed.

Model migration is now handled by scripts/retrain_all.py, which trains
models with the exact notebook parameters and registers them directly
into the Docker MySQL-backed MLflow server.

To retrain and register all models, run:
  set MLFLOW_TRACKING_URI=http://localhost:5000
  python scripts/retrain_all.py

Or simply run gas.bat which does everything automatically.
"""

print("This script is deprecated. Use scripts/retrain_all.py instead.")
print("Run: gas.bat (or set MLFLOW_TRACKING_URI=http://localhost:5000 && python scripts/retrain_all.py)")