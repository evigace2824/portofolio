# FitFlex – Fitness Studio Management System

## 1. Project Overview

FitFlex is a PHP/MySQL–driven fitness studio website that allows customers to browse classes, book and pay for sessions, manage personal profiles, and subscribe to newsletters, while administrators manage users, trainers, and classes.

---

## 2. Technology Stack

• Backend: PHP 8.2 (procedural), using mysqli with prepared statements  
• Database: MariaDB 10.4 (“fitflex_db”) with InnoDB tables  
• Frontend: HTML5, Bootstrap 5, custom CSS, Google Fonts (Poppins & Montserrat), Font Awesome  

• Security:  
Passwords hashed (bcrypt)  
Card data encrypted/decrypted via OpenSSL in includes/crypto.php  

• Project Structure:  
/includes – shared DB connection and crypto functions  
/assets – CSS (style.css), JS (script.js), images (classes & trainers)  
Root PHP scripts for pages and admin actions (CRUD)  

---

## 3. Key Features & Pages

### Authentication (login.php, signup.php, logout.php)
Customer/admin login with role checks and session management.

### Main & Explore (mainpage.php, explore.php)
Class listings with categories (Mind & Body, HIIT, Dance, etc.), search/filter UI, newsletter signup form.

### User Dashboard (userpage.php)
View & remove saved classes, book classes (posts to bookings table), manage profile (editprofile.php).

### Class Booking & Payment (payment.php, process_payment.php, test_cards.php)
Simulated payment flow using test card data, recording bookings in bookings.

### Admin Panel (admin.php)
Overview of users, trainers, classes; links to add/edit/delete for each entity.

### CRUD Operations

Users:
- add_user.php
- edit_user.php
- delete_user.php

Classes:
- add_class.php
- edit_class.php
- delete_class.php

Trainers:
- add_trainer.php
- edit_trainer.php
- delete_trainer.php

---

## 4. Database Schema Highlights

1. users – stores profile data, roles, hashed passwords  
2. bank_accounts – encrypted card details linked to users  
3. classes – fitness class metadata (name, category, description, image)  
4. trainers – trainer profiles (name, specialization, bio, rating)  
5. bookings – user↔class bookings with timestamp  
6. saved_classes – wishlist of classes for each user  
7. newsletter_subscribers – emails for studio newsletter  
8. nutrition_plans & tips – placeholders for future content (tables created but not yet surfaced in UI)  

---

## 5. User Experience & Design

• Responsive Layout: Bootstrap grid ensures mobile-friendly pages.  
• Visuals: Custom CSS styling for cards, buttons (border-radius, shadows), class/trainer images enhance engagement.  

• Interactivity:  
Tooltip support via Bootstrap’s JS  
Client-side form validation alerts  
Profile picture uploads under uploads/profile_pictures/  

---

## 6. Security 

• Prepared Statements prevent SQL injection.  
• Password Hashing with bcrypt.  
• Card Encryption using AES-256-CBC in includes/crypto.php.  
• Session Checks guard admin pages and booking actions.  

---

## 7. How to Run the Project

To run this project locally, follow these steps:

### Step 1 – Install XAMPP
Download and install XAMPP.  
Start both:
- Apache
- MySQL

### Step 2 – Import the Database

1. Open your browser and go to:
   http://localhost/phpmyadmin

2. Click "New" and create a database named:
   fitflex_db

3. Click on the newly created database.

4. Click "Import".

5. Select the file:
   database/fitflex_db.sql

6. Click "Go".

### Step 3 – Move Project to htdocs

Copy the folder:

fitnessstudio

Paste it inside:

C:\xampp\htdocs\

Final structure should be:

C:\xampp\htdocs\fitnessstudio

### Step 4 – Open the Project

Open your browser and go to:

http://localhost/fitnessstudio

Make sure Apache and MySQL are running before accessing the project.


## 8. Conclusion

FitFlex delivers a full-featured, secure, and user-centric fitness studio platform that meets both customer and administrator needs. By leveraging PHP with prepared statements, a well-normalized MySQL schema, and modern frontend frameworks like Bootstrap, it ensures reliability, maintainability, and a responsive experience across devices. Key security measures—password hashing, AES-256-CBC encryption for payment data, and role-based session checks—help safeguard user information. The modular code structure and clear separation of concerns make it straightforward to extend: whether by surfacing nutrition content, integrating real payment gateways, or adding analytics dashboards. Overall, FitFlex stands as a robust foundation for a scalable studio management system and a springboard for future enhancements.

---
