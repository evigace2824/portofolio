import streamlit as st
import pandas as pd
import numpy as np
import joblib
import heapq

# Page configuration

st.set_page_config(
    page_title="Clinical Decision Support System",
    page_icon="🏥",
    layout="wide",
    initial_sidebar_state="expanded"
)

#CSS Theme

st.markdown("""
<style>
    /* Professional Medical Theme */
    :root {
        --primary: #0066CC;      /* Medical Blue - Trust */
        --secondary: #008080;    /* Teal - Healing */
        --accent-red: #D32F2F;   /* Alert Red */
        --accent-orange: #FF9800;/* Warning Orange */
        --accent-green: #388E3C; /* Stable Green */
        --background: #F5F9FF;   /* Light Blue Background */
        --card-bg: #FFFFFF;      /* White Cards */
        --text-primary: #1A237E; /* Dark Blue Text */
        --text-secondary: #546E7A;
        --border: #E3F2FD;
    }

    /* Main App Styling */
    .stApp {
        background-color: var(--background);
    }

    /* Header Styling */
    .main-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 2rem 1rem;
        border-radius: 0 0 20px 20px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 102, 204, 0.15);
        text-align: center;
    }

    .main-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .main-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 800px;
        margin: 0 auto;
    }

    /* Section Headers */
    .section-header {
        color: var(--text-primary);
        font-size: 1.8rem;
        font-weight: 600;
        margin: 2.5rem 0 1.5rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--secondary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Patient Cards */
    .patient-monitoring-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1rem 0;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border-left: 6px solid var(--primary);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .patient-monitoring-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .patient-critical {
        border-left: 6px solid var(--accent-red);
        background: linear-gradient(135deg, #FFEBEE 0%, #FFCDD2 100%);
    }

    .patient-warning {
        border-left: 6px solid var(--accent-orange);
        background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
    }

    .patient-stable {
        border-left: 6px solid var(--accent-green);
        background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-right: 10px;
    }

    .badge-critical {
        background: var(--accent-red);
        color: white;
    }

    .badge-warning {
        background: var(--accent-orange);
        color: white;
    }

    .badge-stable {
        background: var(--accent-green);
        color: white;
    }

    /* Vital Signs Display */
    .vital-container {
        background: var(--background);
        border-radius: 12px;
        padding: 15px;
        margin: 10px 0;
        border: 2px solid var(--border);
    }

    .vital-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .vital-value {
        color: var(--text-primary);
        font-size: 1.3rem;
        font-weight: 700;
    }

    .vital-unit {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-left: 5px;
    }

    /* Risk Indicators */
    .risk-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .risk-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
    }

    .risk-high { background-color: var(--accent-red); }
    .risk-medium { background-color: var(--accent-orange); }
    .risk-low { background-color: var(--accent-green); }

    /* Visit Sequence */
    .visit-step {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1.2rem;
        margin: 0.8rem 0;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border: 2px solid var(--border);
        transition: all 0.3s ease;
    }

    .visit-step:hover {
        border-color: var(--primary);
        box-shadow: 0 5px 15px rgba(0, 102, 204, 0.1);
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1.1rem;
        margin-right: 15px;
    }

    /* Metrics Cards */
    .metric-card {
        background: linear-gradient(135deg, var(--card-bg) 0%, #F8FBFF 100%);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border);
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary);
        margin: 10px 0;
    }

    .metric-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Buttons */
    .stButton > button {
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
    }

    .stButton > button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
    }

    /* Data Tables */
    .dataframe {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    /* Footer */
    .system-footer {
        text-align: center;
        color: var(--text-secondary);
        padding: 2rem 0 1rem 0;
        margin-top: 3rem;
        border-top: 1px solid var(--border);
        font-size: 0.9rem;
    }

    /* Tooltips/Info */
    .info-note {
        background: #E3F2FD;
        border-left: 4px solid var(--primary);
        padding: 1rem;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
</style>
""", unsafe_allow_html=True)


#Load model and feature list

import os
import joblib

@st.cache_resource
def load_model():
    base_dir = os.path.dirname(__file__)
    
    model_path = os.path.join(base_dir, "rf_deterioration_model.pkl")
    features_path = os.path.join(base_dir, "feature_columns.pkl")

    model = joblib.load(model_path)
    feature_cols = joblib.load(features_path)

    return model, feature_cols
    
model, feature_cols = load_model()

#Load patient database
import os
import pandas as pd

BASE_DIR = os.path.dirname(__file__)

patients_path = os.path.join(BASE_DIR, "patients_db.csv")
patients_db = pd.read_csv(patients_path)


if "edit_mode" not in st.session_state:
    st.session_state.edit_mode = {}


# NEWS2 calculation

