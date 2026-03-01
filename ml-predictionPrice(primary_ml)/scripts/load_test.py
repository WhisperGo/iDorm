import time
import requests
import json
import statistics
import concurrent.futures
import math
from termcolor import colored

# Configuration
URL = "http://localhost:8002/predict/yogyakarta"
PAYLOAD = {
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
HEADERS = {"Content-Type": "application/json"}
NUM_REQUESTS = 1000
CONCURRENCY = 20

def send_request(req_id: int):
    """Sends a single POST request and returns latency in milliseconds."""
    start_time = time.perf_counter()
    try:
        resp = requests.post(URL, json=PAYLOAD, headers=HEADERS, timeout=5)
        _ = resp.json()
        status = True
    except Exception as e:
        status = False
    
    latency_ms = (time.perf_counter() - start_time) * 1000
    return latency_ms, status

def run_load_test():
    print(colored(f"--- Starting Load Test ({NUM_REQUESTS} requests, Concurrency: {CONCURRENCY}) ---", "cyan", attrs=["bold"]))
    print(f"Target: {URL}\n")
    
    latencies = []
    success_count = 0
    failure_count = 0
    
    test_start = time.perf_counter()
    
    # Run with thread pool to simulate real-world web traffic (many users at once)
    with concurrent.futures.ThreadPoolExecutor(max_workers=CONCURRENCY) as executor:
        # Submit all tasks
        futures = [executor.submit(send_request, i) for i in range(NUM_REQUESTS)]
        
        # Monitor progress
        completed = 0
        for future in concurrent.futures.as_completed(futures):
            latency, success = future.result()
            if success:
                latencies.append(latency)
                success_count += 1
            else:
                failure_count += 1
                
            completed += 1
            if completed % 1000 == 0:
                print(f"  ... {completed}/{NUM_REQUESTS} requests completed")

    test_duration = time.perf_counter() - test_start
    throughput = NUM_REQUESTS / test_duration
    
    print("\n" + "="*50)
    print(colored("--- LOAD TEST RESULTS (Client Side) ---", "yellow", attrs=["bold"]))
    print("="*50)
    
    print(f"Total Time Taken : {test_duration:.2f} seconds")
    print(f"Throughput       : {throughput:.2f} requests / second")
    print(f"Successful       : {colored(str(success_count), 'green')}")
    print(f"Failed           : {colored(str(failure_count), 'red' if failure_count > 0 else 'green')}")
    
    if latencies:
        # Sort latencies to compute percentiles manually over the exact dataset
        latencies.sort()
        
        avg_lat = sum(latencies) / len(latencies)
        min_lat = latencies[0]
        max_lat = latencies[-1]
        
        # Calculate exactly the indices for percentiles
        def get_percentile(data, p):
            k = (len(data) - 1) * (p / 100.0)
            f = math.floor(k)
            c = math.ceil(k)
            if f == c:
                return data[int(k)]
            d0 = data[int(f)] * (c - k)
            d1 = data[int(c)] * (k - f)
            return d0 + d1

        p50 = get_percentile(latencies, 50)
        p90 = get_percentile(latencies, 90)
        p95 = get_percentile(latencies, 95)
        p99 = get_percentile(latencies, 99)
        
        print("\n" + colored("--- LATENCY PERCENTILES (Client Observer) ---", "cyan"))
        print(f"  Mean : {avg_lat:8.2f} ms")
        print(f"  Min  : {min_lat:8.2f} ms")
        print(f"  P50  : {p50:8.2f} ms  (Median)")
        print(f"  P90  : {p90:8.2f} ms  (90% requests faster than this)")
        print(f"  P95  : {p95:8.2f} ms  (95% requests faster than this)")
        print(f"  P99  : {p99:8.2f} ms ")
        print(f"  Max  : {max_lat:8.2f} ms")
        
    print("\n" + "="*50)
    print(colored("--- API INTERNAL METRICS (FastAPI Side) ---", "magenta", attrs=["bold"]))
    print("="*50)
    try:
        internal_metrics = requests.get("http://localhost:8002/internal-metrics").json()
        if "jakarta_utara" in internal_metrics["regions"]:
            metrics = internal_metrics["regions"]["jakarta_utara"]
            print(f"API Tracked count: {metrics['count']:>10,}")
            print(f"API Tracked Mean : {metrics['mean_ms']:>10.2f} ms")
            print(f"API Tracked P50  : {metrics['p50_ms']:>10.2f} ms")
            print(f"API Tracked P90  : {metrics['p90_ms']:>10.2f} ms")
            print(f"API Tracked P95  : {metrics['p95_ms']:>10.2f} ms")
            print(f"API Tracked Max  : {metrics['max_ms']:>10.2f} ms")
        else:
            print(f"Internal metrics for jakarta_utara not found. Raw response: {internal_metrics}")
    except Exception as e:
        print(f"Could not fetch internal metrics: {e}")

if __name__ == "__main__":
    try:
        import termcolor
    except ImportError:
        import sys
        import subprocess
        print("Installing termcolor for styling...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", "termcolor", "requests"])
        
    run_load_test()