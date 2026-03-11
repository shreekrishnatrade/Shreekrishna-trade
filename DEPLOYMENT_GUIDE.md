# 🚀 Production Deployment Guide

## Prerequisites
- Web hosting with PHP 7.4+ and MySQL 5.7+
- Domain name configured
- SSL certificate (Let's Encrypt recommended)
- FTP/SSH access to server

## Step-by-Step Deployment

### 1. Prepare Files for Upload

**Files to Upload:**
```
shreekrishna2.0/
├── admin/
├── backend/
├── css/
├── img/
├── js/
├── user/
├── includes/ (config.php, functions.php)
├── *.html files
├── .htaccess
└── index.html
```

**Files to EXCLUDE (do NOT upload):**
```
- database/ (only use schema.sql locally)
- logs/
- test_*.php
- migrate-*.php
- *.md files
- config.production.php (rename to config.php after editing)
```

### 2. Database Setup

**On your hosting control panel (cPanel/phpMyAdmin):**

1. Create a new MySQL database
2. Create a database user with full privileges
3. Import `database/schema.sql`
4. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually 'localhost')

### 3. Configure Production Settings

**Edit `includes/config.php` on server:**

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_actual_db_name');
define('DB_USER', 'your_actual_db_user');
define('DB_PASS', 'your_actual_db_password');

define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

define('DISPLAY_ERRORS', false); // IMPORTANT: Set to false
```

### 4. Set File Permissions

```bash
chmod 755 admin/
chmod 755 backend/
chmod 755 user/
chmod 644 includes/config.php
chmod 644 .htaccess
chmod 755 logs/ (if exists)
```

### 5. Enable HTTPS

**In `.htaccess`, uncomment these lines:**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 6. Email Configuration (Optional but Recommended)

**For Gmail SMTP:**

1. Enable 2-Factor Authentication on Gmail
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Update `config.php`:

```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

4. Install PHPMailer via Composer (on server):

```bash
composer require phpmailer/phpmailer
```

### 7. Security Checklist

- [ ] Changed default admin password
- [ ] Updated database credentials
- [ ] Set DISPLAY_ERRORS to false
- [ ] HTTPS enabled and working
- [ ] .htaccess file uploaded
- [ ] Sensitive files blocked (test: yourdomain.com/includes/config.php should show 403)
- [ ] Created logs/ directory with write permissions

### 8. Testing Checklist

Visit your live site and test:

- [ ] Homepage loads correctly
- [ ] User registration works
- [ ] User login works
- [ ] Service request submission
- [ ] Contact form submission
- [ ] Admin login: yourdomain.com/admin/login.html
- [ ] Admin dashboard shows data
- [ ] Email notifications sent (if configured)

### 9. Post-Deployment

**Update Admin Password:**
```sql
-- Run in phpMyAdmin
UPDATE admin_users 
SET password_hash = '$2y$10$NEW_HASH_HERE' 
WHERE username = 'admin';
```

Generate hash locally:
```php
php -r "echo password_hash('YourNewPassword', PASSWORD_BCRYPT);"
```

**Monitor Logs:**
- Check `logs/error.log` for any issues
- Monitor database size
- Set up automated backups

### 10. Performance Optimization

**Enable OPcache (in php.ini or .htaccess):**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

**Database Optimization:**
```sql
OPTIMIZE TABLE users, service_requests, memberships;
```

## Troubleshooting

### "500 Internal Server Error"
- Check `.htaccess` syntax
- Verify PHP version (7.4+)
- Check error logs

### "Database Connection Failed"
- Verify credentials in config.php
- Check if database user has privileges
- Confirm database exists

### "Page Not Found" for admin/user pages
- Check file permissions
- Verify .htaccess is uploaded
- Check mod_rewrite is enabled

### Emails Not Sending
- Verify SMTP credentials
- Check spam folder
- Enable error logging
- Test with simple mail() function first

## Backup Strategy

**Automated Daily Backups:**
1. Database: Use cPanel backup or mysqldump
2. Files: FTP sync or rsync
3. Store backups off-site (Google Drive, Dropbox)

**Manual Backup Command:**
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
tar -czf files_$(date +%Y%m%d).tar.gz /path/to/shreekrishna2.0/
```

## Support & Maintenance

**Regular Tasks:**
- Weekly: Check error logs
- Monthly: Update dependencies
- Quarterly: Security audit
- Yearly: Renew SSL certificate (auto with Let's Encrypt)

**Security Updates:**
- Keep PHP updated
- Monitor for SQL injection attempts in logs
- Review admin activity logs

## Quick Reference

**Admin Panel:** https://yourdomain.com/admin/login.html
**User Dashboard:** https://yourdomain.com/user/dashboard.php
**Database:** Via phpMyAdmin in cPanel

**Default Credentials:**
- Admin: admin / Admin@123 (CHANGE THIS!)

---

## Emergency Contacts

- Hosting Support: [Your host's support]
- Domain Registrar: [Your registrar]
- Developer: [Your contact]

**Last Updated:** 2026-02-01
