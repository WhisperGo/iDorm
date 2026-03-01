import sqlalchemy
from sqlalchemy import text
import mlflow

def seed_infrastructure():
    # port 3307 karena kita memetakan MySQL Docker ke sana
    engine = sqlalchemy.create_engine("mysql+pymysql://root:password@localhost:3307")
    
    with engine.connect() as conn:
        conn.execute(text("CREATE DATABASE IF NOT EXISTS mlflow_db"))
        conn.commit()
        print("Database mlflow_db siap.")

    mlflow.set_tracking_uri("http://localhost:5000")
    experiments = ["jakarta_pusat", "yogyakarta", "jakarta_selatan", "jakarta_utara"]
    for exp in experiments:
        if not mlflow.get_experiment_by_name(exp):
            mlflow.create_experiment(exp)
            print(f"Experiment {exp} dibuat.")

if __name__ == "__main__":
    seed_infrastructure()