def calculate_news2(row):
    score = 0

    # Respiratory rate
    if row["respiratory_rate"] <= 8 or row["respiratory_rate"] >= 25:
        score += 3
    elif 9 <= row["respiratory_rate"] <= 11 or 21 <= row["respiratory_rate"] <= 24:
        score += 2

    # SpO2
    if row["spo2_pct"] <= 91:
        score += 3
    elif row["spo2_pct"] <= 93:
        score += 2
    elif row["spo2_pct"] <= 95:
        score += 1

    # Heart rate
    if row["heart_rate"] <= 40 or row["heart_rate"] >= 131:
        score += 3
    elif 41 <= row["heart_rate"] <= 50 or 111 <= row["heart_rate"] <= 130:
        score += 2
    elif 91 <= row["heart_rate"] <= 110:
        score += 1

    # Systolic BP
    if row["systolic_bp"] <= 90:
        score += 3
    elif row["systolic_bp"] <= 100:
        score += 2
    elif row["systolic_bp"] <= 110:
        score += 1

    # Temperature
    if row["temperature_c"] <= 35.0 or row["temperature_c"] >= 39.1:
        score += 2
    elif 38.1 <= row["temperature_c"] <= 39.0:
        score += 1

    return score

#Greedy A* prioritization
def astar_prioritize(patients, nurse_ward):
    pq = []

    for pid, p in patients.items():
        travel_time = abs(p["ward"] - nurse_ward) * 5 + 2
        urgency = (1 - p["risk"]) + 0.01 * p["age"]
        cost = travel_time + urgency
        heapq.heappush(pq, (cost, pid))

    return [heapq.heappop(pq)[1] for _ in range(len(pq))]

# Header
st.markdown("""
<div class="main-header">
    <h1 class="main-title">Clinical Decision Support System</h1>
    <p class="main-subtitle">AI-powered patient monitoring and nurse prioritization for optimal clinical workflow</p>
</div>
""", unsafe_allow_html=True)


# SYSTEM OVERVIEW METRICS

st.markdown('<div class="section-header">System Overview</div>', unsafe_allow_html=True)
c1, c2 = st.columns(2)

with c1:
    st.markdown(f"<div class='metric-card'><div class='metric-value'>{len(patients_db)}</div><div class='metric-label'>Active Patients</div></div>", unsafe_allow_html=True)
with c2:
    st.markdown(f"<div class='metric-card'><div class='metric-value'>{patients_db['ward'].nunique()}</div><div class='metric-label'>Wards</div></div>", unsafe_allow_html=True)

# PATIENT MONITORING SECTION
st.markdown('<div class="section-header">Patient Monitoring Dashboard</div>', unsafe_allow_html=True)

st.markdown("""
<div class="info-note">
    <strong>Instructions:</strong> Update vital signs for each patient below. The system will automatically recalculate NEWS2 scores and deterioration risks.
</div>
""", unsafe_allow_html=True)

updated_rows = []


for idx, row in patients_db.iterrows():
    pid = row["patient_id"]

    is_editing = st.session_state.edit_mode.get(pid, False)


  # placeholder for the patient card
    card_placeholder = st.empty()

