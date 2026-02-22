from thefuzz import process

VALID_ENTITIES = {
    "rooms": ["cws", "theater", "sergun", "communal", "plaza", "dapur"],
    "actions": ["book", "cancel", "check", "reserve"],
}

def normalize_typos(text, threshold=90): # INCREASED THRESHOLD to 90 (was 80)
    words = text.split()
    corrected_words = []

    # Stopwords to ignore
    stopwords = ["at", "to", "for", "in", "on", "the", "a", "an", "room", "is", "of"]

    for word in words:
        clean_word = word.lower()
        
        if len(clean_word) < 4 or clean_word in stopwords:
            corrected_words.append(word)
            continue
            
        # Check Rooms
        match_room, score_room = process.extractOne(clean_word, VALID_ENTITIES["rooms"])
        
        # Check Actions
        match_action, score_action = process.extractOne(clean_word, VALID_ENTITIES["actions"])

        final_word = word 

        if score_room >= threshold:
            final_word = match_room
        elif score_action >= threshold:
            final_word = match_action
        
        corrected_words.append(final_word)

    return " ".join(corrected_words)