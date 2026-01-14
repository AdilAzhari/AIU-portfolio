# Deployment Checklist for Shared Server

Use this checklist to ensure all deployment steps are completed correctly.

## Pre-Deployment Checklist

- [ ] Verify server meets all requirements (PHP 8.3+, MySQL, Node.js)
- [ ] Have database credentials ready
- [ ] Have domain/subdomain configured
- [ ] Have SSL certificate installed (recommended)
- [ ] Have backup of existing data (if updating)

## Initial Deployment Steps

### 1. File Upload
- [ ] Upload all project files to server OR clone from Git
- [ ] Ensure hidden files (.htaccess, .gitignore) are included

### 2. Environment Configuration
- [ ] Copy `.env.production.example` to `.env`
- [ ] Update `APP_NAME`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Update `APP_URL` with your domain
- [ ] Configure database credentials (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [ ] Configure mail settings (MAIL_MAILER, MAIL_HOST, etc.)
- [ ] Update blockchain settings if using (PRIVATE_KEY, RPC_URL)
- [ ] Update IPFS/Pinata credentials if using
- [ ] Run `php artisan key:generate`

### 3. Dependencies
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `npm ci --production`

### 4. Build Assets
- [ ] Run `npm run build:production`
- [ ] Verify `public/build` directory exists with files

### 5. Database Setup
- [ ] Create database via hosting control panel
- [ ] Update `.env` with database credentials
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan db:seed --force` (if needed)

### 6. File Permissions
- [ ] Set `storage` directory permissions to 775
- [ ] Set `bootstrap/cache` directory permissions to 775
- [ ] Set correct ownership if needed

### 7. Web Server Configuration
- [ ] Set document root to `public` directory
- [ ] Verify `.htaccess` is working
- [ ] Enable HTTPS redirect in `.htaccess` if SSL is active
- [ ] Test URL rewriting

### 8. Production Optimization
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan event:cache`

### 9. Queue Configuration (Optional)
- [ ] Set up cron job for `php artisan schedule:run`
- [ ] Configure queue worker if needed

### 10. Testing
- [ ] Visit website in browser
- [ ] Test user registration
- [ ] Test user login
- [ ] Test main features
- [ ] Check browser console for errors
- [ ] Test on mobile devices
- [ ] Verify blockchain features work (if enabled)
- [ ] Test file uploads (if applicable)

## Post-Deployment Checklist

### Security
- [ ] Verify `APP_DEBUG=false`
- [ ] Verify `.env` is not web-accessible
- [ ] Verify HTTPS is working
- [ ] Test security headers (X-Frame-Options, X-Content-Type-Options)
- [ ] Verify private keys are secure
- [ ] Review file permissions

### Performance
- [ ] Test page load times
- [ ] Verify caching is working
- [ ] Check gzip compression is enabled
- [ ] Test asset loading (CSS/JS)

### Monitoring
- [ ] Check `storage/logs/laravel.log` for errors
- [ ] Monitor server error logs
- [ ] Set up uptime monitoring (optional)
- [ ] Configure error notifications (optional)

### Documentation
- [ ] Document server credentials
- [ ] Document deployment process
- [ ] Note any customizations made
- [ ] Document backup procedures

## Updating Existing Deployment

For subsequent updates, use this simplified checklist:

- [ ] Backup database
- [ ] Backup `.env` file
- [ ] Put site in maintenance mode: `php artisan down`
- [ ] Pull latest changes: `git pull` OR upload new files
- [ ] Update dependencies: `composer install --no-dev`
- [ ] Update node modules: `npm ci --production`
- [ ] Rebuild assets: `npm run build:production`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Rebuild caches: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] Bring site back up: `php artisan up`
- [ ] Test critical functionality
- [ ] Monitor logs for errors

## Quick Commands Reference

```bash
# Full deployment (use deploy.sh script)
./deploy.sh

# Manual deployment steps
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build:production
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan up

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check application status
php artisan about
```

## Troubleshooting Quick Fixes

| Issue | Quick Fix |
|-------|-----------|
| 500 Error | Check permissions on `storage` and `bootstrap/cache` |
| Assets not loading | Run `npm run build:production` and clear browser cache |
| Database error | Verify `.env` credentials and database exists |
| Cache issues | Run `php artisan cache:clear && php artisan config:clear` |
| Routes not working | Check `.htaccess` and run `php artisan route:clear` |

## Emergency Rollback

If deployment fails:

1. Restore previous files from backup
2. Restore database from backup
3. Run `composer install --no-dev`
4. Run `php artisan up`
5. Investigate issue before next deployment

---

**Note:** Keep this checklist updated with any project-specific steps or modifications.
