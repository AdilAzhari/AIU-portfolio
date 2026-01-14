# AIU Portfolio - Deployment Guide for Shared Server

This guide will help you deploy the AIU Portfolio application on a shared hosting server.

## Prerequisites

Before deploying, ensure your shared server meets these requirements:

- **PHP**: Version 8.3 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.3+)
- **Composer**: Installed and accessible
- **Node.js**: Version 18 or higher
- **NPM**: Version 9 or higher
- **SSH Access**: Required for command-line operations
- **Git**: Installed (optional, but recommended)

## Server Requirements Checklist

Ensure the following PHP extensions are enabled:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GMP (for blockchain functionality)

## Step 1: Upload Files to Server

### Option A: Using Git (Recommended)

```bash
# SSH into your server
ssh username@your-server.com

# Navigate to your web directory (adjust path as needed)
cd public_html  # or htdocs, www, etc.

# Clone your repository
git clone https://github.com/yourusername/aiu-portfolio.git
cd aiu-portfolio
```

### Option B: Using FTP/SFTP

1. Upload all project files to your server
2. Ensure the entire project is uploaded, including hidden files (.env, .gitignore, etc.)

## Step 2: Configure Environment

1. Copy the production environment template:
```bash
cp .env.production.example .env
```

2. Edit the `.env` file with your production settings:
```bash
nano .env  # or use your preferred editor
```

3. **Critical Settings to Update:**

### Application Settings
```
APP_NAME="AIU Portfolio"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Database Configuration
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Mail Configuration
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-mail-server.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
```

### Blockchain Settings (Optional)
```
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_NETWORK=sepolia  # or mainnet for production
PRIVATE_KEY=your_production_private_key
ETHERSCAN_API_KEY=your_etherscan_api_key
```

### IPFS/Pinata Settings (Optional)
```
IPFS_ENABLED=true
PINATA_API_KEY=your_pinata_api_key
PINATA_SECRET_KEY=your_pinata_secret_key
PINATA_JWT=your_pinata_jwt
```

4. Generate application key:
```bash
php artisan key:generate
```

## Step 3: Install Dependencies

1. Install PHP dependencies:
```bash
composer install --no-dev --optimize-autoloader
```

2. Install Node.js dependencies:
```bash
npm ci --production
```

## Step 4: Build Frontend Assets

Build the production assets:
```bash
npm run build:production
```

This will create optimized assets in the `public/build` directory.

## Step 5: Configure Database

1. Create a MySQL database through your hosting control panel (cPanel, Plesk, etc.)
2. Note the database name, username, and password
3. Run migrations:
```bash
php artisan migrate --force
```

4. (Optional) Seed the database if you have seeders:
```bash
php artisan db:seed --force
```

## Step 6: Set File Permissions

Set proper permissions for Laravel directories:

```bash
# Storage and cache directories need write permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# If using Apache/PHP-FPM, set ownership to web server user
chown -R www-data:www-data storage bootstrap/cache
# OR for some shared hosts:
# chown -R username:username storage bootstrap/cache
```

## Step 7: Configure Web Server

### For Shared Hosting with cPanel

1. **Document Root Setup:**
   - Set your domain's document root to the `public` directory
   - In cPanel: Domains → Select Domain → Document Root → `/path/to/aiu-portfolio/public`

2. **The `.htaccess` file** in the `public` directory is already configured with:
   - URL rewriting
   - Security headers
   - Compression
   - Browser caching
   - Optional HTTPS redirect

3. **Enable HTTPS (Highly Recommended):**
   - Uncomment the HTTPS redirect lines in `public/.htaccess`:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

## Step 8: Optimize for Production

Run Laravel optimization commands:

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache
```

## Step 9: Test the Deployment

1. Visit your domain in a web browser
2. Test user registration and login
3. Test blockchain functionality (if enabled)
4. Test file uploads (if applicable)
5. Check error logs for any issues

## Step 10: Set Up Queue Workers (Optional)

If your application uses queues:

1. **Using Cron Jobs:**
   Add this to your crontab (cPanel → Cron Jobs):
   ```
   * * * * * cd /path/to/aiu-portfolio && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **For Long-Running Queues:**
   Some shared hosts support Supervisor or you can use a keep-alive script.

## Automatic Deployment Script

For future updates, use the provided deployment script:

```bash
# Make it executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

The script will:
1. Enable maintenance mode
2. Pull latest changes
3. Update dependencies
4. Rebuild assets
5. Run migrations
6. Clear and rebuild caches
7. Disable maintenance mode

## Troubleshooting

### Issue: 500 Internal Server Error

**Solutions:**
1. Check file permissions on `storage` and `bootstrap/cache`
2. Verify `.env` file exists and is properly configured
3. Check PHP version compatibility
4. Review error logs: `storage/logs/laravel.log`

### Issue: CSS/JS Not Loading

**Solutions:**
1. Run `npm run build:production` again
2. Clear browser cache
3. Check that `APP_URL` in `.env` matches your domain
4. Verify `public/build` directory exists and has files

### Issue: Database Connection Error

**Solutions:**
1. Verify database credentials in `.env`
2. Ensure database exists
3. Check database user has proper permissions
4. Try `127.0.0.1` instead of `localhost` for `DB_HOST`

### Issue: Blockchain Features Not Working

**Solutions:**
1. Verify `BLOCKCHAIN_ENABLED=true` in `.env`
2. Check RPC URL is accessible
3. Verify private key is valid
4. Ensure GMP PHP extension is installed

### Issue: File Upload Errors

**Solutions:**
1. Check `storage/app` permissions
2. Verify `php.ini` settings: `upload_max_filesize`, `post_max_size`
3. Check disk space on server

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong `APP_KEY`
- [ ] Keep `.env` file secure (not accessible via web)
- [ ] Enable HTTPS
- [ ] Keep private keys secure
- [ ] Regularly update dependencies
- [ ] Set proper file permissions
- [ ] Enable security headers in `.htaccess`
- [ ] Use strong database passwords
- [ ] Regularly backup database and files

## Maintenance

### Updating the Application

```bash
# Pull latest changes
git pull origin main

# Run deployment script
./deploy.sh
```

### Backing Up

**Database Backup:**
```bash
php artisan backup:run  # if backup package is installed
# OR
mysqldump -u username -p database_name > backup.sql
```

**Files Backup:**
```bash
tar -czf backup-$(date +%Y%m%d).tar.gz storage/ .env
```

### Clearing Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

For issues specific to:
- **Laravel**: Check [Laravel Documentation](https://laravel.com/docs)
- **Blockchain**: Verify your RPC provider status
- **IPFS/Pinata**: Check [Pinata Documentation](https://docs.pinata.cloud/)

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Laravel Optimization](https://laravel.com/docs/deployment#optimization)
- [Shared Hosting Best Practices](https://laravel.com/docs/deployment#server-requirements)
