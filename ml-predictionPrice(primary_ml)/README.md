# iDorm - Kos Price Prediction (MLOps Service)

> Fully containerized Machine Learning microservice for predicting fair monthly rental prices of boarding houses (kos) across Indonesian regions. Built with **FastAPI**, **MLflow**, **MySQL**, and **Docker Compose**, featuring observability via **Prometheus** and **Grafana**.

---

## Table of Contents

- [Overview](#overview)
- [Project Structure](#project-structure)
- [Supported Models & Performance](#supported-models--performance)
- [System Architecture](#system-architecture)
- [Quick Start Guide](#quick-start-guide)
  - [First-Time Setup (Full Reset)](#1-first-time-setup--full-reset-gasbat)
  - [Starting the Server (Daily Use)](#2-starting-the-server-daily-use)
  - [Stopping the Server](#3-stopping-the-server)
- [Verifying the Deployment](#verifying-the-deployment)
- [Integration with Main Web App (Laravel)](#integration-with-main-web-app-laravel)
- [MLflow Model Lifecycle](#mlflow-model-lifecycle)
- [Targeted Model Retraining](#targeted-model-retraining)
- [Load & Latency Testing](#load--latency-testing)
- [Rerunning Jupyter Notebooks (Local)](#rerunning-jupyter-notebooks-local)
- [Observability Stack (Grafana & Prometheus)](#observability-stack-grafana--prometheus)
- [API Internal Endpoints](#api-internal-endpoints)
- [Live Cloud Deployment (Optional)](#live-cloud-deployment-optional)

---

## Live Cloud Deployment (Optional)

This project is also deployed on **Google Cloud Platform**. If you prefer to access the system directly without running Docker locally, all services are available at the following public URLs:

| Service              | URL                                                                  | Description                                      |
| -------------------- | -------------------------------------------------------------------- | ------------------------------------------------ |
| Main Web (iDorm)     | [https://idorm.site](https://idorm.site)                             | The full iDorm web application (Laravel)         |
| ML API (Swagger)     | [https://prediction.idorm.site/docs](https://prediction.idorm.site/docs) | Interactive API documentation & live testing |
| MLflow Tracking      | [https://mlflow.idorm.site](https://mlflow.idorm.site)               | Model registry, parameters, metrics, and versions|
| Grafana Dashboard    | [https://grafana.idorm.site](https://grafana.idorm.site)             | Real-time monitoring (login: `admin` / `admin`)  |
| ML Database          | [https://db-ml.idorm.site](https://db-ml.idorm.site)                 | phpMyAdmin — browse the MLflow MySQL database    |
| Prometheus           | [https://prometheus.idorm.site](https://prometheus.idorm.site)       | Raw Prometheus metrics and query interface        |

> **Note:** The cloud deployment mirrors the exact same Docker Compose stack described in this README. All local instructions (prediction endpoints, Grafana setup, etc.) apply identically to the cloud URLs — simply replace `localhost:{port}` with the corresponding domain above.

---

## Overview

This microservice predicts fair monthly rental prices for **kos** based on room attributes such as room size, distance to landmarks, and available amenities. It supports **4 independent regional models**, each trained on region-specific datasets.

The entire ML lifecycle—from data preprocessing, model training, and MLflow registration to API serving and monitoring—is **fully automated and containerized within Docker**. No local Python environment or ML libraries are required on the host machine.

---

## Project Structure

```text
ml-predictionPrice(primary_ml)/
│
├── app/                        # FastAPI application source code
│   ├── main.py                 # Application entry point
│   ├── router.py               # API route definitions (/predict, /healthy, etc.)
│   ├── model_loader.py         # Loads @production models from MLflow registry
│   ├── schema.py               # Pydantic request/response validation schemas
│   ├── metrics.py              # Internal latency tracking (P50, P90, P95)
│   ├── prometheus_metrics.py   # Prometheus metric definitions
│   ├── middleware.py           # Request logging middleware
│   └── logging_config.py      # Structured JSON logging configuration
│
├── datasets/                   # Raw CSV data used for training
│   ├── jakarta_pusat.csv
│   ├── jakarta_selatan.csv
│   ├── jakarta_utara.csv
│   └── yogyakarta.csv
│
├── notebooks/                  # Jupyter Notebooks for experimentation
│   ├── jakarta_pusat.ipynb     # Hyperparameter tuning & EDA
│   ├── jakarta_selatan.ipynb
│   ├── jakarta_utara.ipynb
│   └── yogyakarta.ipynb
│
├── scripts/                    # Automation & utility scripts
│   ├── retrain_all.py          # Production retraining script (supports --region flag)
│   ├── load_test.py            # Load testing script (P50/P90/P95 latency report)
│   └── ...                     # Other helper/debug scripts
│
├── docs/                       # Technical documentation
│   ├── integration_guide.md    # How to integrate with the main web app
│   └── production_governance.md # MLOps governance framework
│
├── grafana_dashboard(json)/    # Pre-built Grafana dashboard templates
│   ├── overall_monitoring.json
│   └── regional_latency_monitoring.json
│
├── Dockerfile                  # Container image definition
├── docker-compose.yml          # Multi-container orchestration
├── requirements.txt            # Python dependencies (used inside Docker only)
├── requirements_notebook.txt   # Python dependencies for rerunning notebooks locally
├── prometheus.yml              # Prometheus scrape configuration
├── gas.bat                     # Windows one-click launcher (full reset + build)
└── README.md                   # This file
```

---

## Supported Models & Performance

Each region uses the best-performing algorithm as determined during the experimental phase (see `notebooks/`):

| Region             | Algorithm              | MAE (Rp)   | R² Score |
| ------------------ | ---------------------- | ---------- | -------- |
| `jakarta_pusat`    | Gradient Boosting      | 538,324    | 0.7112   |
| `jakarta_selatan`  | Random Forest          | 500,346    | 0.6247   |
| `jakarta_utara`    | Random Forest          | 256,533    | 0.6914   |
| `yogyakarta`       | Ridge Regression       | 255,535    | 0.7180   |

> **MAE** (Mean Absolute Error) represents the average prediction error in Rupiah.
> **R²** (R-Squared) represents how well the model explains price variance (1.0 = perfect).

---

## System Architecture

```text
┌─────────────────────────────────────────────────────────┐
│                    CLIENT (Main Web App)                │
│                POST /predict/{region}                   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│                  FastAPI Service (:8002)                  │
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

### Port Allocation

All ports are carefully chosen to avoid conflicts with the main web application (Laravel on `:8000`, MySQL on `:3306`):

| Port   | Service                   | URL                                |
| ------ | ------------------------- | ---------------------------------- |
| `3307` | ML MySQL Database         | Internal only                      |
| `8081` | phpMyAdmin                | http://localhost:8081               |
| `5000` | MLflow Tracking Server    | http://localhost:5000               |
| `8002` | FastAPI Prediction API    | http://localhost:8002               |
| `9092` | Prometheus                | http://localhost:9092               |
| `3002` | Grafana                   | http://localhost:3002               |

---

## Quick Start Guide

### Prerequisites

- **Docker Desktop** must be installed and running.
- No Python, Jupyter, or ML libraries need to be installed on the host machine.

---

### 1. First-Time Setup / Full Reset (`gas.bat`)

> **Use this when:** You are running the project for the very first time, OR you want to completely wipe everything and start fresh (e.g., for evaluation/grading purposes).

**Windows:**
```bat
gas.bat
```

**Mac / Linux:**

Since `.bat` files are Windows-only, Mac/Linux users should run the two commands inside `gas.bat` manually:
```bash
docker-compose down -v
docker-compose up --build
```

**What `gas.bat` does internally:**
1. `docker-compose down -v` — Stops all running containers and **deletes all Docker volumes** (wiping the MySQL database and all previously trained models). This guarantees a clean slate.
2. `docker-compose up --build` — **Rebuilds** all Docker images from scratch, then starts the entire stack in sequence:
   - Starts the `db_ml` MySQL container and waits until it is healthy.
   - Starts the `mlflow` tracking server (backed by MySQL).
   - Starts the `phpmyadmin` container for database inspection.
   - Starts the `retrain` container, which automatically reads the CSV datasets, trains all 4 regional models, registers them into MLflow with the `@production` alias, and then safely exits.
   - Starts the `fastapi_app` container, which connects to MLflow, loads the `@production` models into memory, and begins serving predictions.
   - Starts `prometheus` and `grafana` for monitoring.

> **Important:** Because `gas.bat` includes `--build`, it rebuilds Docker images every time. This is intentional for first-time setup and evaluation, but is **not necessary for daily use** (see below).

---

### 2. Starting the Server (Daily Use)

> **Use this when:** The project has already been set up before (you have already run `gas.bat` at least once), and you simply want to turn the server back on without rebuilding or retraining.

**Windows / Mac / Linux:**
```bash
docker-compose up -d
```

**What this does:**
- Starts all existing containers in the background (`-d` = detached mode).
- Does **not** rebuild images or retrain models.
- Your previously trained models and MLflow data are preserved inside the Docker volumes.
- Takes only a few seconds to start.

> **Tip:** Omit `-d` if you want to see the live server logs in your terminal (useful for debugging).

---

### 3. Stopping the Server

> **Use this when:** You are done testing and want to turn off Docker to free up system resources.

**To stop without deleting data (preserves models and database):**
```bash
docker-compose down
```

**To stop AND delete all data (full wipe, same as resetting):**
```bash
docker-compose down -v
```

> The `-v` flag removes Docker volumes. This means the MySQL database, trained models, and MLflow history will all be deleted. Only use this if you intend to do a full reset.

---

## Verifying the Deployment

Once the server is running and you see `Uvicorn running on http://0.0.0.0:8000` in the terminal logs, verify with:

| Check                    | URL                                             | Expected Result                                  |
| ------------------------ | ----------------------------------------------- | ------------------------------------------------ |
| API Health               | http://localhost:8002/healthy                    | Status `healthy`, lists all 4 loaded regions     |
| MLflow Registry          | http://localhost:5000                            | Navigate to "Models" to inspect versions & params|
| API Documentation        | http://localhost:8002/docs                       | Interactive Swagger UI for testing predictions   |
| phpMyAdmin (Database)    | http://localhost:8081                            | Browse the `mlflow_db` tables directly           |
| Grafana (Monitoring)     | http://localhost:3002                            | Login with `admin` / `admin`                     |

---

## Integration with Main Web App (Laravel)

The main iDorm web application (Laravel, running on port `:8000`) communicates with this ML service by making HTTP `POST` requests to the FastAPI container on port `:8002`.

**Endpoint:** `POST http://localhost:8002/predict/{region}`

Valid regions: `jakarta_pusat`, `jakarta_selatan`, `jakarta_utara`, `yogyakarta`

**Example Request Payload:**
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

The Laravel `PredictionController` then compares the user's offered price against the predicted price using the model's MAE as a dynamic margin to determine whether the price is **Wajar** (fair), **Overprice**, or **Underprice**.

---

## MLflow Model Lifecycle

This project uses **MLflow** with a **MySQL backend** for full model lifecycle governance:

1. Models are trained using the exact hyperparameters defined in `scripts/retrain_all.py`, which mirrors the best results from the experimental Jupyter Notebooks.
2. Trained models are registered into the MLflow Model Registry (stored in `mlflow_db` via MySQL).
3. Each model is tagged with the `@production` alias upon registration.
4. The FastAPI application exclusively loads models tagged `@production`, ensuring it always serves the correct, validated version.

> **Note:** `gas.bat` runs `docker-compose down -v` first, which wipes all Docker volumes. This means every run of `gas.bat` produces a fresh, deterministic Version 1 training. This is by design for evaluation purposes. For incremental versioning (e.g., creating Version 2), see [Targeted Model Retraining](#targeted-model-retraining).

---

## Targeted Model Retraining

If you want to retrain **only one region** (e.g., you updated the hyperparameters for Yogyakarta) without affecting the other 3 regions:

**Step 1:** Update the hyperparameters in `scripts/retrain_all.py` under the `REGION_CONFIGS` dictionary for the target region.

**Step 2:** Run the retraining script with the `--region` flag:
```bash
python scripts/retrain_all.py --region yogyakarta
```

**What happens:**
- Only Yogyakarta is retrained. Jakarta Pusat, Jakarta Selatan, and Jakarta Utara remain untouched at their current versions.
- MLflow automatically creates **Version 2** for Yogyakarta and assigns the `@production` alias to it.
- Restart the FastAPI container to load the new model:
  ```bash
  docker restart idorm_fastapi
  ```

> **Without the `--region` flag**, the script will retrain all 4 regions by default.

---

## Load & Latency Testing

A dedicated load testing script is included to stress-test the API and measure real-world performance:

```bash
python scripts/load_test.py
```

**What it does:**
- Fires 1,000 concurrent HTTP requests at the FastAPI prediction endpoint using 20 parallel threads.
- Measures and reports:
  - **P50 (Median):** The latency experienced by the average user.
  - **P90:** 90% of requests are faster than this.
  - **P95:** 95% of requests are faster than this (industry SLA benchmark).
- Also fetches internal FastAPI metrics from `/internal-metrics` for comparison between client-side latency (network + processing) and server-side latency (pure model inference).

> **Prerequisite:** The Docker stack must be running before executing this script. Run `docker-compose up -d` first.

### Continuous Traffic Generator (For Grafana)

To see the Grafana dashboards come alive with real-time charts, use the continuous load test which sends requests **indefinitely** across **all 4 regions** at random:

```bash
python scripts/load_test_continuous.py
```

**What it does:**
- Runs continuously until you press `Ctrl+C`.
- Sends 20 concurrent requests per batch to random regions (`jakarta_pusat`, `jakarta_selatan`, `jakarta_utara`, `yogyakarta`).
- Prints throughput updates every 500 requests.

> **Tip:** Run this script in one terminal, then open [http://localhost:3002](http://localhost:3002) (Grafana) in your browser. You will see the latency, and model status panels update in real-time as the traffic flows in.

---

## Rerunning Jupyter Notebooks (Local)

The `notebooks/` folder contains the 4 Jupyter Notebooks used during the experimentation and hyperparameter tuning phase. These notebooks are **not required** for running the ML system (Docker handles everything automatically), but if you wish to rerun them locally (e.g., to verify training results or experiment with different parameters), you will need a local Python environment with the correct library versions.

> **Important:** The `requirements.txt` in the project root is used exclusively inside Docker for the production system. It includes server-specific packages (FastAPI, Prometheus, etc.) that are not needed for notebooks. Use `requirements_notebook.txt` instead.

### Step-by-Step Setup

**Step 1:** Create a fresh Python virtual environment (Python 3.10 recommended):
```bash
# Using venv
python -m venv ml_env
# Activate it:
# Windows:
ml_env\Scripts\activate
# Mac/Linux:
source ml_env/bin/activate
```

**Step 2:** Install the notebook dependencies:
```bash
pip install -r requirements_notebook.txt
```

**Step 3:** Register the environment as a Jupyter kernel:
```bash
python -m ipykernel install --user --name ml_env --display-name "Python (ml_env)"
```

**Step 4:** Open Jupyter and run any notebook:
```bash
jupyter notebook
```
Navigate to the `notebooks/` folder and open any of the 4 regional notebooks. Make sure to select the **"Python (ml_env)"** kernel from the top-right kernel selector.

### Library Version Reference

| Library        | Version  | Purpose                              |
| -------------- | -------- | ------------------------------------ |
| `numpy`        | 1.26.4   | Numerical computing                  |
| `pandas`       | 1.5.3    | Data manipulation & CSV loading      |
| `scikit-learn` | 1.2.2    | ML algorithms & preprocessing        |
| `matplotlib`   | 3.8.5    | Visualization & plotting             |
| `seaborn`      | 0.13.2   | Statistical visualization            |
| `scipy`        | 1.15.3   | Statistical tests & distributions    |
| `joblib`       | 1.5.2    | Model serialization                  |

---

## Observability Stack (Grafana & Prometheus)

The FastAPI service exposes Prometheus metrics which are automatically scraped and can be visualized in Grafana.

### Setting Up Grafana

1. Open http://localhost:3002 in your browser.
2. Login with username `admin` and password `admin`.
3. Go to **Connections** > **Data Sources** > **Add data source**.
4. Select **Prometheus**.
5. In the **Connection URL** field, enter: `http://prometheus:9090`
   *(This is the internal Docker network address; do not use `localhost` here.)*
6. Click **Save & Test**.

### Importing Pre-Built Dashboards

Pre-configured Grafana dashboards are provided in the `grafana_dashboard(json)/` folder:

| Dashboard File                        | Purpose                                         |
| ------------------------------------- | ----------------------------------------------- |
| `overall_monitoring.json`             | Total requests, errors, model status, throughput |
| `regional_latency_monitoring.json`    | P50, P90, P95 latency panels per region          |

**To import:**
1. In Grafana, click the **+** icon > **Import dashboard**.
2. Copy-paste the JSON content from the respective file.
3. Select the Prometheus data source you just configured.
4. Click **Import**.

---

## API Internal Endpoints

Besides the main prediction endpoint, the FastAPI server exposes these utility endpoints:

| Endpoint                      | Method | Description                                                    |
| ----------------------------- | ------ | -------------------------------------------------------------- |
| `/healthy`                    | GET    | Health check showing loaded models and their statuses          |
| `/predict/{region}`           | POST   | Main prediction endpoint                                      |
| `/docs`                       | GET    | Interactive Swagger API documentation                          |
| `/metrics`                    | GET    | Prometheus-compatible metrics (scraped automatically)          |
| `/internal-metrics`           | GET    | Latency percentiles (mean, P50, P90, P95) and error counts    |
| `/model-info/{region}`        | GET    | Shows the currently loaded MLflow Run ID and model version     |

---

## License

This project is part of the **iDorm** web application, developed for academic purposes at Bina Nusantara University.