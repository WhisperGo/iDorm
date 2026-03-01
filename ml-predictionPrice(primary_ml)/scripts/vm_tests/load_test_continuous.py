import time
import requests
import json
import statistics
import concurrent.futures
import math
import random
import os
from termcolor import colored

# Configuration from env vars for VM usage
BASE_URL = os.getenv("TARGET_URL", "https://idorm.site:8002/predict/")
GRAFANA_URL = os.getenv("GRAFANA_URL", "https://idorm.site:3002")
CONCURRENCY = int(os.getenv("CONCURRENCY", "20"))
REGIONS = ["jakarta_pusat", "jakarta_selatan", "jakarta_utara", "yogyakarta"]

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

def send_request(req_id: int):
    """Sends a single POST request to a random region and returns latency in ms."""
    region = random.choice(REGIONS)
    url = BASE_URL + region
    
    start_time = time.perf_counter()
    status = False
    try:
        resp = requests.post(url, json=PAYLOAD, headers=HEADERS, timeout=5)
        if resp.status_code == 200:
            status = True
    except Exception:
        pass
    
    latency_ms = (time.perf_counter() - start_time) * 1000
    return latency_ms, status

def run_load_test():
    print(colored(f"--- Starting Continuous Multi-Region Load Test (Concurrency: {CONCURRENCY}) ---", "cyan", attrs=["bold"]))
    print(f"Targeting: {BASE_URL} + {REGIONS}\n")
    print("Press Ctrl+C to stop the test and view final metrics in Grafana.\n")
    
    success_count = 0
    failure_count = 0
    total_requests = 0
    start_time = time.perf_counter()
    
    try:
        with concurrent.futures.ThreadPoolExecutor(max_workers=CONCURRENCY) as executor:
            while True:
                futures = [executor.submit(send_request, i) for i in range(CONCURRENCY)]
                
                for future in concurrent.futures.as_completed(futures):
                    latency, success = future.result()
                    total_requests += 1
                    if success:
                        success_count += 1
                    else:
                        failure_count += 1
                
                if total_requests % 500 == 0:
                    elapsed = time.perf_counter() - start_time
                    rps = total_requests / elapsed if elapsed > 0 else 0
                    print(f"  ... {total_requests} requests sent | Current Throughput: {rps:.1f} req/s")
                    
                time.sleep(0.05) # Small sleep to prevent overwhelming the local client CPU
                
    except KeyboardInterrupt:
        print("\n" + colored("[!] Load test stopped by user.", "red", attrs=["bold"]))
        
        elapsed = time.perf_counter() - start_time
        rps = total_requests / elapsed if elapsed > 0 else 0
        
        print("\n" + "="*50)
        print(colored("--- FINAL SUMMARY ---", "yellow", attrs=["bold"]))
        print("="*50)
        print(f"Total Time Running : {elapsed:.2f} seconds")
        print(f"Total Requests     : {total_requests}")
        print(f"Average Throughput : {rps:.2f} req/s")
        print(f"Successful         : {colored(str(success_count), 'green')}")
        print(f"Failed             : {colored(str(failure_count), 'red' if failure_count > 0 else 'green')}")
        print(f"\nCek dashboard Grafana ({GRAFANA_URL}) untuk melihat pergerakan traffic secara real-time!")

if __name__ == "__main__":
    try:
        import termcolor
    except ImportError:
        import sys
        import subprocess
        print("Installing termcolor for styling...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", "termcolor", "requests"])
        
    run_load_test()
