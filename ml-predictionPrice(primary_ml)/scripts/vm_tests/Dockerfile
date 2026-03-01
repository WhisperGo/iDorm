# Use a lightweight Python image
FROM python:3.10-slim

# Set working directory
WORKDIR /app

# Install dependencies required by the load testing scripts
RUN pip install --no-cache-dir requests termcolor

# Copy the load test scripts into the container
COPY load_test.py /app/load_test.py
COPY load_test_continuous.py /app/load_test_continuous.py

# Set default env variables (can be overridden at runtime)
# Target base URL for the continuous load test
ENV TARGET_URL="https://idorm.site:8002/predict/"
# Target host for the single load test
ENV TARGET_HOST="https://idorm.site:8002"
# Informational URL for the dashboard
ENV GRAFANA_URL="https://idorm.site:3002"
# Number of concurrent threads
ENV CONCURRENCY=20
# Number of requests for the static load test
ENV NUM_REQUESTS=1000

# By default, run the continuous load test. You can override this command when running the container.
CMD ["python", "load_test_continuous.py"]
