import pickle
import re
import sys
import os

# --- CONFIGURATION ---
MODEL_FILE = 'intent_model.pkl'

# --- 1. REPLICATE THE CLEANING FUNCTION ---
# CRITICAL: This MUST be identical to what you wrote in the Jupyter Notebook.
# If you change one regex character here, the model will be confused.
def clean_text(text):
    text = str(text).lower()
    # Keep only letters, numbers, dots (for time), and spaces
    text = re.sub(r'[^a-z0-9\.\s]', '', text) 
    return text

# --- 2. LOAD THE BRAIN ---
print(f"Loading model from {MODEL_FILE}...")

if not os.path.exists(MODEL_FILE):
    print("CRITICAL ERROR: Model file not found. Did you run the training notebook?")
    sys.exit(1)

try:
    with open(MODEL_FILE, 'rb') as f:
        model = pickle.load(f)
    print("✅ Model loaded successfully.")
except Exception as e:
    print(f"❌ Failed to load model: {e}")
    sys.exit(1)

# --- 3. PREDICTION FUNCTION ---
def predict(user_input):
    # Step A: Clean the input (Pre-processing)
    cleaned_input = clean_text(user_input)
    
    # Step B: Feed to model (Inference)
    # Note: .predict expects a list, so we wrap cleaned_input in []
    prediction_label = model.predict([cleaned_input])[0]
    
    # Step C: Get Confidence Score (How sure is the AI?)
    # .predict_proba returns probabilities for all classes. We take the max.
    confidence_score = model.predict_proba([cleaned_input]).max()
    
    return prediction_label, confidence_score, cleaned_input

# --- 4. INTERACTIVE LOOP ---
print("\n" + "="*40)
print("   iDorm INTENT TESTER (Type 'quit' to exit)")
print("="*40)

while True:
    user_text = input("\nUser says: ")
    
    if user_text.lower() in ['quit', 'exit', 'stop']:
        print("Exiting...")
        break
    
    if not user_text.strip():
        continue
        
    intent, confidence, debug_clean = predict(user_text)
    
    # Output Formatting
    print(f"   Stats: [Cleaned: '{debug_clean}']")
    print(f"   🤖 AI Prediction: {intent.upper()}")
    print(f"   📊 Confidence:    {confidence:.2%}")
    
    # Brute Force Feedback
    if confidence < 0.7:
        print("   ⚠️ WARNING: Low confidence. The model is guessing.")