# VM Load Testing Scripts

This directory contains the necessary files to run load testing scripts against your machine learning API on a Google Cloud Platform (GCP) Compute Engine VM. 

We recommend using Docker to run these scripts as it isolates dependencies and simplifies deployment on the VM without modifying the server environment.

## 1. Build the Docker Image

You can build the Docker image either on your local machine and push it to a registry (like Docker Hub or Google Container Registry), or build it directly inside your VM.

To build it directly, navigate to this directory on the VM and run:

```bash
docker build -t ml-load-tester .
```

## 2. Run the Docker Container

The scripts support configuration via environment variables so you don't need to hardcode URLs or connection concurrency.

### Running Continuous Load Testing (Default)

Running this command will start the `load_test_continuous.py` script indefinitely to generate background traffic against your API. Since the default environment variables already point to `https://idorm.site`, you can just run this without overriding any URLs (unless you want to target a different IP or change concurrency):

```bash
docker run -it --rm \
  -e TARGET_URL="https://idorm.site:8002/predict/" \
  -e GRAFANA_URL="https://idorm.site:3002" \
  -e CONCURRENCY=20 \
  ml-load-tester
```
*(If you are running the test from the same VM where the API is hosted using Docker networking, you might use the docker network alias instead of `idorm.site`)*.

### Running Static Load Testing

If you want to run the static `load_test.py` script to generate a fixed number of requests and output full percentile percentiles, override the default command:

```bash
docker run -it --rm \
  -e TARGET_HOST="https://idorm.site:8002" \
  -e CONCURRENCY=20 \
  -e NUM_REQUESTS=1000 \
  ml-load-tester python load_test.py
```

### Environment Variables

| Variable | Script | Default | Description |
|---|---|---|---|
| `TARGET_URL` | `load_test_continuous.py` | `https://idorm.site:8002/predict/` | The base URL to which the regions will be appended. |
| `TARGET_HOST` | `load_test.py` | `https://idorm.site:8002` | The host for static load test endpoints (predict & internal-metrics). |
| `GRAFANA_URL` | `load_test_continuous.py` | `https://idorm.site:3002` | Used for logging so you know where to view results. |
| `CONCURRENCY` | Both | `20` | Thread concurrency count. |
| `NUM_REQUESTS`| `load_test.py` | `1000` | Total number of requests. |
