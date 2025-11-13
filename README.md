# aslams — Health Care Equipment

This repository contains a lightweight PHP 8.1 website for aslams — Health Care Equipment. The site is built without a framework and uses a simple front controller (`routes.php`) to serve modular pages from `/pages` with shared layout elements in `/includes`.

## Requirements

- PHP 8.1+
- Composer (for installing PHPMailer)
- MySQL 8.x (or compatible)
- Web server configured to serve the project root (or use PHP's built-in server)

## Installation

1. **Clone the repository** and install PHP dependencies:

   ```bash
   composer require phpmailer/phpmailer
   ```

2. **Configure environment values** in `includes/config.php`:
   - Database credentials (`DB_HOST`, `DB_NAME`, etc.)
   - SMTP credentials (`SMTP_HOST`, `SMTP_USER`, etc.)
   - Brand contact details (address, phone, email)

3. **Create the database schema** and seed starter content:

   ```bash
   mysql -u <user> -p < database_name < sql/seed.sql
   ```

4. **Serve the application**. For local development you can run:

   ```bash
   php -S 0.0.0.0:8000 -t .
   ```

   Navigate to `http://localhost:8000/?page=home`.

## Key Features

- **Unified layout** with sticky navigation, active states, and shared brand tokens defined via CSS variables.
- **Accessible design**: semantic headings, visible focus states, ARIA attributes, and keyboard-friendly sliders.
- **Get a Quote workflows**: two secure forms with CSRF protection, honeypot trap, session rate-limiting, PHPMailer-based email notifications, and MySQL lead storage.
- **Products catalogue**: server-side filtering, pagination, feature badges, and per-card Product JSON-LD schema.
- **Knowledge hub**: category chips, excerpts, and Article JSON-LD for better SEO.
- **Home page highlights**: hero CTA, stats, client carousel, product/post previews, testimonials, and CTAs.
- **Data fallbacks**: JSON seed files in `/data` populate the UI when the database is not available.

## Project Structure

```
includes/        Shared configuration, helpers, header, footer
pages/           Page templates rendered by routes.php
partials/        Reserved for reusable view fragments
assets/css/      Global stylesheet (site.css)
assets/js/       Global script (site.js)
sql/             Database schema and seed data
```

## SMTP & Email Delivery

The `send_mail` helper relies on PHPMailer. Ensure the SMTP constants are populated before deploying. When SMTP credentials are absent or PHPMailer is not installed, the helper logs an error and returns `false`.

## Security Notes

- Sessions start in `includes/functions.php` for CSRF and flash messaging.
- Quote forms include CSRF tokens, honeypot field, and per-session submission limits.
- All database interactions use prepared statements through PDO.

## Assets

- All critical styling is bundled in `assets/css/site.css`.
- Behaviour for navigation, carousel, and sliders is located in `assets/js/site.js` and loaded with `defer` to keep the page responsive.

## Running Tests

This project does not yet include automated tests. Before deploying, manually verify:

- Forms submit successfully and send email
- Database records are inserted for new leads
- Products and Knowledge filters return expected results
- Lighthouse scores (Performance > 80, Accessibility > 90, SEO > 90)
