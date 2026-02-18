import pandas as pd

try:
    # Load the dataset
    df = pd.read_csv('training_dataset.csv')
    
    # Check basic info
    print("--- DATASET INFO ---")
    print(f"Total Rows: {len(df)}")
    print(f"Columns: {df.columns.tolist()}")
    
    # Check Intent Distribution
    print("\n--- INTENT DISTRIBUTION ---")
    print(df['intent'].value_counts())
    
    # Check for exact duplicates
    duplicates = df.duplicated().sum()
    print(f"\n--- DUPLICATES: {duplicates} ---")
    
    # Show sample data to check variance
    print("\n--- SAMPLE ROWS ---")
    print(df.sample(5).to_string())

except Exception as e:
    print(f"CRITICAL ERROR: Could not read file. {e}")