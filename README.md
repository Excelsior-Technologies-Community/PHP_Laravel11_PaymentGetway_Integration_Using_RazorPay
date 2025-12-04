# Laravel 11 Payment Gateway Integration (Razorpay)

**By:** Manasi Patel  
**Date:** 2025  
**Laravel Version:** 11  

This project demonstrates a **Payment Management System** using Laravel 11 and Razorpay. Users can make payments, view active payments, and manage them with soft delete and restore functionality.  

---

## Features

- Payment form using Razorpay  
- Save payments in database (pending → success)  
- Display payments list (only active)  
- Delete payments with popup confirmation  
- Restore deleted payments (optional)  
- Centered Bootstrap design  

---

## Installation & Setup

### 1. Install Laravel 11 & Navigate to Project

```bash
composer create-project laravel/laravel laravel11-payment "^11.0"
cd laravel11-payment
2. Configure Database
Update .env file:

env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_app
DB_USERNAME=root
DB_PASSWORD=
Create database:

sql

CREATE DATABASE payment_app;
3. Install Razorpay SDK
bash

composer require razorpay/razorpay
4. Create Payments Table Migration
bash

php artisan make:migration create_payments_table --create=payments
php artisan migrate
5. Create Payment Model
bash

php artisan make:model Payment
6. Create Payment Controller
bash

php artisan make:controller PaymentController
7. Add Routes
Edit routes/web.php and define routes for payment form, processing, success callback, listing, deleting, and restoring payments.

8. Add Environment Variables for Razorpay
env

RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxx
9. Run the Application
bash

php artisan serve
Open browser:

bash

http://localhost:8000/payment
Workflow
User enters payment amount in the payment form.

On submission, a Razorpay order is created via API.

Payment is recorded in the database with status = pending.

Razorpay checkout popup opens.

After successful payment, payment status is updated to success.

Admin can view all payments.

Admin can soft delete payments or restore them.

Notes
SoftDeletes allows restoring deleted payments.

All payments are initially marked as pending.

Razorpay test keys are used for sandbox testing.

Replace hardcoded created_by and updated_by with Auth::id() in production.

All Commands in One Place
bash

# 1. Install Laravel
composer create-project laravel/laravel laravel11-payment "^11.0"
cd laravel11-payment

# 2. Install Razorpay SDK
composer require razorpay/razorpay

# 3. Create Migration for Payments Table
php artisan make:migration create_payments_table --create=payments

# 4. Run Migrations
php artisan migrate

# 5. Create Payment Model
php artisan make:model Payment

# 6. Create Payment Controller
php artisan make:controller PaymentController

# 7. Run Laravel Server
php artisan serve
