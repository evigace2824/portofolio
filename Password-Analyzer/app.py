from flask import Flask, render_template, request
import math
import string

app = Flask(__name__)


def calculate_entropy(password):
    pool = 0

    if any(c.islower() for c in password):
        pool += 26
    if any(c.isupper() for c in password):
        pool += 26
    if any(c.isdigit() for c in password):
        pool += 10
    if any(c in string.punctuation for c in password):
        pool += 32

    if pool == 0:
        return 0

    entropy = len(password) * math.log2(pool)
    return round(entropy, 2)


def estimate_crack_time(entropy):
    guesses_per_second = 1e9
    seconds = (2 ** entropy) / guesses_per_second

    if seconds < 60:
        return f"{round(seconds, 2)} seconds"
    elif seconds < 3600:
        return f"{round(seconds / 60, 2)} minutes"
    elif seconds < 86400:
        return f"{round(seconds / 3600, 2)} hours"
    else:
        return f"{round(seconds / 86400, 2)} days"


def get_strength(entropy):
    if entropy < 40:
        return "Weak", "red"
    elif entropy < 60:
        return "Moderate", "orange"
    else:
        return "Strong", "green"


@app.route("/", methods=["GET", "POST"])
def index():
    result = None

    if request.method == "POST":
        password = request.form["password"]

        entropy = calculate_entropy(password)
        crack_time = estimate_crack_time(entropy)
        strength, color = get_strength(entropy)

        issues = []
        if len(password) < 8:
            issues.append("Too short")
        if not any(c.islower() for c in password):
            issues.append("Missing lowercase letters")
        if not any(c.isupper() for c in password):
            issues.append("Missing uppercase letters")
        if not any(c.isdigit() for c in password):
            issues.append("Missing numbers")
        if not any(c in string.punctuation for c in password):
            issues.append("Missing special characters")

        result = {
            "entropy": entropy,
            "crack_time": crack_time,
            "strength": strength,
            "color": color,
            "issues": issues
        }

    return render_template("index.html", result=result)


if __name__ == "__main__":
    app.run(debug=True)
