# Clinical Decision Support System  
---

## Project Overview

This project implements an **AI-powered clinical decision support system** designed to assist hospital staff in identifying high-risk patients and optimizing nurse visit schedules.

The system combines:
- **Machine Learning** for short-term patient deterioration prediction
- **Clinical rules (NEWS2)** for real-time severity assessment
- **Search-based planning (Greedy A*)** for efficient nurse visit prioritization

The goal is to improve **patient safety**, **resource utilization**, and **workflow efficiency**, while keeping humans fully in control of clinical decisions.

---


## Dataset Source

The training dataset used in this project is the **Hospital Clinical Deterioration Dataset**
obtained from **Kaggle**.

#### Due to file size limitations, the dataset is not included in this repository and must be downloaded manually before running the training notebook.
---

## AI Approach Summary

### 1. Machine Learning (Risk Prediction)
- **Task:** Binary classification  
- **Target:** Patient deterioration within the next 12 hours  
- **Model used:** Random Forest (final model)  
- **Comparison model:** XGBoost  

Random Forest was selected for deployment due to:
- Stable and reliable probability estimates
- Robustness to noise and class imbalance
- Suitability for real-time decision support

The model outputs **probabilistic risk scores**, which are used for patient prioritization rather than only binary decisions.

---

### 2. Clinical Safety Layer (NEWS2)
- NEWS2 (National Early Warning Score 2) is a **clinically validated scoring system**
- Reflects the patient’s **current physiological condition**
- Complements ML predictions of **future deterioration**
- Improves interpretability and trust for clinical staff

---

### 3. Planning & Optimization (Greedy A*)
- A **Greedy A*-style planning algorithm** is used
- Computes an optimized nurse visit sequence
- Cost function combines:
  - Travel time between wards
  - Medical urgency (ML risk + patient age)

This approach enables **fast, dynamic replanning**, making it suitable for real hospital environments.

---

## Graphical User Interface (GUI)

The GUI is built using **Streamlit** and allows:
- Viewing patient status and risk levels
- Editing vital signs in real time
- Automatic recalculation of:
  - NEWS2 scores
  - ML-based deterioration risk
  - Optimized nurse visit sequence

The GUI uses a **small demo patient database** for clarity and interaction.  
In a real deployment, it would be connected to live hospital systems.

---

## Project Structure

```text
.
├── model_training/
│   ├── train_model.ipynb
│   └── hospital_deterioration_ml_ready.csv
│
├── gui/
│   ├── app.py
│   ├── patients_db.csv
│   ├── rf_deterioration_model.pkl
│   └── feature_columns.pkl
│
├── report/
│   └── AI_Project_Report.pdf
│
├── README.md
├── requirements.txt
└── .gitignore
```
## How to Run the Project (Step-by-Step)

This section explains how to download, set up, and run the Clinical Decision
Support System locally.

---
## First Way:  
###  Streamlit Machine Learning Application

This project is a Streamlit-based web application that uses a machine learning model trained on a custom dataset. The model must be trained before running the application.

---

## Dataset Setup & Running the Project

### 1. Download the Dataset
Download the dataset from the following Google Drive link:

**Dataset:**  
https://drive.google.com/file/d/1O3vETFRNkBsz1Kkaa7AIEyVs_2hPSUBt/view?usp=sharing

Save the file as a `.csv` file on your local machine.

---

### 2. Open the Project in GitHub Codespaces
1. Open this repository on GitHub.
2. Click **Code → Codespaces → Create codespace on main**.
3. Wait for the Codespace environment to load.

### 3. Upload the Dataset to the Project
1. In the Codespaces file explorer, navigate to: model_training/
2. Upload the downloaded `.csv` dataset file into this folder.

After uploading, the folder structure should look like:
```text
model_training/
├── your_dataset_name.csv
├── train_model.ipynb
```
---

### 4. Train the Machine Learning Model
1. Open the notebook: model_training/train_model.ipynb
2. Run **all cells** in order.
3. The training process will generate a trained model file (e.g.):  rf_deterioration_model.pkl
### 5. Run the Streamlit Application
From the project root directory, run the following command:

```bash
python -m streamlit run gui/app.py
```

## Second Way
### Step 1: Clone the GitHub Repository

Open a terminal and clone the project repository using:

```bash
git clone https://github.com/EPK-COURSES/cen352-term-project-2025-26-belina-elisona-evelina.git
```
## Step 2: Navigate to the Project Folder

Move into the project root directory:
```bash
cd cen352-term-project-2025-26-belina-elisona-evelina
```
The project root directory contains:
- requirements.txt
- model_training/
- gui/
- README.md

Usually it will be saved to C:\Users\User\TermProject

## Step 3: Install Dependencies

Install all required Python libraries by running:
```bash
pip install -r requirements.txt
```
This installs the dependencies needed for both model training and the GUI.

## Step 4: Download the Dataset
The training dataset is not included in the repository due to file size limitations.
1. Download the Hospital Clinical Deterioration Dataset from this link: https://drive.google.com/file/d/1O3vETFRNkBsz1Kkaa7AIEyVs_2hPSUBt/view?usp=sharing
2. Place the dataset file (hospital_deterioration_ml_ready.csv)
inside the model_training/ folder.

## Prerequisite 
Environment Requirements
This project was developed and tested using:
- **Python version:** 3.13

To avoid dependency and kernel mismatch issues, it is strongly recommended to
use **Python 3.13** when running the training notebook and the GUI.

## Step 5: Train the Machine Learning Model
Open the notebook:
```bash
model_training/train_model.ipynb
```
Run all cells in order.
Running the notebook will:
- Preprocess the dataset
- Train Random Forest and XGBoost models
- Evaluate model performance
- Generate the following files locally:
- rf_deterioration_model.pkl
- feature_columns.pkl

### Step 6: Run the Graphical User Interface (GUI)

After the model files have been generated and saved into the `gui/` folder,
the Streamlit application can be started as follows:

1. Open the project folder in **File Explorer**.
2. Navigate to the `gui/` folder.
3. **Right-click** inside the `gui/` folder and select **“Open in Terminal”**
   (or **“Open in Command Prompt” / “Open PowerShell window here”** on Windows).
4. In the opened terminal, run:

```bash
python -m streamlit run app.py
```
The application will open in the web browser and display the patient monitoring
dashboard, deterioration risk predictions, and optimized nurse visit sequence.
