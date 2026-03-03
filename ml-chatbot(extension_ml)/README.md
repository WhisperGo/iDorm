# 🤖 iDorm ML Chatbot Service

> **NLP-powered Intent Classification Chatbot** for assisting dormitory residents with facility booking through natural language conversation. This microservice is fully containerized with Docker.

---

## 📋 Table of Contents
- [Overview](#overview)
- [Architecture](#architecture)
- [Supported Intents](#supported-intents)
- [Getting Started](#getting-started)
- [API Usage](#api-usage)
- [Integration Guide (For Main Web App)](#integration-guide-for-main-web-app)

---

## Overview

This chatbot microservice processes natural language messages from residents and provides intelligent responses for facility booking workflows. It supports:

- **Intent Classification**: Identifies user intent (booking request, check availability, greetings, etc.) using a trained scikit-learn model.
- **Fuzzy Typo Correction**: Automatically corrects misspelled room names and actions using `thefuzz` library.
- **Entity Extraction**: Extracts structured data (room name, date, start/end time) from free-text input.
- **Slot-Filling Dialogue**: Guides users to provide missing information when their booking request is incomplete.

---

## Architecture

```text
┌─────────────────────────────────────────────┐
│            CLIENT (Main Web App)            │
│              POST /predict                  │
└──────────────────┬──────────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────┐
│              FastAPI Service (:8001)         │
├──────────────────────────────────────────────┤
│  1. Fuzzy Typo Correction (thefuzz)          │
│  2. Intent Classification (scikit-learn)     │
│  3. Entity Extraction (regex + dateparser)   │
│  4. Slot-Filling Response Logic              │
└──────────────────────────────────────────────┘
```

### Tech Stack

| Component | Technology |
| :---: | :--- |
| **Runtime** | Python 3.11 |
| **API Framework** | FastAPI + Uvicorn |
| **ML Model** | scikit-learn (Pickle serialized) |
| **NLP Tools** | `thefuzz`, `dateparser`, `nltk` |
| **Containerization** | Docker |
| **Port** | `8001` |

---

## Supported Intents

| Intent | Description | Example |
| :---: | :--- | :--- |
| `booking_request` | User wants to book a facility | *"I want to book CWS tomorrow at 08.00 to 10.00"* |
| `check_availability` | User checks if a room is available | *"Is the theater available on Friday?"* |
| `greet` | General greeting | *"Hello"*, *"Hi"* |
| `goodbye` | Farewell message | *"Bye"*, *"See you"* |
| `thanks` | Expression of gratitude | *"Thank you!"* |
| `unknown` | Low-confidence / unrecognized input | *(API-only, not handled in web UI)* |

### Extractable Entities

| Entity | Format | Example |
| :---: | :--- | :--- |
| **Room** | CWS (Co-working Space), Theater, Sergun, Dapur | *"book CWS"* |
| **Date** | `YYYY-MM-DD` (parsed from natural language) | *"tomorrow"*, *"15 March", "15 Mar"* |
| **Start Time** | `HH.MM` (must end in `.00` or `.30`) | *"at 08.00"*, *"10am"* |
| **End Time** | `HH.MM` (or calculated from duration) | *"to 10.00"*, *"for 2 hours"* |

---

## Getting Started

### Prerequisites
- **[Docker Desktop](https://www.docker.com/products/docker-desktop/)** must be installed and running.

### 1. First-Time Launch (Build & Start)
Open your terminal in the `ml-chatbot(extension_ml)` directory and run:
```bash
docker-compose up --build -d
```
This will build the Docker image from scratch and start the container in the background.

### 2. Subsequent Launches (Without Rebuild)
If the image has already been built previously, you can start the service without rebuilding:
```bash
docker-compose up -d
```

### 3. Stop the Service
To stop the running chatbot container:
```bash
docker-compose down
```

### 4. Verify the Deployment
Once the container is running, verify the service is healthy:
- **Health Check**: [http://localhost:8001](http://localhost:8001)
  *(Should return `{"status": "AI Server is running!", "endpoint": "/predict"}`)*

---

## API Usage

**Endpoint:** `POST http://localhost:8001/predict`

**Request Body:**
```json
{
  "message": "I want to book CWS room for tomorrow from 08.00 to 10.00"
}
```

**Success Response:**
```json
{
  "status": "success",
  "data": {
    "intent": "booking_request",
    "confidence": 0.95,
    "entities": {
      "room": "CWS",
      "date": "2026-03-04",
      "start_time": "08.00",
      "end_time": "10.00",
      "class_group": null,
      "time_warning": null
    },
    "missing_info": null,
    "bot_reply": "I have received your request to book CWS on 2026-03-04 at 08.00 till 10.00. Checking availability now...",
    "original_text": "I want to book CWS room for tomorrow from 08.00 to 10.00",
    "preprocessed_text": "I want to book CWS room for tomorrow from 08.00 to 10.00"
  }
}
```

**Incomplete Request Response (Slot-Filling):**
```json
{
  "status": "incomplete",
  "data": {
    "intent": "booking_request",
    "confidence": 0.92,
    "entities": {
      "room": "CWS",
      "date": null,
      "start_time": "08.00",
      "end_time": "10.00",
      "class_group": null,
      "time_warning": null
    },
    "missing_info": "date",
    "bot_reply": "Apologies, the information regarding the date is missing. Please provide the date for your booking.",
    "original_text": "book CWS from 08.00 to 10.00",
    "preprocessed_text": "book CWS from 08.00 to 10.00"
  }
}
```