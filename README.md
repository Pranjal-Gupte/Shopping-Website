# Shopping Website Using Laravel 11 and php 8.4

This is a shopping website just like amazon, flipkart, etc. but using Laravel 11 and php 8.4 as it's core. The backend is handled by laravel and the frontend with HTML/Blade.

## Note: Website is still under basic development

## Keyfeatures
- User registration and login
- User profile management
- Product listing and filtering
- Product details and reviews
- Cart management
- Payment gateway integration
- Order management
- Admin panel for managing products, orders, and users
- Search functionality
- Responsive design

## Requirements
- Core Language: php 8.4
- Backend: Laravel 11
- Package Installer: Composer (for php/laravel), npm (for Node.js)
- Database: MySQL
- Frontend Build: Node.js
- Server: Apache, nginx.

## Installation
1. Install: Composer, npm & Node.js
1. Clone the repository:
1. Install php dependencies: `composer install`
1. Install Node.js dependencies: `npm install`
1. Setup configuration: `cp .env.example .env`
1. Generate key: `php artisan key:generate`
1. Create database and update your configuration in `.env`
1. Run database migrations: `php artisan migrate`
1. Run seeders: `php artisan db:seed`
1. Run Frontend Build: `npm run build`
1. Run Server: `php artisan serve`

> **Note: access the website at `http://localhost:8000`**

## Cart Service Provider Package
This project uses a custom made cart handler. To learn more about this you can go through [this](https://github.com/Pranjal-Gupte/Shopping-Cart/).

## Dummy Accounts

### Admin Account
- Email: `admin@example.com`
- Password: `password`

### User Account
- Email: `user@example.com`
- Password: `password`