from pydantic import BaseModel

class UserCreate(BaseModel):
    name: str
    email: str
    department: str


class CampaignCreate(BaseModel):
    name: str
    template_type: str
    difficulty: int

class EventCreate(BaseModel):
    user_id: int
    campaign_id: int
    event_type: str
