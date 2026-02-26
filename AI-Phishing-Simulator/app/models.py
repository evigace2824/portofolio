from sqlalchemy import Column, Integer, String, DateTime
from sqlalchemy.sql import func
from .database import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String)
    email = Column(String)
    department = Column(String)
    risk_score = Column(Integer, default=0)


class Campaign(Base):
    __tablename__ = "campaigns"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String)
    template_type = Column(String)
    difficulty = Column(Integer)
    created_at = Column(DateTime(timezone=True), server_default=func.now())


class Event(Base): 
    __tablename__ = "events"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer)
    campaign_id = Column(Integer)
    event_type = Column(String)
    timestamp = Column(DateTime(timezone=True), server_default=func.now())
