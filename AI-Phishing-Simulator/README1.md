# AI Phishing Simulator

## Overview

**AI Phishing Simulator** is a cybersecurity-focused application that models phishing campaign dynamics and predicts user interaction behavior using Machine Learning.

The system provides a structured environment where administrators can:

- Manage users  
- Design phishing campaigns  
- Record behavioral events  
- Analyze risk through AI-based probability estimation  

This project demonstrates the integration of:

- Secure API design  
- Data persistence & modeling  
- Behavioral analytics  
- Machine Learning prediction  

---

# Core Capabilities

## User Management
Create and manage user profiles with behavioral attributes.

## Campaign Management
Define phishing campaigns with configurable difficulty levels.

## Event Logging System
Record user interaction events (clicks / non-clicks).

## AI-Based Click Probability Prediction
Estimate phishing susceptibility using **Logistic Regression**.

## RESTful API Architecture
Fully implemented with **FastAPI**.

## Database Integration
Built with **SQLite + SQLAlchemy ORM**.

---

# Machine Learning Component

The system implements a **Logistic Regression** model to estimate:

> The probability that a user clicks a phishing email.

## Prediction Inputs

The behavioral model uses simplified risk indicators:

- Department  
- Campaign Difficulty  
- User Risk Score  

## Output

- **Click Probability (0 → 1)**

This models user susceptibility as a probabilistic classification problem.

---

# System Architecture

The application follows a layered architecture:

```
Database Layer     → SQLite + SQLAlchemy
API Layer          → FastAPI
ML Layer           → Logistic Regression
Client Interface   → Swagger UI (/docs)
```

This separation ensures:

- Modularity  
- Maintainability  
- Scalability  

---

# Technologies Used

- Python  
- FastAPI  
- SQLAlchemy  
- SQLite  
- Scikit-learn  
- Uvicorn  

---

# Project Structure

```
AI_Phishing_Simulator/
│── app/
│   │── main.py          # FastAPI application entry point
│   │── database.py      # Database configuration
│   │── models.py        # SQLAlchemy ORM models
│   │── schemas.py       # Pydantic validation schemas
│   │── ml_model.py      # Machine Learning logic
│
│── phishing.db          # SQLite database
│── requirements.txt     # Dependencies
│── run.py               # Application runner
```

---

# How to Run the Project

## 1. Install Dependencies

```bash
pip install -r requirements.txt
```

## 2. Start the Application

```bash
python run.py
```

## 3. Access API Interface

Open in your browser:

```
http://127.0.0.1:8000/docs
```

Swagger UI allows you to:

- Create Users  
- Create Campaigns  
- Log Events  
- Predict User Behavior  

---

# Analytical Perspective

This project models phishing susceptibility as a behavioral risk estimation problem, combining:

- Structured data modeling  
- Feature-based classification  
- Probabilistic prediction  

It illustrates how AI techniques can support:

- Risk profiling  
- Behavioral pattern analysis  
- Decision-support systems  

---

