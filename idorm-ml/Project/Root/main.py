import pickle
import re
import uvicorn
import logging
import dateparser
import random
from datetime import datetime, timedelta
from notebooks.preprocessor import normalize_typos
from fastapi import FastAPI, HTTPException

app = FastAPI(title="iDorm Service")

from pydantic import BaseModel
from typing import Optional, List

# Define input format (JSON)
class ChatRequest(BaseModel):
    message: str

MODEL_FILE = 'Scripts/intent_model2.pkl'

try:
    with open(MODEL_FILE, 'rb') as f:
        model = pickle.load(f)
    print("Model loaded.")
except FileNotFoundError:
    print("Model file not found")
    exit()

logging.basicConfig(
    filename='chatbot_requests.log', 
    level=logging.INFO, 
    format='%(asctime)s - %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)

def clean_text(text):
    text = str(text).lower()
    text = re.sub(r'[^a-z0-9\.\s]', '', text) 
    return text

def extract_entities(text):
    text = text.lower()
    entities = {
        "room": None,
        "date": None,       # YYYY-MM-DD
        "date_original": None,
        "start_time": None,
        "end_time": None,
        "class_group": None,
        "time_warning": None # Flag jika user input menit selain .30 / .00
    }
    # Extract ROOM
    rooms = {
        "cws": "CWS", "co working": "CWS", "co-working": "CWS", "co working space": "CWS",
        "theater": "Theater", "theatre": "Theater",
        "sergun": "Sergun", "serbaguna": "Sergun", "serba guna": "Sergun",
        "plaza": "Plaza",
        "dapur": "Dapur", "kitchen": "Dapur", "pantry": "Dapur",
        "communal 1": "Communal 1", "communal 2": "Communal 2", "communal 3": "Communal 3", "communal 5": "Communal 5"
    }
    
    for key in sorted(rooms.keys(), key=len, reverse=True):
        if key in text:
            entities['room'] = rooms[key]
            break

    # Extract CLASS
    class_match = re.search(r'\b(ppti|ppbp|ti|bp)\s?(\d{1,2})\b', text)
    
    if class_match:
        raw_prefix = class_match.group(1).lower() # Ambil kata depannya (ti/bp)
        number = class_match.group(2)             # Ambil angkanya
        
        if raw_prefix in ['ti', 'ppti']:
            std_prefix = "PPTI"
        elif raw_prefix in ['bp', 'ppbp']:
            std_prefix = "PPBP"
        else:
            std_prefix = raw_prefix.upper()
        
        entities['class_group'] = f"{std_prefix} {number}"

    # Extract DATE
    date_regex = r'(\d{1,2}(?:st|nd|rd|th)?\s+(?:jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*)|(\d{1,2}[-/]\d{1,2}[-/]\d{2,4})|(tomorrow)|(next week)|(today)'
    
    date_match = re.search(date_regex, text)
    if date_match:
        try:
            parsed_date = dateparser.parse(date_match.group(0), settings={'DATE_ORDER': 'DMY', 'PREFER_DATES_FROM': 'future'})
            if parsed_date:
                entities['date'] = parsed_date.strftime('%Y-%m-%d')
        except:
            pass

    # Extract TIME
    found_times = []

    # XX.30 or XX:30
    regex_standard = re.findall(r'\b([0-1]?[0-9]|2[0-3])[\.:]([0-5][0-9])\b', text)
    for h, m in regex_standard:
        found_times.append((h, m))

    # "10 am" or "10pm" or "10am"
    regex_ampm = re.findall(r'\b(\d{1,2})\s*(am|pm)\b', text)
    for h, period in regex_ampm:
        h_int = int(h)
        if period == "pm" and h_int < 12: h_int += 12
        if period == "am" and h_int == 12: h_int = 0
        found_times.append((str(h_int), "00"))

    # "at 8"
    if not found_times:
        regex_at = re.findall(r'\bat\s+(\d{1,2})(?!\d|[\.:])', text)
        for h in regex_at:
            found_times.append((h, "00"))

    # Process found times
    if found_times:
        # Sort by hour to ensure start is before end
        found_times.sort(key=lambda x: int(x[0]))
        
        start_h, start_m = found_times[0]
        entities['start_time'] = f"{start_h.zfill(2)}.{start_m}"
        
        if start_m not in ['00', '30']:
            entities['time_warning'] = f"Sorry, start time {entities['start_time']} is invalid. Must be xx.00 or xx.30."

        if len(found_times) > 1:
            end_h, end_m = found_times[1]
            entities['end_time'] = f"{end_h.zfill(2)}.{end_m}"
            if end_m not in ['00', '30']:
                entities['time_warning'] = f"Sorry, end time {entities['end_time']} is invalid. Must be xx.00 or xx.30."

    # durasi handling
    if entities['start_time'] and not entities['end_time']:
        duration_match = re.search(r'(?:for)\s+(\d+(?:\.\d+)?)\s*(?:hours?|hrs?)', text)
        if duration_match:
            try:
                duration_hours = float(duration_match.group(1))
                start_dt = datetime.strptime(entities['start_time'], "%H.%M")
                end_dt = start_dt + timedelta(hours=duration_hours)
                entities['end_time'] = end_dt.strftime("%H.%M")
            except: pass

    return entities

@app.get("/")
def home():
    return {"status": "AI Server is running!", "endpoint": "/predict"}

@app.post("/predict")
def predict_intent(request: ChatRequest):
    # 1. Preprocessing (Fuzzy Logic)
    corrected_text = normalize_typos(request.message)
    clean = clean_text(corrected_text)
    
    # 2. Predict Intent
    intent = model.predict([clean])[0]
    confidence = model.predict_proba([clean]).max()
    
    logging.info(f"User Input: '{request.message}' | Predicted: {intent} (Conf: {confidence:.2%})")


    THRESHOLD = 0.65
    if confidence < THRESHOLD:
        intent = "unknown"

    # 3. Extract Entities
    entities = extract_entities(corrected_text)
    
    if entities['time_warning']:
        return {
            "status": "error",
            "data": {
                "intent": intent,
                "confidence": float(confidence),
                "entities": entities,
                "bot_reply": entities['time_warning'], 
                "original_text": request.message
            }
        }

    status = "success"
    missing_info = None
    bot_reply = "System processing..."

    if intent == "unknown":
        bot_reply = "I apologize, I did not understand your request. Could you please rephrase it clearly? (e.g., 'PPTI 22 wants to book CWS room for tomorrow from 08.00 to 10.00')"
        status = "error"

    elif intent == "greet":
        current_hour = datetime.now().hour
        time_greeting = "Good Morning"
        if 12 <= current_hour < 18:
            time_greeting = "Good Afternoon"
        elif 18 <= current_hour <= 23 or 0 <= current_hour < 5:
            time_greeting = "Good Evening"
            
        replies = [
            f"{time_greeting}! How can I assist you with iDorm facilities today?",
            f"{time_greeting}! Do you need to book a room?",
            "Hello! I am ready to help you with your scheduling.",
            "Hi there! Which facility would you like to use today?"
        ]
        bot_reply = random.choice(replies)
    
    elif intent == "goodbye":
        replies = [
            "Goodbye! Have a productive day.",
            "See you later! Feel free to chat again if you need anything.",
            "Signing off. Take care!",
            "Bye! Don't forget to check your booking status."
        ]
        bot_reply = random.choice(replies)

    elif intent == "thanks":
        replies = [
            "You're welcome! Happy to help.",
            "No problem at all!",
            "My pleasure.",
            "Anytime! Let me know if you need anything else."
        ]
        bot_reply = random.choice(replies)

    elif intent == "booking_request":

        # slot filling check
        missing_slots = []
        if entities['room'] is None: missing_slots.append("room name")
        if entities['date'] is None: missing_slots.append("date")
        if entities['start_time'] is None: missing_slots.append("start time")
        if entities['end_time'] is None: missing_slots.append("end time")
        
        if missing_slots:
            status = "incomplete"
            missing_info = missing_slots[0] 
            bot_reply = f"Apologies, the information regarding the {missing_info} is missing. Please provide the {missing_info} for your booking."
        else:
            bot_reply = f"I have received your request to book {entities['room']} on {entities['date']} at {entities['start_time']} till {entities['end_time']}. Checking availability now..."

    elif intent == "check_availability":
        bot_reply = "I am checking the facility schedule for you. Please hold on a moment..."
    
    elif intent == "cancel_booking":
        bot_reply = "I can help with that. Please provide the booking details you wish to cancel."

    # 6. Return JSON
    return {
        "status": status,
        "data": {
            "intent": intent,
            "confidence": float(confidence),
            "entities": entities,
            "missing_info": missing_info,
            "bot_reply": bot_reply,
            "original_text": request.message,
            "preprocessed_text": corrected_text
        }
    }
    
if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)