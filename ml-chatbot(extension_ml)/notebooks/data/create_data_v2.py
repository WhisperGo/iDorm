import pandas as pd
import random

intents = {
    "booking_request": [
        # Direct Verbs
        "book {room}", "reserve {room}", "get {room}", "secure {room}", "grab {room}",
        "schedule {room}", "hold {room}", "keep {room}", "want {room}", "need {room}",
        # Full Sentences
        "I want to book {room}", "We need to reserve {room}", "Can I get {room}?",
        "Please secure {room} for us", "booking {room} for class",
        "PPTI {class_num} wants {room}", "PPBP {class_num} needs {room}",
        "reservation for {room} please", "put me down for {room}",
        # Time focused
        "{room} for tomorrow", "{room} on {date}", "booking {room} at {time}",
        "reserve {room} from {time} to {time}",
        # Short
        "book {room} pls", "need {room} asap", "{room} booking", 
        "reserving {room}", "book {room} now"
    ],
    "check_availability": [
        # Direct Questions
        "is {room} free?", "is {room} available?", "is {room} empty?", "is {room} vacant?",
        "is {room} occupied?", "is {room} open?", "can I use {room}?",
        # Checking status
        "check {room}", "check {room} status", "status of {room}",
        "any slots in {room}?", "when is {room} free?",
        "see if {room} is available", "availability {room}",
        # Time focused
        "is {room} free at {time}?", "can we use {room} on {date}?",
        "{room} available tomorrow?", "check schedule for {room}"
    ],
    "cancel_booking": [
        # Direct Verbs
        "cancel {room}", "drop {room}", "remove {room}", "delete {room}", "void {room}",
        "unbook {room}", "forget {room}", "abort {room}",
        # Full Sentences
        "cancel my booking", "I want to cancel reservation",
        "we don't need {room} anymore", "stop the booking for {room}",
        "please cancel {room}", "remove reservation for PPTI {class_num}",
        # Short
        "cancel booking", "cancel reservation", "cancel it", "delete booking"
    ],
    "greet": [
        "hi", "hello", "halo", "hey", "good morning", "good afternoon", "good evening",
        "hi there", "hello bot", "hey idorm", "greetings", "yo", "hi bot", "hallo"
    ],
    "goodbye": [
        "bye", "goodbye", "see you", "bye bye", 
        "see ya", "end chat", "quit", "exit", "have a nice day"
    ],
    "thanks": [
        "thank you", "thanks", "terima kasih", "makasih", "thx", "thank u",
        "thanks a lot", "thank you so much", "arigato", "thanks bot"
    ]
}

rooms = ["CWS", "Theater", "Sergun", "Communal 1", "Communal 2", "Plaza", "Dapur", "Serbaguna", "Coworking Space", "Communal 3", "Co working space", "Communal 5"]
dates = ["tomorrow", "next week", "18th Dec", "Monday", "12/12/2025", "today", "25th Dec", "3rd March", "5th Jan 2026", "10/01/2026", "19-01-2026", "2nd February 2026", "16th February 2026", "2018-12-25"]
times = ["08.00", "10.00", "1pm", "morning", "afternoon", "18 to 20", "09.30", "14:00-16:00", "13.00-15.00", "17.00 - 18.30", "07.30 to 09.30", "08:00-12:00"]
classes = ["20", "21", "22", "23", "24", "25", "7", "8", "9", "10"]

data = []

for intent_name, templates in intents.items():
    for _ in range(150):
        template = random.choice(templates)
        
        # Fill in the blanks dynamically
        text = template.format(
            room=random.choice(rooms),
            date=random.choice(dates),
            time=random.choice(times),
            class_num=random.choice(classes)
        )
        
        # 10% chance to add noise to make it realistic
        if random.random() < 0.5:
            text = text.lower()
        if random.random() < 0.3:
            text = text.replace("?", "").replace(".", "")
            
        data.append((text, intent_name))

df = pd.DataFrame(data, columns=["text", "intent"])
df = df.sample(frac=1).reset_index(drop=True) # Shuffle randomly

filename = 'dataset.csv'
df.to_csv(filename, index=False)

print(f"Generated {len(df)} rows.")
print("Sample:")
print(df.head(10))