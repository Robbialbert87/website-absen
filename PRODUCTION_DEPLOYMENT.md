# PRODUCTION DEPLOYMENT CHECKLIST

## Critical Configuration Changes Required

### 1. Environment Variables (.env)

**BEFORE DEPLOYING - Update these values:**

```bash
# Change from:
APP_ENV=local
APP_DEBUG=true

# Change to:
APP_ENV=production
APP_DEBUG=false
```

### 2. Database Configuration

**Ensure these are set correctly in production:**

```bash
DB_HOST=your_production_server
DB_PORT=3306
DB_DATABASE=db_absensi_pegawai
DB_USERNAME=secure_username
DB_PASSWORD=strong_secure_password
```

⚠️ **CRITICAL:** Never use empty password in production!

### 3. Mail Configuration

**Update for production SMTP:**

```bash
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_server
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SIJAGA HAM"
```

### 4. Application URL

```bash
APP_URL=https://yourdomain.com  # Use HTTPS
```

### 5. Session & Cookie Configuration

**In config/session.php:**
```php
'secure' => true,  // Require HTTPS
'http_only' => true,  // Prevent JS access
'same_site' => 'lax',
```

---

## Pre-Deployment Tasks

### Phase 1: Code & Database
- [x] Fix role naming inconsistency (super-admin → super_admin)
- [x] Fix migration table name mismatch
- [x] Add authorization checks to destroy methods
- [x] Add username validation
- [x] Add file size validation for uploads
- [ ] Run all migrations: `php artisan migrate --force`
- [ ] Run seeder for kepala ruangan: `php artisan db:seed --class=UpdateKepalaRuanganUserSeeder`

### Phase 2: Security
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Configure HTTPS/SSL certificate
- [ ] Set secure database password
- [ ] Configure firewall rules
- [ ] Enable login rate limiting
- [ ] Set up error logging (not showing errors to users)

### Phase 3: Optimization
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`

### Phase 4: Monitoring & Backup
- [ ] Set up automated database backups
- [ ] Configure error logging service (e.g., Sentry)
- [ ] Set up monitoring/alerting
- [ ] Test disaster recovery process

---

## Server Requirements

```
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js (for assets if needed)
- 2GB+ RAM recommended
- 10GB+ disk space
```

---

## Post-Deployment Verification

1. **Test Login**
   - Admin login (email/username)
   - Kepala ruangan login (NIP)
   - Regular user login

2. **Test Critical Features**
   - Create user
   - Create kegiatan
   - Submit attendance
   - Export reports

3. **Security Checks**
   - Verify HTTPS is enforced
   - Verify APP_DEBUG=false
   - Check error logging (not displaying errors)
   - Test unauthorized access returns 403

4. **Database**
   - Verify backups are running
   - Test backup restoration

---

## Troubleshooting

### If migrations fail:
```bash
php artisan migrate:rollback
php artisan migrate --force
```

### If cache issues occur:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### If permissions are wrong:
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

---

## Quick Deploy Commands

```bash
# Full deployment sequence
git pull origin main
php artisan migrate --force
php artisan db:seed --class=UpdateKepalaRuanganUserSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

---

**IMPORTANT:** Always test in staging environment first!
