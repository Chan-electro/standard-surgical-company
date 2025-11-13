<?php
// Global configuration constants
const BRAND_NAME = 'Standard Surgical Company';
const ADDRESS = 'Plot No. 112, Medical Devices Park, Sultanpur, Sangareddy Dist., Telangana, India — 502319';
const PHONE = '+919876543210';
const PHONE_DISPLAY = '+91 98765 43210';
const EMAIL_PRIMARY = 'hello@standardsurgicalcompany.com';

// Database credentials
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'standard_surgical_company';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// SMTP credentials
const SMTP_HOST = 'smtp.example.com';
const SMTP_PORT = 587;
const SMTP_USER = 'no-reply@example.com';
const SMTP_PASS = 'change-me';
const SMTP_ENCRYPTION = 'tls';
const SMTP_FROM_EMAIL = EMAIL_PRIMARY;
const SMTP_FROM_NAME = BRAND_NAME;

// Application settings
const ITEMS_PER_PAGE = 12;
const SESSION_RATE_LIMIT_KEY = 'quote_rate_limit';
const RATE_LIMIT_MAX = 5; // max submissions per hour
const RATE_LIMIT_WINDOW = 3600; // seconds

const HOME_STATS = [
    ['value' => '500+', 'label' => 'Hospital Clients'],
    ['value' => '300+', 'label' => 'Product SKUs'],
    ['value' => '30+', 'label' => 'Years in Operation'],
    ['value' => '24/7', 'label' => 'National Support'],
];