# Vital signs inputs
    col1, col2, col3 = st.columns(3)

    with col1:
        st.markdown("#### Cardiac & Respiratory")
        heart_rate = st.number_input(
            "Heart Rate (bpm)",
            min_value=30,
            max_value=200,
            value=int(row["heart_rate"]),
            key=f"hr_{pid}",
            disabled=not is_editing
        )


        respiratory_rate = st.number_input(
            "Respiratory Rate",
            min_value=5,
            max_value=60,
            value=int(row["respiratory_rate"]),
            key=f"rr_{pid}",
            disabled=not is_editing
        )

    with col2:
        st.markdown("#### Oxygenation")
        spo2_pct = st.number_input(
            "SpO₂ (%)",
            min_value=50,
            max_value=100,
            value=int(row["spo2_pct"]),
            key=f"spo2_{pid}",
            disabled=not is_editing
        )

        systolic_bp = st.number_input(
            "Systolic BP (mmHg)",
            min_value=60,
            max_value=250,
            value=int(row["systolic_bp"]),
            key=f"sbp_{pid}",
            disabled=not is_editing
        )

    with col3:
        st.markdown("#### Temperature")
        temperature_c = st.number_input(
            "Temperature (°C)",
            min_value=34.0,
            max_value=42.0,
            value=float(row["temperature_c"]),
            step=0.1,
            key=f"temp_{pid}",
            disabled=not is_editing
        )

    if not is_editing:
        if st.button("Edit", key=f"edit_{pid}"):
            st.session_state.edit_mode[pid] = True
            st.rerun()
    else:
        if st.button("Save", key=f"save_{pid}"):
            mask = patients_db["patient_id"] == pid

            patients_db.loc[mask, [
                "heart_rate",
                "respiratory_rate",
                "spo2_pct",
                "temperature_c",
                "systolic_bp"
            ]] = [
                heart_rate,
                respiratory_rate,
                spo2_pct,
                temperature_c,
                systolic_bp
            ]

            patients_db.to_csv("patients_db.csv", index=False)
            st.session_state.edit_mode[pid] = False
            st.success(f"Saved updates for Patient {pid}")
            st.rerun()


    live_row = row.copy()
    live_row["heart_rate"] = heart_rate
    live_row["respiratory_rate"] = respiratory_rate
    live_row["spo2_pct"] = spo2_pct
    live_row["systolic_bp"] = systolic_bp
    live_row["temperature_c"] = temperature_c

    
    initial_news2 = calculate_news2(live_row)

    # card class based on NEWS2
    if initial_news2 >= 7:
        card_class = "patient-critical"
        status_badge = '<span class="status-badge badge-critical">CRITICAL</span>'
    elif initial_news2 >= 5:
        card_class = "patient-warning"
        status_badge = '<span class="status-badge badge-warning">WARNING</span>'
    else:
        card_class = "patient-stable"
        status_badge = '<span class="status-badge badge-stable">STABLE</span>'

    #patient card
    with card_placeholder:
      st.markdown(f"""
    <div class="patient-monitoring-card {card_class}">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div>
                <h3 style="margin: 0 0 8px 0; color: #1A237E;">Patient {row['patient_id']}</h3>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                    {status_badge}
                    <span style="color: #546E7A; font-weight: 500;">Ward {row['ward']} • Age {row['age']}</span>
                </div>
                <div style="color: #78909C; font-size: 0.9rem;">
                    Admission: {row['admission_type']}
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 2rem; font-weight: 800; color: #1A237E;">
                    {initial_news2}
                </div>
                <div style="color: #78909C; font-size: 0.85rem;">Current NEWS2</div>
            </div>
        </div>
    """, unsafe_allow_html=True)


    st.markdown("</div>", unsafe_allow_html=True)
    st.markdown("<div style='height: 20px;'></div>", unsafe_allow_html=True)

    updated = live_row.copy()
    updated["heart_rate"] = heart_rate
    updated["respiratory_rate"] = respiratory_rate
    updated["spo2_pct"] = spo2_pct
    updated["temperature_c"] = temperature_c
    updated["systolic_bp"] = systolic_bp

    updated_rows.append(updated)

df = pd.DataFrame(updated_rows)


# Prediction 
X = pd.get_dummies(df, drop_first=True)

for col in feature_cols:
    if col not in X.columns:
        X[col] = 0

X = X[feature_cols]

df["NEWS2"] = df.apply(calculate_news2, axis=1)
df["risk"] = model.predict_proba(X)[:, 1]


# NURSE STARTING WARD

st.markdown('<div class="section-header"> Nurse Settings</div>', unsafe_allow_html=True)

nurse_start_ward = st.selectbox(
    "Nurse Starting Ward",
    sorted(df["ward"].unique()),
    key="nurse_start_ward"
)

col_n1, col_n2, col_n3 = st.columns([1, 2, 1])

with col_n2:
    st.markdown(f"""
    <div style="background: linear-gradient(135deg, #0066CC 0%, #008080 100%);
                color: white;
                padding: 1.5rem;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 6px 20px rgba(0, 102, 204, 0.2);
                margin: 1rem 0 2rem 0;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 10px;">
            <div style="font-size: 2.5rem;">👩‍⚕️</div>
            <div>
                <h3 style="margin: 0; color: white;">Current Shift Configuration</h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Nurse Starting Location</p>
            </div>
        </div>
        <div style="background: rgba(255,255,255,0.15);
                    padding: 15px;
                    border-radius: 10px;
                    margin-top: 15px;
                    border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Starting Ward</div>
            <div style="font-size: 3rem; font-weight: 800;">{nurse_start_ward}</div>
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 10px;">
                <div style="text-align: center;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Ward Type</div>
                    <div style="font-weight: 600;">{['ICU', 'Med/Surg', 'Emergency'][nurse_start_ward-1]}</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.3);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Patients in Ward</div>
                    <div style="font-weight: 600;">{len(patients_db[patients_db['ward'] == nurse_start_ward])}</div>
                </div>
            </div>
        </div>
        <div style="margin-top: 15px; font-size: 0.9rem; opacity: 0.9;">
            <span style="margin-right: 10px;">📍 Starting point for optimized route</span>
            <span>•</span>
            <span style="margin-left: 10px;"> Navigation calculations based on this location</span>
        </div>
    </div>
    """, unsafe_allow_html=True)


