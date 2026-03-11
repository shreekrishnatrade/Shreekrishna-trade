# Backend Installation Guide

## 1. Database Setup
1. Open phpMyAdmin or your MySQL client.
2. Create a new database named `shreekrishna_services`.
3. Import the `database/schema.sql` file into this database.
   - This will create all necessary tables (users, requests, etc.) and seed default data (Admin user, Technicians).

## 2. Configuration
1. Open `includes/config.php`.
2. Update the Database Credentials if necessary:
   ```php
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Check if your XAMPP uses a password
   ```
3. Update `SITE_URL` to match your local path (e.g., `http://localhost/shreekrishna2.0`).

## 3. Email Setup (Optional for Local)
- The system uses PHP's `mail()` function by default. For local XAMPP, this might not send actual emails unless configured via `sendmail`.
- For production, configure the SMTP settings in `includes/config.php`.

## 4. Admin Access
- URL: `admin/login.html`
- Username: `admin`
- Password: `Admin@123` (Note: Ensure the hash in `schema.sql` matches this password or generate a new bcrypt hash).

## 5. Integrating with Frontend
- Include `js/backend-forms.js` in your HTML files (index.html, register.html, etc.) to activate the forms.
  ```html
  <script src="js/backend-forms.js"></script>
  ```
- Alternatively, Replace the logic in `js/main.js` with the code from `js/backend-forms.js`.

## 6. Security
- A `.htaccess` file has been created to protect sensitive files. Ensure your web server (Apache) has `AllowOverride All` enabled.

## Testing
1. Go to `register.html` and create a new user account.
2. You should be redirected to `user/dashboard.php`.
3. Try booking a service request.
4. Log out and try logging back in.
