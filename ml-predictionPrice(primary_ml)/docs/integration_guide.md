# idorm-ml Laravel Integration Guide

This guide is designed for the hybrid-cloud setup connecting Laravel to FastAPI (Internal Server). It outlines how to consume the model inference safely across different regions regardless of their exact feature requirements.

## Universal JSON Payload

A common issue in deploying regional Machine Learning models is dealing with "Feature Mismatches." For example, the model for Jakarta Selatan might require the distances to UGM and ITB, whereas Jakarta Pusat might not.

Our FastAPI endpoint is now **Backend-Agnostic**.
Laravel should **send everything it knows about a Kos**. The API will automatically unpack and filter out unnecessary features without blocking the request. It safely zeroes any missing required features as defaults.

### Endpoint
**`POST {FASTAPI_URL}/predict/{region}`**
Available regions: `jakarta_pusat`, `jakarta_selatan`, `jakarta_utara`, `yogyakarta`.

### Example Request Body (Send Everything!)
```json
{
  "luas_kamar": 15.0,
  "jarak_ke_bca": 2.5,
  "tipe_kos": "campur",
  "is_km_dalam": 1,
  "is_water_heater": 0,
  "is_furnished": 1,
  "is_listrik_free": 0,
  "is_parkir_mobil": 1,
  "is_mesin_cuci": 1
}
```

### Laravel Implementation Pattern

In Laravel, gather all the data regardless of what region is being requested:
```php
$payload = [
    'luas_kamar' => $kos->room_size,
    'jarak_ke_bca' => $kos->bca_distance ?? 0.0,
    // Add all other attributes natively...
    'tipe_kos' => $kos->type, // putra, putri, or campur
    'is_km_dalam' => $kos->has_internal_bathroom ? 1 : 0,
    // ...
];

$response = Http::post(env('FASTAPI_MODEL_URL') . "/predict/{$kos->region}", $payload);

$predictedPrice = $response->json('predicted_price');
```

## MLflow Training Template

The local physical versions map (`models/`) has been removed in favor of native MLflow operations via a MySQL backend.

To train a new model or region:
1. Run your regular Scikit-Learn training pipelines in Jupyter Notebook to experiment and find the best Hyperparameters.
2. Open `scripts/retrain_all.py` and input your exact new Hyperparameters into the `REGION_CONFIGS` dictionary.
3. Stop your backend (`docker-compose down`) and restart the entire system with `gas.bat` (or `docker-compose up --build`).

The startup sequence will automatically:
1. Connect to the MLflow MySQL database.
2. Train your models on the exact preset parameters.
3. Register the trained models directly into the `mlflow_db` and tag them with `@production`.

Your FastAPI app will dynamically load whatever is tagged `@production` for that region in the registry upon startup. Ensure the FastAPI container is restarted if you tag a new model in production via the MLflow UI (`http://localhost:5000`).
