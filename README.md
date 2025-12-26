# AeroClick-Ecommerce
513-finally work
AeroClick is a fully functional e-commerce website specializing in gaming mice, built as a student project for the ICTWEB513 unit - "Build dynamic websites". This project demonstrates comprehensive web development skills including PHP, MySQL, responsive design, and secure coding practices.
Live Demo：https://grant.fwh.is/513/week7/ecommerce-project/index.php
Student: Grant (Student ID: 233190711)
✨ Features
🛍️ E-commerce Functionality
Product Catalog with filtering by brand, DPI, connectivity, etc.

Product Comparison Tool - Compare up to 3 products side-by-side

Shopping Cart System with session persistence

Checkout Process with simulated payment

Order History for registered users

User Reviews with verified purchase system

👥 User Management
Registration System with email/phone validation

Login Integration with WordPress FluentCRM database

Profile Management and order tracking

Role-based Access Control (User/Admin)

💬 Community Features
Interactive Forum with categories and threading

Post Creation & Replies with user attribution

Admin Moderation tools for content management

View Counting and activity tracking

🔧 Technical Features
Responsive Design - Works on mobile, tablet, and desktop

Database Integration - Dual database system (AeroClick + WordPress)

External API Integration - Baidu Maps, Giscus comments

Security Features - CSRF protection, SQL injection prevention, input validation

File Upload System - Contact form with CV/document upload

Export Functionality - CSV export for product comparisons

🛠️ Technology Stack
Backend
PHP 8.0+ - Server-side scripting

MySQL 5.7+ - Database management

PDO - Secure database operations

Apache - Web server

Frontend
HTML5 - Semantic markup

CSS3 - Custom styling with Flexbox/Grid

JavaScript (ES6) - Interactive features

Baidu Maps API - Location display

Giscus - GitHub-powered comments

Security
CSRF Tokens - All forms protected

Prepared Statements - SQL injection prevention

Input Validation - Server-side validation


Session Management - Secure user sessions

File Upload Security - Type/size validation
📁 Project Structure
text
aeroclick/
├── assets/                 # Static assets
│   ├── css/               # Stylesheets
│   │   ├── style.css      # Main styles
│   │   └── admin.css      # Admin panel styles
│   ├── js/                # JavaScript files
│   │   ├── main.js        # Main scripts
│   │   ├── table-to-csv.js # Export functionality
│   │   └── star-rating.js # Rating widget
│   └── images/            # Images and icons
│       ├── logo.png       # Website logo
│       └── products/      # Product images
├── includes/              # Shared PHP includes
│   ├── header.php         # Site header
│   ├── footer.php         # Site footer
│   ├── functions.php      # Utility functions
│   ├── init.php           # Initialization
│   └── admin_auth.php     # Admin authentication
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── paths.php          # Path configuration
├── auth/                  # Authentication
│   ├── login.php          # Login page
│   ├── register.php       # Registration page
│   ├── process_login.php  # Login processing
│   └── logout.php         # Logout script
├── products/              # Product management
│   ├── index.php          # Product listing
│   ├── view.php           # Product details
│   ├── compare.php        # Product comparison
│   └── export_csv.php     # CSV export
├── cart/                  # Shopping cart
│   ├── index.php          # Cart view
│   ├── add_to_cart.php    # Add to cart
│   ├── update_cart.php    # Update cart
│   ├── remove_item.php    # Remove items
│   └── checkout.php       # Checkout process
├── admin/                 # Admin panel
│   ├── index.php          # Admin dashboard
│   ├── products.php       # Product management
│   ├── users.php          # User management
│   └── orders.php         # Order management
├── user/                  # User section
│   ├── profile.php        # User profile
│   └── orders.php         # User orders
├── forum/                 # Community forum
│   ├── forum.php          # Forum main page
│   └── forum_view.php     # Individual thread view
├── uploads/               # Uploaded files
│   └── cv/                # Contact form uploads
├── logs/                  # Application logs
└── index.php              # Homepage
🗄️ Database Schema
Main Tables (AeroClick Database)
users - User accounts and profiles

products - Product information and specifications

categories - Product categories

orders - Order information

order_items - Individual order items

reviews - Product reviews from verified purchases

admin_logs - Admin activity tracking

WordPress Integration Tables
wpri_fc_subscribers - FluentCRM subscriber data (authentication)

forum_posts - Forum discussions

forum_replies - Forum replies

wpri_contact_submissions - Contact form submissions

🚀 Installation & Setup
Prerequisites
PHP 8.0 or higher

MySQL 5.7 or higher

Apache web server

Composer (optional)

Step-by-Step Installation
Clone or download the project

