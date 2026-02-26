from sklearn.linear_model import LogisticRegression
import numpy as np


X = np.array([
    [1, 1, 5],   # IT, easy campaign, low risk
    [2, 3, 25],  # Finance, hard campaign, high risk
    [3, 2, 15],  # HR, medium campaign, medium risk
    [2, 1, 10],
    [1, 3, 30],
])

y = np.array([0, 1, 0, 0, 1])  # 0 = no click, 1 = click

model = LogisticRegression()
model.fit(X, y)


def predict_click_probability(department, difficulty, risk_score):

    dept_map = {
        "IT": 1,
        "Finance": 2,
        "HR": 3
    }

    dept_value = dept_map.get(department, 1)

    input_data = np.array([[dept_value, difficulty, risk_score]])

    probability = model.predict_proba(input_data)[0][1]

    return probability
