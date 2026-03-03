"""
Retrain all 4 regional models with the EXACT parameters chosen in the notebooks,
then register them in MLflow with the @production alias.

This script is designed to be run against the Docker MySQL-backed MLflow server
via:  MLFLOW_TRACKING_URI=http://localhost:5000 python scripts/retrain_all.py

Model configurations (from notebooks):
  - jakarta_pusat:   RandomForest (n_est=250, depth=5, leaf=4, feat=sqrt)
  - jakarta_selatan: RandomForest (n_est=250, depth=5, leaf=4, feat=sqrt)
  - jakarta_utara:   LinearRegression (fit_intercept=True, preprocessor_linear)
  - yogyakarta:      RandomForest (n_est=200, depth=5, leaf=4, feat=sqrt)
"""

import sys
import os
import numpy as np
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import RobustScaler, OneHotEncoder
from sklearn.pipeline import Pipeline
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.linear_model import LinearRegression, Ridge
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score, mean_squared_error

PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, os.path.join(PROJECT_ROOT, "src", "training"))
from utils import train_and_register

RANDOM_SEED = 42
np.random.seed(RANDOM_SEED)

# ─── Notebook reference metrics (for sanity check) ────────────────────────────
NOTEBOOK_REFERENCE = {
    "jakarta_pusat":   {"R2": 0.7112, "MAE": 538324, "MAPE": 24.50},
    "jakarta_selatan": {"R2": 0.6247, "MAE": 500346, "MAPE": 30.12},
    "jakarta_utara":   {"R2": 0.6914, "MAE": 256533, "MAPE": 18.35},
    "yogyakarta":      {"R2": 0.7180, "MAE": 255535, "MAPE": 29.00},
}

# ─── Region configurations ────────────────────────────────────────────────────
REGION_CONFIGS = {
    "jakarta_pusat": {
        "model_type": "GradientBoosting",
        "params": {
            "n_estimators": 100,
            "max_depth": 2,
            "min_samples_leaf": 4,
            "learning_rate": 0.05,
            "subsample": 0.7
        },
    },
    "jakarta_selatan": {
        "model_type": "RandomForest",
        "params": {
            "n_estimators": 250,
            "max_depth": 10,
            "min_samples_leaf": 4,
            "max_features": "sqrt",
        },
    },
    "jakarta_utara": {
        "model_type": "RandomForest",
        "params": {
            "n_estimators": 200,
            "max_depth": 10,
            "min_samples_leaf": 4,
            "max_features": "sqrt",
        },
    },
    "yogyakarta": {
        "model_type": "Ridge",
        "params": {
            "alpha": 5.0,
        },
    },
}


def load_and_prepare(region: str):
    """Load CSV, clean data, engineer features — mirrors notebook pipeline."""
    csv_path = os.path.join(PROJECT_ROOT, "datasets", f"{region}.csv")
    df = pd.read_csv(csv_path)
    print(f"\n{'='*60}")
    print(f"  REGION: {region}")
    print(f"  Raw shape: {df.shape}")
    print(f"{'='*60}")

    # Drop zero-variance columns
    cols_to_drop = [c for c in df.columns if df[c].nunique() <= 1]
    df = df.drop(columns=cols_to_drop)
    if cols_to_drop:
        print(f"  Dropped zero-variance: {cols_to_drop}")

    # 99th percentile capping on price
    cap = df["harga"].quantile(0.99)
    df = df[df["harga"] <= cap].copy()
    print(f"  After capping (99th={cap:,.0f}): {len(df)} rows")

    # Feature engineering: amenities_count
    df["amenities_count"] = (
        df["is_furnished"].astype(int)
        + df["is_water_heater"].astype(int)
        + df["is_km_dalam"].astype(int)
        + df["is_listrik_free"].astype(int)
        + df["is_mesin_cuci"].astype(int)
        + df["is_parkir_mobil"].astype(int)
    )
    df["luas_kamar"] = df["luas_kamar"].clip(lower=1)

    # Price segments for stratified split
    df["price_segment"] = pd.cut(
        df["harga"],
        bins=[0, 1_500_000, 3_500_000, 6_000_000, 100_000_000],
        labels=["Budget", "Standard", "Upper-Standard", "Premium"],
        duplicates="drop",
    )

    # Features & target
    X = df.drop(columns=["nama_kos", "harga", "price_segment"])
    y = df["harga"].copy()

    print(f"  Features: {list(X.columns)}")
    print(f"  Samples: {len(X)}")

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=RANDOM_SEED, stratify=df["price_segment"]
    )
    print(f"  Split seed: {RANDOM_SEED}")
    print(f"  Train: {len(X_train)}, Test: {len(X_test)}")

    return X_train, X_test, y_train, y_test


def build_preprocessor_tree(continuous_cols):
    """Build ColumnTransformer for RF / tree-based models (includes amenities_count)."""
    binary_cols = [
        "is_furnished", "is_water_heater", "is_km_dalam",
        "is_listrik_free", "is_mesin_cuci", "is_parkir_mobil",
    ]
    categorical_cols = ["tipe_kos"]

    return ColumnTransformer(
        [
            ("continuous", RobustScaler(), continuous_cols),
            ("binary", "passthrough", binary_cols),
            ("categorical", OneHotEncoder(handle_unknown="ignore", sparse_output=False), categorical_cols),
        ],
        remainder="drop",
        verbose=0,
    )


