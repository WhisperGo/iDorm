# 🏠 iDorm MLOps Prediction Service

> **Enterprise-grade MLOps Prediction API** for predicting boarding house (kos) rental prices across Indonesian regions. This service features fully containerized model training, MLflow model registry, and a FastAPI serving layer with Prometheus observability.

---

## 📋 Table of Contents
- [Overview](#overview)
- [Architecture](#architecture)
- [Getting Started (For Evaluators)](#getting-started-for-evaluators)
- [Integration Guide (For Main Web App)](#integration-guide-for-main-web-app)
- [MLflow Model Lifecycle](#mlflow-model-lifecycle)
- [Observability Stack](#observability-stack)

---

## Overview

This microservice predicts fair monthly rental prices for **kos** based on room attributes. It supports **4 independent regional models**:
- `jakarta_pusat` (Random Forest)
- `jakarta_selatan` (Random Forest)
- `jakarta_utara` (Linear Regression)
- `yogyakarta` (Random Forest)

The entire ML lifecycle—from model training and registration to serving and monitoring—is entirely automated and encapsulated within Docker. **No local Python environment or dependencies are required.**

---

## Architecture

```text
┌─────────────────────────────────────────────────────────┐
│                    CLIENT (Main Web App)                │
│                POST /predict/{region}                   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│                  FastAPI Service                         │
│  Loads models tagged with '@production' alias from MLflow│
├──────────────────────────────────────────────────────────┤
│  Prometheus Metrics  │  JSON Logging  │ Health Endpoints │
└──────────────────────┬───────────────────────────────────┘
                       │
           ┌───────────┴──────────────┐
           ▼                          ▼
┌──────────────────┐      ┌────────────────────┐
│  MLflow Registry │      │  Monitoring Layer  │
│  (MySQL backend) │      │  ┌──────────────┐  │
│                  │      │  │  Prometheus  │  │
│  @production     │      │  └──────┬───────┘  │
│  alias per model │      │  ┌──────▼───────┐  │
└──────────────────┘      │  │   Grafana    │  │
                          │  └──────────────┘  │
                          └────────────────────┘
```

The system uses dedicated ports to prevent any conflicts with your main web application:
- `3307`: ML MySQL Database (`mlflow_db`)
- `5000`: MLflow Tracking Server UI
- `8002`: FastAPI Prediction Server
- `9092`: Prometheus
- `3002`: Grafana

---

## Getting Started (For Evaluators)

This project has been engineered to be **100% plug-and-play**. You do not need Python, Jupyter, or any ML libraries installed on your host machine.

### Prerequisites
- **Docker Desktop** must be running.

### 1. Launch the Environment
Open your terminal in this repository folder and execute:

**Windows:**
```bat
gas.bat
```

**What happens automatically:**
1. Spins up the `mlflow_db` MySQL container.
2. Spins up the `mlflow` server.
3. Spins up the `retrain` container which:
   - Reads the raw CSV datasets.
   - Cleans data and executes the feature engineering pipeline.
   - Trains all 4 regional models mathematically perfectly.
   - Registers them into MLflow and tags them as `@production`.
   - Safely exits.
4. Spins up the `fastapi_app` container, which waits for the models to be ready, loads them into high-speed memory, and begins listening for requests.
5. Spins up `prometheus` and `grafana`.

### 2. Verify the Deployment
Once `gas.bat` finishes and you see `Uvicorn running on http://0.0.0.0:8000` in the terminal logs:

1. **Verify API Health:** [http://localhost:8002/healthy](http://localhost:8002/healthy) 
   *(Should return status "healthy" and list all 4 regions)*
2. **Explore MLflow Registry:** [http://localhost:5000](http://localhost:5000)
   *(Navigate to "Models" to see the registered versions and parameters)*
3. **View API Documentation:** [http://localhost:8002/docs](http://localhost:8002/docs)
   *(Interactive Swagger UI where you can test predictions directly)*

---

## Integration Guide (For Main Web App)

To integrate this ML service into your main web application (e.g., Laravel, Node.js), simply make a `POST` request to the FastAPI container running on port `8002`.

**Endpoint:** `POST http://localhost:8002/predict/{region}`
*(Valid regions: `jakarta_pusat`, `jakarta_selatan`, `jakarta_utara`, `yogyakarta`)*

**Example Payload:**
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

**Example Response:**
```json
{
  "region": "jakarta_pusat",
  "predicted_price": 2920511.76,
  "model_version": "1"
}
```

---

## MLflow Model Lifecycle

This project utilizes **MLflow** for robust lifecycle governance:
1. Every time `gas.bat` is run, the script `scripts/retrain_all.py` executes inside an isolated container.
2. It trains the models using the exact parameters determined in the experimental phase. 
3. The newly trained models are uploaded into the MLflow MySQL database and attached with the `@production` alias.
4. The FastAPI application queries MLflow exclusively for the `@production` alias, ensuring it always loads the correct version.

*Note: `gas.bat` runs `docker-compose down -v` first. This purposefully wipes the Docker Volumes to guarantee a completely fresh, deterministic training run starting from Version 1 every single time it is evaluated.*

---

## Observability Stack

The FastAPI service exposes Prometheus metrics which are automatically scraped and can be visualized in Grafana. 

### Accessing Grafana
1. Open [http://localhost:3002](http://localhost:3002) in your browser.
2. Login with `admin` / `admin`.
3. Add a **Prometheus** Data Source.
4. When prompted for the connection URL, enter: `http://prometheus:9090` *(Grafana resolves the internal Docker network name).*

Recomended JSON for GRAFANA (by developer):
OVERALL MONITORING:
{
  "annotations": { "list": [] },
  "editable": true,
  "fiscalYearStartMonth": 0,
  "graphTooltip": 1,
  "links": [],
  "liveNow": false,
  "panels": [
    {
      "title": "Quick Overview (Total Requests vs Errors)",
      "type": "stat",
      "gridPos": { "h": 6, "w": 8, "x": 0, "y": 0 },
      "targets": [
        { "expr": "sum(prediction_requests_total)", "legendFormat": "Requests" },
        { "expr": "sum(prediction_errors_total)", "legendFormat": "Errors" }
      ],
      "fieldConfig": {
        "defaults": {
          "color": { "mode": "thresholds" },
          "thresholds": {
            "mode": "absolute",
            "steps": [
              { "color": "green", "value": null },
              { "color": "red", "value": 1 }
            ]
          }
        }
      }
    },
    {
      "title": "System Latency P95 (Real-time Experience)",
      "type": "stat",
      "gridPos": { "h": 6, "w": 8, "x": 8, "y": 0 },
      "targets": [
        { "expr": "histogram_quantile(0.95, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))" }
      ],
      "fieldConfig": {
        "defaults": { "unit": "s", "mappings": [], "thresholds": { "mode": "absolute", "steps": [{ "color": "green", "value": null }, { "color": "yellow", "value": 0.5 }, { "color": "red", "value": 1.5 }] } }
      }
    },
    {
      "title": "Model Load Status per Region",
      "type": "stat",
      "gridPos": { "h": 6, "w": 8, "x": 16, "y": 0 },
      "targets": [
        { "expr": "model_load_status", "legendFormat": "{{region}}" }
      ],
      "options": { "colorMode": "background", "graphMode": "none", "textMode": "name" },
      "fieldConfig": {
        "defaults": { "mappings": [{ "options": { "0": { "color": "red", "text": "DOWN" }, "1": { "color": "green", "text": "READY" } }, "type": "value" }] }
      }
    },
    {
      "title": "Throughput by Region (Requests/sec)",
      "type": "timeseries",
      "gridPos": { "h": 9, "w": 24, "x": 0, "y": 6 },
      "targets": [
        { "expr": "sum by (region) (rate(prediction_requests_total[1m]))", "legendFormat": "{{region}}" }
      ],
      "options": { "legend": { "displayMode": "table", "placement": "right" } }
    },
    {
      "title": "Prediction Value Distribution (Monitoring Drift)",
      "type": "histogram",
      "gridPos": { "h": 9, "w": 24, "x": 0, "y": 15 },
      "targets": [
        { "expr": "prediction_value_summary_sum / prediction_value_summary_count", "legendFormat": "Average Predicted Price" }
      ]
    }
  ],
  "refresh": "5s",
  "schemaVersion": 38,
  "style": "dark",
  "tags": ["iDorm", "MLOps", "PPTI-BCA"],
  "time": { "from": "now-30m", "to": "now" },
  "title": "iDorm MLOps Final Dashboard",
  "uid": "idorm_pro_v1"
}

LATENCY FOCUS MONITORING:
{
  "annotations": { "list": [] },
  "editable": true,
  "fiscalYearStartMonth": 0,
  "graphTooltip": 1,
  "links": [],
  "liveNow": false,
  "panels": [
    {
      "title": "Total Requests vs Errors",
      "type": "stat",
      "gridPos": { "h": 6, "w": 6, "x": 0, "y": 0 },
      "targets": [
        { "expr": "sum(prediction_requests_total)", "legendFormat": "Req" },
        { "expr": "sum(prediction_errors_total)", "legendFormat": "Err" }
      ],
      "fieldConfig": {
        "defaults": { "color": { "mode": "thresholds" }, "thresholds": { "mode": "absolute", "steps": [{ "color": "green", "value": null }, { "color": "red", "value": 1 }] } }
      }
    },
    {
      "title": "Median Latency (P50)",
      "type": "stat",
      "gridPos": { "h": 6, "w": 6, "x": 6, "y": 0 },
      "targets": [
        { "expr": "histogram_quantile(0.50, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))" }
      ],
      "fieldConfig": {
        "defaults": { "unit": "s", "thresholds": { "mode": "absolute", "steps": [{ "color": "green", "value": null }, { "color": "yellow", "value": 0.2 }, { "color": "red", "value": 0.5 }] } }
      }
    },
    {
      "title": "Tail Latency (P90)",
      "type": "stat",
      "gridPos": { "h": 6, "w": 6, "x": 12, "y": 0 },
      "targets": [
        { "expr": "histogram_quantile(0.90, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))" }
      ],
      "fieldConfig": {
        "defaults": { "unit": "s", "thresholds": { "mode": "absolute", "steps": [{ "color": "green", "value": null }, { "color": "yellow", "value": 0.5 }, { "color": "red", "value": 1.0 }] } }
      }
    },
    {
      "title": "Worst Latency (P95)",
      "type": "stat",
      "gridPos": { "h": 6, "w": 6, "x": 18, "y": 0 },
      "targets": [
        { "expr": "histogram_quantile(0.95, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))" }
      ],
      "fieldConfig": {
        "defaults": { "unit": "s", "thresholds": { "mode": "absolute", "steps": [{ "color": "green", "value": null }, { "color": "yellow", "value": 0.8 }, { "color": "red", "value": 1.5 }] } }
      }
    },
    {
      "title": "Latency Percentiles Trend",
      "type": "timeseries",
      "gridPos": { "h": 8, "w": 24, "x": 0, "y": 6 },
      "targets": [
        { "expr": "histogram_quantile(0.50, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))", "legendFormat": "P50 (Median)" },
        { "expr": "histogram_quantile(0.90, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))", "legendFormat": "P90" },
        { "expr": "histogram_quantile(0.95, sum by (le) (rate(prediction_latency_seconds_bucket[5m])))", "legendFormat": "P95" }
      ],
      "options": { "legend": { "displayMode": "table", "placement": "right" } },
      "fieldConfig": { "defaults": { "unit": "s" } }
    },
    {
      "title": "Model Status",
      "type": "stat",
      "gridPos": { "h": 4, "w": 24, "x": 0, "y": 14 },
      "targets": [ { "expr": "model_load_status", "legendFormat": "{{region}}" } ],
      "options": { "colorMode": "background", "textMode": "name" },
      "fieldConfig": {
        "defaults": { "mappings": [{ "options": { "0": { "color": "red", "text": "DOWN" }, "1": { "color": "green", "text": "READY" } }, "type": "value" }] }
      }
    }
  ],
  "refresh": "5s",
  "schemaVersion": 38,
  "style": "dark",
  "tags": ["iDorm", "MLOps", "Binus"],
  "time": { "from": "now-15m", "to": "now" },
  "title": "iDorm MLOps Final - Multi-Percentile",
  "uid": "idorm_multi_p"
}

### Internal Endpoints
The FastAPI server also exposes these internal status endpoints:
- `GET /internal-metrics`: Returns latency percentiles (mean, p50, p90, p95) and error counts.
- `GET /model-info/{region}`: Details the currently loaded MLflow Run ID and version.