# CLINICAL STATUS DASHBOARD
st.markdown('<div class="section-header"> Clinical Status Dashboard</div>', unsafe_allow_html=True)

# Count of statuses
critical_count = len(df[df["NEWS2"] >= 7])
warning_count = len(df[(df["NEWS2"] >= 5) & (df["NEWS2"] < 7)])
stable_count = len(df[df["NEWS2"] < 5])

# Status summary
col1, col2, col3 = st.columns(3)
with col1:
    st.markdown(f"""
    <div class="metric-card">
        <div class="metric-value" style="color: #D32F2F;">{critical_count}</div>
        <div class="metric-label">Critical Patients</div>
    </div>
    """, unsafe_allow_html=True)

with col2:
    st.markdown(f"""
    <div class="metric-card">
        <div class="metric-value" style="color: #FF9800;">{warning_count}</div>
        <div class="metric-label">Warning Patients</div>
    </div>
    """, unsafe_allow_html=True)

with col3:
    st.markdown(f"""
    <div class="metric-card">
        <div class="metric-value" style="color: #388E3C;">{stable_count}</div>
        <div class="metric-label">Stable Patients</div>
    </div>
    """, unsafe_allow_html=True)

# Risk overview

st.header(" 12-Hour Deterioration Risk (ML)")

st.dataframe(
    df[["patient_id", "ward", "age", "NEWS2", "risk"]],
    use_container_width=True
)


# A* OPTIMIZED VISIT SEQUENCE
st.markdown('<div class="section-header"> AI-Optimized Visit Sequence</div>', unsafe_allow_html=True)

st.markdown("""
<div class="info-note">
    <strong>A* Search Algorithm:</strong> Visit sequence optimized using A* search considering travel distance, clinical risk, and patient vulnerability.
</div>
""", unsafe_allow_html=True)


critical_ids = df[df["NEWS2"] >= 7]["patient_id"].tolist()
non_critical_ids = df[df["NEWS2"] < 7]["patient_id"].tolist()

def build_dict(ids):
    return {
        r["patient_id"]: {
            "risk": r["risk"],
            "ward": r["ward"],
            "age": r["age"]
        }
        for _, r in df.iterrows()
        if r["patient_id"] in ids
    }

critical_dict = build_dict(critical_ids)
non_critical_dict = build_dict(non_critical_ids)

visit_order = []
current_ward = nurse_start_ward

if critical_dict:
    critical_order = astar_prioritize(critical_dict, current_ward)
    visit_order.extend(critical_order)
    current_ward = critical_dict[critical_order[-1]]["ward"]

if non_critical_dict:
    remaining_order = astar_prioritize(non_critical_dict, current_ward)
    visit_order.extend(remaining_order)

#optimized route
st.markdown("### Optimized Visit Route")
total_time = 0
current_ward = nurse_start_ward

for i, pid in enumerate(visit_order):
    p = df[df["patient_id"] == pid].iloc[0]
    travel = abs(p["ward"] - current_ward) * 5 + 2
    total_time += travel
    
    risk_pct = p["risk"] * 100

    if risk_pct >= 70:
        risk_color = "#D32F2F"
        risk_label = "HIGH Risk to Deteriorate"
        risk_icon = "🔴"
    elif risk_pct >= 30:
        risk_color = "#FF9800"
        risk_label = "MODERATE Risk to Deteriorate"
        risk_icon = "🟡"
    else:
        risk_color = "#388E3C"
        risk_label = "LOW Risk to Deteriorate"
        risk_icon = "🟢"


    st.markdown(f"""
    <div class="visit-step">
        <div style="display: flex; align-items: center;">
            <span class="step-number">{i+1}</span>
            <div style="flex-grow: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h4 style="margin: 0; color: #1A237E;">{pid} → Ward {p['ward']}</h4>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: {risk_color}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem;">
                            {risk_icon} {risk_label}
                        </span>
                        <span style="font-weight: 700; color: {risk_color};">{risk_pct:.1f}%</span>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; color: #546E7A;">
                    <div>
                        <div class="vital-label">NEWS2 Score</div>
                        <div class="vital-value">{p['NEWS2']}</div>
                    </div>
                    <div>
                        <div class="vital-label">Travel Time</div>
                        <div class="vital-value">{travel}<span class="vital-unit">min</span></div>
                    </div>
                    <div>
                        <div class="vital-label">From Ward</div>
                        <div class="vital-value">{current_ward if i>0 else 'Start'}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    """, unsafe_allow_html=True)

    current_ward = p["ward"]

# FOOTER
st.markdown("""
<div class="system-footer">
    <p style="margin: 0 0 10px 0; font-weight: 600;">Clinical Decision Support System</p>
    <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">
       Term Project 2026
    </p>
</div>
""", unsafe_allow_html=True)