bash
git clone https://github.com/yourusername/aeroclick.git
cd aeroclick
Configure database connections
Edit config/database.php with your database credentials:

php
$host = 'your_host';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';
Import database schema

bash
mysql -u username -p database_name < database/schema.sql
Set up WordPress integration (optional)

Ensure WordPress database is accessible

Update WordPress database credentials in relevant files

Configure file permissions

bash
chmod 755 uploads/
chmod 755 logs/
Configure web server

Point your web server to the project root

Ensure mod_rewrite is enabled for clean URLs

Configuration Details
Environment Variables:

BASE_URL - Set your website's base URL

ASSETS_URL - Path to assets directory

Database credentials in config files

Security Settings:

Update CSRF token generation in includes/functions.php

Configure session settings in PHP.ini

Set up SSL certificate for HTTPS

👥 User Accounts
Demo Accounts
Admin Account:

Email: 1459321941@qq.com

No password required (demo authentication)

User Accounts:

Email: hty1326547@163.com

Phone: 13738053838

No password required for demo purposes

🔒 Security Features
Implemented Security Measures
SQL Injection Prevention - PDO prepared statements

Cross-Site Scripting (XSS) Protection - Output encoding

Cross-Site Request Forgery (CSRF) Protection - Token validation

Session Security - Regenerated IDs, timeout handling

Input Validation - Server-side validation for all inputs

File Upload Security - Type verification, size limits

Password Security - bcrypt hashing (for registration system)

Admin Access Control - Role-based permissions

Security Headers
php
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
📱 Responsive Design
Breakpoints:

Mobile: < 768px (single column layout)

Tablet: 768px - 1024px (two column layout)

Desktop: > 1024px (full responsive grid)

Supported Browsers:

Chrome 90+

Firefox 88+

Safari 14+

Edge 90+

🔗 External Integrations
Baidu Maps API
Used in About page to display Australian TAFE locations

Interactive map with markers and info windows

API key configuration required

Giscus Comments
GitHub-powered comment system on product pages

Requires GitHub repository setup

Configured in products/view.php

WordPress Integration
User authentication via FluentCRM subscribers

Forum system using WordPress database

Contact form submissions storage

🧪 Testing
Tested Features
User Authentication - Login, registration, session management

Shopping Cart - Add, update, remove items, checkout

Product Management - Filtering, comparison, details view

Forum System - Post creation, replies, moderation

Admin Functions - Product management, user management, order processing

Responsive Design - Cross-device compatibility

Security Features - CSRF, XSS, SQL injection protection

Browser Testing
✅ Chrome (Desktop & Mobile)

✅ Firefox

✅ Safari

✅ Edge

📊 Performance Optimization
Implemented Optimizations
Image Optimization - Proper sizing and compression

CSS/JS Minification - Reduced file sizes

Database Indexing - Optimized query performance

Caching Strategy - Browser caching for static assets

Code Optimization - Efficient PHP algorithms

📝 Documentation
Included Documentation
Code Comments - Comprehensive inline documentation

Database Schema - Complete ER diagrams

API Documentation - Integration guides

Security Documentation - Implementation details

Project Documentation Files
ICTWEB513_Project_Portfolio.docx - Complete project portfolio

Database schema files

Installation guide

User manual

🚨 Disclaimer
Educational Purpose
This project is developed for educational purposes only as part of the ICTWEB513 unit. It demonstrates web development skills but should not be used for real e-commerce transactions.

No Real Transactions
All transactions are simulated

No real payments are processed

Inventory management is for demonstration only

User data is for testing purposes

Demo Data
All products, reviews, and user data are fictional and created for demonstration purposes.

👨‍💻 Development
Development Environment
Local Server: XAMPP/WAMP/MAMP

Code Editor: VS Code

Version Control: Git

Database Tool: phpMyAdmin/MySQL Workbench

Coding Standards
PSR-12 - PHP coding standards

Semantic HTML - Accessible markup

BEM Methodology - CSS class naming

Modular JavaScript - Component-based scripts

🤝 Contributing
As this is a student project, contributions are not expected. However, suggestions and feedback are welcome for educational purposes.

📄 License
This project is created for educational purposes as part of the ICTWEB513 unit. All rights reserved by the student developer.

📧 Contact
Student: Grant
Student ID: 233190711
Course: ICTWEB513 - Build Dynamic Websites
Institution: TAFE/Educational Institution

🎯 Learning Outcomes
This project demonstrates competency in:

Building dynamic websites with PHP and MySQL

Implementing secure coding practices

Creating responsive web designs

Integrating databases and external APIs

Developing e-commerce functionality

Implementing user authentication systems

Creating administrative interfaces

Testing and debugging web applications
