from fastapi import FastAPI, Depends
from sqlalchemy.orm import Session
from .database import engine, SessionLocal, Base
from . import models, schemas
from app import ml_model

Base.metadata.create_all(bind=engine)

app = FastAPI()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

@app.get("/")
def root():
    return {"message": "AI Phishing Simulator Running "}

@app.post("/users")
def create_user(user: schemas.UserCreate, db: Session = Depends(get_db)):
    db_user = models.User(**user.dict())
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user

@app.post("/campaigns")
def create_campaign(campaign: schemas.CampaignCreate, db: Session = Depends(get_db)):
    db_campaign = models.Campaign(**campaign.dict())
    db.add(db_campaign)
    db.commit()
    db.refresh(db_campaign)
    return db_campaign

@app.post("/events")
def create_event(event: schemas.EventCreate, db: Session = Depends(get_db)):

    db_event = models.Event(**event.dict())
    db.add(db_event)

    user = db.query(models.User).filter(models.User.id == event.user_id).first()

    if event.event_type == "link_clicked":
        user.risk_score += 10

    elif event.event_type == "credentials_entered":
        user.risk_score += 25

    db.commit()
    db.refresh(user)

    return {"message": "Event recorded", "new_risk_score": user.risk_score}
@app.get("/predict/{user_id}/{campaign_id}")
def predict_user_behavior(user_id: int, campaign_id: int, db: Session = Depends(get_db)):

    user = db.query(models.User).filter(models.User.id == user_id).first()
    campaign = db.query(models.Campaign).filter(models.Campaign.id == campaign_id).first()

    if not user:
        return {"error": "User not found"}

    if not campaign:
        return {"error": "Campaign not found"}

    probability = ml_model.predict_click_probability(
        user.department,
        campaign.difficulty,
        user.risk_score
    )

    return {
        "user": user.name,
        "click_probability": probability
    }
@app.get("/debug/users")
def debug_users(db: Session = Depends(get_db)):
    users = db.query(models.User).all()
    return users