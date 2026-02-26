import math
import string


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


def analyze_password(password):
    issues = []

    if len(password) < 8:
        issues.append("Too short (minimum 8 characters)")
    if not any(c.islower() for c in password):
        issues.append("Missing lowercase letters")
    if not any(c.isupper() for c in password):
        issues.append("Missing uppercase letters")
    if not any(c.isdigit() for c in password):
        issues.append("Missing numbers")
    if not any(c in string.punctuation for c in password):
        issues.append("Missing special characters")

    entropy = calculate_entropy(password)
    crack_time = estimate_crack_time(entropy)

    print("\n🔐 PASSWORD ANALYSIS RESULT")
    print("-" * 35)
    print(f"Password Length: {len(password)}")
    print(f"Entropy Score: {entropy} bits")
    print(f"Estimated Crack Time: {crack_time}")

    if issues:
        print("\n⚠️ Weaknesses:")
        for issue in issues:
            print(f" - {issue}")
    else:
        print("\n✅ Strong Password")


if __name__ == "__main__":
    password = input("Enter your password: ")
    analyze_password(password)