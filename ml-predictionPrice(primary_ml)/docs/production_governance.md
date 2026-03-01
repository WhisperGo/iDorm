
# Production Governance Documentation

**Kos Price Prediction**

---

## 1. Purpose

This document defines the governance framework applied to the Kos Price Prediction API production environment. It ensures that model deployment, monitoring, validation, and rollback processes are controlled, auditable, and aligned with industry best practices.

The governance framework aims to:

* Ensure model stability and reliability
* Prevent silent model degradation
* Provide traceability for all model versions
* Enable controlled promotion and rollback
* Support monitoring and operational transparency

---

## 2. Model Ownership & Responsibilities

| Role             | Responsibility                                              |
| ---------------- | ----------------------------------------------------------- |
| ML Engineer      | Model training, evaluation, versioning, metadata management |
| Backend Engineer | API integration, stability, endpoint reliability |
| Project Lead     | Approval of model promotion to production                   |

Clear ownership ensures accountability during incident handling and model lifecycle management.

---

## 3. Model Versioning Policy

The system implements **MLflow-backed semantic registering** for model artifacts. We do not use physical folders anymore; everything is tracked in a centralized MySQL database (`mlflow_db`).

Production models are tracked using **Aliases** instead of stages or explicit folder paths. 

```
mlflow.pyfunc.load_model(f"models:/{region}@production")
```

Each run in the MySQL database automatically contains:
* The `.pkl` artifact (stored in the Docker volume `mlruns_data`)
* Full hyperparameters (e.g., `n_estimators`, `learning_rate`)
* Validation metrics (e.g., `MAE`, `R2`, `MAPE`)
* Training environment requirements

---

## 4. Metadata Governance

Because we use a MySQL-backed MLflow tracking server, metadata is natively enforced.
Each model version automatically includes tracking for:

* `region` (via model name registry)
* `model_version` (auto-incremented by MLflow)
* `params` (logged via `mlflow.log_params()`)
* `metrics` (logged via `mlflow.log_metrics()`)
* `signature` (automatically enforces input schema and data types)

### Integrity Validation
During API startup, `App/model_loader.py` validates:
1. The requested region exists in the MLflow registry.
2. A model is explicitly tagged with the `@production` alias.
3. The Input Signature matches the Pydantic schema required by FastAPI.

If validation fails, the API gracefully falls back or throws a designated 404 until the registry is corrected. This prevents corrupted deployments or silent feature mismatches.

---

## 5. Model Promotion Process

The system follows an **Alias Promotion Strategy** via MLflow.

### Steps:
1. Jupyter Notebooks are used to find optimal algorithms and hyperparameters.
2. The optimal parameters are hardcoded into `scripts/retrain_all.py`.
3. The Docker stack is built (`gas.bat`). The retraining script runs automatically, connecting to the MySQL MLflow store.
4. The system natively registers the new model and tags it with `@production`.
5. The FastAPI server starts, pulling the newly aliased `@production` model directly into memory.

To safely test a "Staging" model without breaking production, standard MLflow practices dictate creating a `@staging` alias and routing traffic accordingly.

---

## 6. Monitoring & Observability

The system implements multiple monitoring layers visible via Grafana (`http://localhost:3002`):

### 6.1 Operational Monitoring & Load Testing
The API provides deep insights into latency and load handling via Prometheus metrics at `/metrics`, and internal summaries at `/internal-metrics`.

* Request count
* Error count
* Latency tracking (Mean, **P50**, **P90**, **P95**)

These metrics are essential for determining the difference between "Model Latency" (internal math speed) and "Client Latency" (the speed the user experiences when predicting a price). A dedicated `scripts/load_test.py` is included to simulate 10,000 concurrent user requests to test Web Server throughput vs CPU latency.

---

### 6.2 Prediction Monitoring
Prometheus Tracks:
* Latest predicted value (`idorm_latest_prediction_value`)
* Prediction distribution summary (`idorm_prediction_value_summary`)

This allows for real-time drift detection if sudden spikes in Kos prices occur inside the API's requests.

---

## 7. Rollback Strategy

Because models are stored in a MySQL relational database managed by MLflow, rollbacks are instant and do not require code changes or file movements.

If production instability is detected:
1. Open the MLflow UI (`http://localhost:5000`).
2. Navigate to the failing regional model (e.g., `jakarta_utara`).
3. Remove the `@production` alias from the current version.
4. Add the `@production` alias to the previous stable version.
5. Restart the FastAPI Docker container (`docker restart idorm_fastapi`).

The system will instantly pull the older, stable model without any downtime in the main proxy.

---

## 8. Error Handling & Logging

The system implements:

* Structured JSON logging
* Request ID tracking
* Global exception handling
* Separate error logs
* No raw exception exposure to client

Logs include:

* Region
* Model version
* Latency
* Request ID
* Prediction output

This ensures traceability and auditability.

---

## 9. Data & Feature Governance

Feature schema is controlled by Pydantic validation.

Validation guarantees:

* Numeric bounds enforcement
* Enum validation for categorical values
* Required feature completeness

Feature mismatch between model and schema triggers startup failure.

---

## 10. Retraining Policy

Retraining is performed manually when:

* Significant new data is available
* Monitoring detects drift
* Performance degradation is observed
* Scheduled evaluation requires re-validation

Retrained models are not automatically promoted to production.

---

## 11. Security Considerations

The system ensures:

* No exposure of internal stack traces
* Structured logs without sensitive data
* Model artifacts are not publicly accessible
* Request ID tracking for trace investigation

---

## 12. Governance Maturity Level

This system implements:

* Controlled semantic versioning
* Artifact validation
* Monitoring and observability
* Manual approval promotion
* Rollback capability
* Structured logging
* Drift awareness

---

## 13. Future Improvements

Potential enterprise extensions:

* Automated CI/CD retraining pipeline
* MLflow Model Registry integration
* Automated rollback triggers

---

# Conclusion

The Kos Price Prediction API follows a controlled and auditable production governance framework that prioritizes stability, validation integrity, and operational transparency.

This approach ensures that model updates are deliberate, monitored, and reversible, reducing risk while maintaining performance and reliability.