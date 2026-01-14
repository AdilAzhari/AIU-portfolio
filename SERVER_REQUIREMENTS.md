# Server Requirements for AIU Portfolio

This document lists all requirements needed to run the AIU Portfolio application on a shared hosting server.

## Minimum Server Requirements

### PHP
- **Version**: PHP 8.3 or higher
- **Memory Limit**: 256MB minimum (512MB recommended)
- **Max Execution Time**: 60 seconds minimum (300 seconds recommended)
- **Upload Max Filesize**: 20MB minimum (64MB recommended)
- **Post Max Size**: 20MB minimum (64MB recommended)

### Database
- **MySQL**: 5.7 or higher, OR
- **MariaDB**: 10.3 or higher
- **Storage**: 500MB minimum (1GB+ recommended)

### Node.js & NPM
- **Node.js**: Version 18 or higher
- **NPM**: Version 9 or higher

### Web Server
- **Apache**: 2.4 or higher with mod_rewrite enabled, OR
- **Nginx**: 1.18 or higher
- **HTTPS/SSL**: Highly recommended for production

### Server Access
- **SSH Access**: Required for running commands
- **Composer**: Must be installed and accessible
- **Git**: Recommended for deployment

## Required PHP Extensions

The following PHP extensions must be enabled:

### Core Extensions (Critical)
- [x] **BCMath** - For arbitrary precision mathematics
- [x] **Ctype** - Character type checking
- [x] **cURL** - For external HTTP requests
- [x] **DOM** - For XML/HTML manipulation
- [x] **Fileinfo** - For file type detection
- [x] **JSON** - For JSON parsing
- [x] **Mbstring** - Multi-byte string support
- [x] **OpenSSL** - For encryption and HTTPS
- [x] **PDO** - Database abstraction layer
- [x] **PDO_MySQL** - MySQL database driver
- [x] **Tokenizer** - For parsing PHP code
- [x] **XML** - XML support

### Additional Extensions (Recommended)
- [x] **GMP** - GNU Multiple Precision (required for blockchain features)
- [x] **GD** or **Imagick** - Image manipulation (for QR codes)
- [x] **Zip** - For file compression
- [x] **Redis** - For caching (optional but recommended)

## Apache Modules Required

If using Apache web server, ensure these modules are enabled:

- [x] **mod_rewrite** - URL rewriting (critical)
- [x] **mod_headers** - HTTP headers manipulation
- [x] **mod_expires** - Browser caching
- [x] **mod_deflate** - Compression

You can check enabled modules with: `apache2ctl -M` or `httpd -M`

## Checking Your Server

### Check PHP Version
```bash
php -v
```
Expected output: `PHP 8.3.x` or higher

### Check PHP Extensions
```bash
php -m
```
This lists all installed extensions. Verify all required extensions are present.

### Check Specific Extension
```bash
php -r "echo extension_loaded('gmp') ? 'GMP is installed' : 'GMP is NOT installed';"
```

### Check PHP Configuration
```bash
php -i | grep -E "memory_limit|max_execution_time|upload_max_filesize|post_max_size"
```

### Check Composer
```bash
composer --version
```
Expected: Composer version 2.x

### Check Node.js and NPM
```bash
node --version
npm --version
```
Expected: Node v18+ and NPM v9+

### Check MySQL
```bash
mysql --version
```

## Hosting Provider Compatibility

### Recommended Shared Hosting Providers
The following hosting providers typically meet all requirements:

- **SiteGround** - Excellent Laravel support
- **A2 Hosting** - Good performance
- **Cloudways** - Managed cloud hosting
- **DigitalOcean** - VPS (requires more setup)
- **Linode** - VPS (requires more setup)
- **Vultr** - VPS (requires more setup)

### What to Ask Your Hosting Provider

If you're unsure whether your hosting meets requirements, ask:

1. "Do you support PHP 8.3 or higher?"
2. "Can I use Composer on the command line?"
3. "Is SSH access included in my plan?"
4. "Are all required PHP extensions available?" (provide list)
5. "Can I set custom php.ini values?"
6. "Is Node.js/NPM available or can I install it?"
7. "Do you support Laravel applications?"

## Storage Requirements

### Disk Space
- **Application Files**: ~200MB
- **Vendor Dependencies**: ~150MB
- **Node Modules**: ~300MB (development only, not on production)
- **Built Assets**: ~10-50MB
- **Database**: ~100-500MB (depends on usage)
- **User Uploads**: Variable (plan accordingly)
- **Logs**: ~50-200MB
- **Recommended Total**: 2GB minimum free space

### Database
- **Tables**: ~20-30 tables
- **Connections**: 5-10 concurrent connections typical
- **Size**: Grows with user data

## Bandwidth and Performance

- **Bandwidth**: 10GB/month minimum (depends on traffic)
- **Concurrent Connections**: 50-100 recommended
- **Response Time**: <500ms ideal

## Optional but Recommended

### Caching
- **Redis** - For session and cache storage
- **Memcached** - Alternative to Redis
- **OpCache** - PHP opcode caching (usually enabled by default)

### Queue Workers
- **Supervisor** - Process monitor for queue workers
- **Cron Jobs** - For scheduled tasks

### Monitoring
- **Error Logging** - Access to server error logs
- **Application Monitoring** - New Relic, Laravel Telescope, etc.

### Security
- **SSL Certificate** - Free with Let's Encrypt
- **Firewall** - Web Application Firewall (WAF)
- **Backup System** - Automated daily backups

## Testing Your Server Before Deployment

Create a `test.php` file in your server's public directory:

```php
<?php
phpinfo();
?>
```

Upload it and visit `https://yourdomain.com/test.php` in your browser to see all PHP configuration details.

**Remember to delete this file after testing for security!**

## Common Issues and Solutions

### Issue: PHP Version Too Old
**Solution**: Ask hosting provider to upgrade, or use a newer hosting plan

### Issue: Missing PHP Extensions
**Solution**:
- Check hosting control panel for PHP extension management
- Ask hosting provider to enable extensions
- Consider VPS for full control

### Issue: Can't Access SSH
**Solution**:
- Upgrade to a hosting plan that includes SSH
- Some providers offer SSH for specific plans only

### Issue: Can't Install Composer
**Solution**:
- Use shared hosting with pre-installed Composer
- Install Composer locally and upload vendor files (not recommended)

### Issue: Node.js Not Available
**Solution**:
- Build assets locally and upload `public/build` folder
- Use VPS for full Node.js support

## Verification Checklist

Use this checklist to verify your server:

- [ ] PHP 8.3+ installed
- [ ] All required PHP extensions enabled
- [ ] MySQL/MariaDB installed
- [ ] Composer accessible
- [ ] Node.js and NPM available
- [ ] SSH access working
- [ ] mod_rewrite enabled (Apache)
- [ ] SSL certificate installed
- [ ] Sufficient disk space
- [ ] Database created
- [ ] Database user created with proper permissions

## Getting Help

If you're having trouble meeting these requirements:

1. Contact your hosting provider's support
2. Consider upgrading your hosting plan
3. Consider migrating to a Laravel-friendly host
4. Consider using a VPS for full control

---

**Last Updated**: 2026-01-14
