@echo off
echo   iDorm ML - Starting All Services
echo.
echo Prerequisites: Docker Desktop must be running.
echo.

echo [1/2] Stopping old containers...
docker-compose down -v

echo [2/2] Building and starting all services...
echo   This will automatically:
echo     - Start MySQL database
echo     - Start MLflow server
echo     - Train and register all 4 models
echo     - Start FastAPI prediction server
echo     - Start Prometheus + Grafana monitoring
echo.
docker-compose up --build
