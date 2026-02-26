from app.database import SessionLocal
from app import models

db = SessionLocal()

try:
    
    users = [
        models.User(name="Evelina", email="evelina@test.com", department="IT"),
        models.User(name="Krisli", email="krisli@test.com", department="Finance"),
        models.User(name="Olgeta", email="olgeta@test.com", department="HR"),
        models.User(name="Edmond", email="edmond@test.com", department="Management"),
        models.User(name="Bela", email="bela@test.com", department="Finance"),
        models.User(name="Sona", email="sona@test.com", department="HR"),
    ]

    for user in users:
        db.add(user)

    db.commit()

    print(" Users added successfully!")

    
    campaigns = [
        models.Campaign(name="Password Reset Attack", template_type="Reset", difficulty=3),
        models.Campaign(name="Fake Invoice", template_type="Finance", difficulty=5),
        models.Campaign(name="HR Policy Update", template_type="HR", difficulty=4),
    ]

    for campaign in campaigns:
        db.add(campaign)

    db.commit()

    print("Campaigns added successfully!")

finally:
    db.close()
