from prometheus_client import Counter, REGISTRY
from fastapi import APIRouter
from fastapi.responses import Response
import prometheus_client

metrics_router = APIRouter()

def get_or_create_counter(name, documentation, labelnames=()):
    if name in REGISTRY._names_to_collectors:
        return REGISTRY._names_to_collectors[name]
    return Counter(name, documentation, labelnames)

REQUEST_COUNT = get_or_create_counter(
    'prediction_requests',
    'Total number of prediction requests',
    ['region', 'status']
)

@metrics_router.get("/metrics")
async def metrics():
    return Response(
        content=prometheus_client.generate_latest(REGISTRY),
        media_type=prometheus_client.CONTENT_TYPE_LATEST
    )