def build_preprocessor_linear():
    """Build ColumnTransformer for Linear/Ridge Regression (yogyakarta).
    
    Key differences from tree preprocessor:
      - continuous features: only ['luas_kamar'] (NO amenities_count)
      - OneHotEncoder uses drop='first' to avoid dummy variable trap
    """
    continuous_cols = ["luas_kamar"]
    binary_cols = [
        "is_water_heater", "is_km_dalam", "is_listrik_free",
        "is_mesin_cuci", "is_parkir_mobil", "is_furnished",
    ]
    categorical_cols = ["tipe_kos"]

    return ColumnTransformer(
        [
            ("continuous", RobustScaler(), continuous_cols),
            ("binary", "passthrough", binary_cols),
            ("categorical", OneHotEncoder(
                handle_unknown="ignore",
                sparse_output=False,
                categories=[["putra", "putri", "campur"]],
                drop="first",
            ), categorical_cols),
        ],
        remainder="drop",
        verbose=0,
    )


def train_region(region, X_train, X_test, y_train, y_test):
    """Train a model for a region using the EXACT notebook parameters."""
    config = REGION_CONFIGS[region]
    model_type = config["model_type"]
    model_params = config["params"]

    if model_type == "RandomForest":
        continuous_cols = ["jarak_ke_bca", "luas_kamar", "amenities_count"]
        preprocessor = build_preprocessor_tree(continuous_cols)
        pipeline = Pipeline([
            ("preprocessor", preprocessor),
            ("regressor", RandomForestRegressor(
                random_state=RANDOM_SEED,
                n_jobs=-1,
                **model_params,
            )),
        ])
    elif model_type == "GradientBoosting":
        continuous_cols = ["jarak_ke_bca", "luas_kamar", "amenities_count"]
        preprocessor = build_preprocessor_tree(continuous_cols)
        pipeline = Pipeline([
            ("preprocessor", preprocessor),
            ("regressor", GradientBoostingRegressor(
                random_state=RANDOM_SEED,
                **model_params,
            )),
        ])
    elif model_type == "LinearRegression":
        preprocessor = build_preprocessor_linear()
        pipeline = Pipeline([
            ("preprocessor", preprocessor),
            ("regressor", LinearRegression(**model_params)),
        ])
    elif model_type == "Ridge":
        preprocessor = build_preprocessor_linear()
        pipeline = Pipeline([
            ("preprocessor", preprocessor),
            ("regressor", Ridge(**model_params)),
        ])
    else:
        raise ValueError(f"Unknown model_type: {model_type}")

    # Fit with exact parameters (no search)
    print(f"\n  Training {model_type} with params: {model_params}")
    pipeline.fit(X_train, y_train)

    # Evaluate
    y_pred = pipeline.predict(X_test)
    mae = mean_absolute_error(y_test, y_pred)
    r2 = r2_score(y_test, y_pred)
    rmse = float(np.sqrt(mean_squared_error(y_test, y_pred)))
    mape = float(np.mean(np.abs((y_test - y_pred) / y_test)) * 100)

    print(f"  R²:   {r2:.4f}")
    print(f"  MAE:  Rp {mae:,.0f}")
    print(f"  RMSE: Rp {rmse:,.0f}")
    print(f"  MAPE: {mape:.2f}%")

    # Sanity check against notebook reference
    ref = NOTEBOOK_REFERENCE[region]
    r2_diff = abs(r2 - ref["R2"])
    mae_diff = abs(mae - ref["MAE"])
    if r2_diff > 0.05 or mae_diff > ref["MAE"] * 0.10:
        print(f"  ⚠ WARNING: Metrics differ from notebook! (R² diff={r2_diff:.4f}, MAE diff={mae_diff:,.0f})")
    else:
        print(f"  ✓ Metrics match notebook reference (R²≈{ref['R2']}, MAE≈Rp{ref['MAE']:,.0f})")

    # Register in MLflow
    params_log = {"model_type": model_type}
    params_log.update({str(k): str(v) for k, v in model_params.items()})

    metrics_log = {
        "MAE": mae,
        "R2": r2,
        "RMSE": rmse,
        "MAPE": mape,
    }

    model_uri = train_and_register(
        region=region,
        model=pipeline,
        X_train=X_train,
        y_train=y_train,
        X_test=X_test,
        y_test=y_test,
        params=params_log,
        metrics=metrics_log,
    )

    print(f"  Registered at: {model_uri}")
    return model_uri


def main():
    import argparse
    parser = argparse.ArgumentParser(description="Retrain idorm ML models")
    parser.add_argument("--region", type=str, help="Specify a region to train (e.g. yogyakarta). If not specified, trains all regions.")
    args = parser.parse_args()

    # Determine which regions to train
    if args.region:
        regions = [args.region]
        print(f"\nTARGETED RETRAINING INITIATED FOR: {args.region.upper()}")
    else:
        regions = ["jakarta_pusat", "jakarta_selatan", "jakarta_utara", "yogyakarta"]
        print(f"\nFULL SYSTEM RETRAINING INITIATED FOR {len(regions)} REGIONS")

    results = {}

    for region in regions:
        X_train, X_test, y_train, y_test = load_and_prepare(region)
        uri = train_region(region, X_train, X_test, y_train, y_test)
        results[region] = uri

    print(f"\n{'='*60}")
    print("  ALL REQUESTED MODELS RETRAINED SUCCESSFULLY")
    print(f"{'='*60}")
    for region, uri in results.items():
        print(f"  {region}: {uri}")


if __name__ == "__main__":
    main()
