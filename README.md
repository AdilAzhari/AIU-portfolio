# Blockchain-Based Digital Credential Portfolio System

A comprehensive web application for managing academic credentials using Ethereum blockchain and IPFS decentralized storage.

![Laravel](https://img.shields.io/badge/Laravel-12.0-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3.4-green)
![PHP](https://img.shields.io/badge/PHP-8.3-blue)
![Ethereum](https://img.shields.io/badge/Ethereum-Solidity%200.8.20-purple)

## Table of Contents

- [Features](#features)
- [System Architecture](#system-architecture)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Blockchain Setup](#blockchain-setup)
- [IPFS Setup](#ipfs-setup)
- [Running the Application](#running-the-application)
- [User Roles](#user-roles)
- [Testing](#testing)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## Features

### Core Functionality
- 🔐 **Blockchain-Based Credentials** - Immutable credential records on Ethereum
- 📁 **IPFS Storage** - Decentralized evidence file storage via Pinata
- 🎫 **QR Code Generation** - Instant credential verification via QR codes
- ✅ **Public Verification** - Anyone can verify credential authenticity
- 👥 **Role-Based Access Control** - Four user roles: Student, Issuer, Verifier, Admin
- 📊 **Activity Logging** - Comprehensive audit trail for all actions
- 🔍 **Real-time Blockchain Status** - Track transaction confirmations

### User Features

**For Students:**
- View all credentials in portfolio dashboard
- Upload evidence files to IPFS
- Download QR codes for credentials
- View credential verification page
- Track credential status (pending, issued, revoked)

**For Issuers (Faculty):**
- Create credentials for students
- Issue credentials to blockchain
- Revoke credentials with reasons
- View all issued credentials
- Track blockchain transactions

**For Verifiers:**
- Verify credential authenticity
- View blockchain confirmation
- Check IPFS content integrity
- Access comprehensive verification reports

**For Administrators:**
- System oversight and monitoring
- View activity logs
- Export audit reports
- Manage user roles

## System Architecture

```
Frontend (Vue.js 3 + Inertia.js)
           ↓
Backend (Laravel 12 + PHP 8.3)
           ↓
     ┌─────┴─────┐
     ↓           ↓
MySQL Database  Ethereum Blockchain
                (Smart Contracts)
     ↓
IPFS Network (Pinata)
```

## Prerequisites

### Required Software

- **PHP 8.3 or higher**
- **Composer 2.x**
- **Node.js 18.x or higher**
- **NPM 9.x or higher**
- **MySQL 8.0 or higher**
- **Git**

### Optional (for blockchain development)

- **Hardhat** (for local blockchain testing)
- **MetaMask** (for blockchain interactions)

### External Services

- **Pinata Account** (Free tier: 1GB) - [Sign up here](https://pinata.cloud)
- **Ethereum RPC Provider** - Infura, Alchemy, or local Hardhat network

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/aiu-portfolio.git
cd aiu-portfolio
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node.js Dependencies

```bash
npm install
```

### Step 4: Create Environment File

```bash
# Copy the example environment file
cp .env.example .env
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

## Configuration

### Environment Variables

Edit `.env` file with your configuration:

#### Basic Configuration

```env
APP_NAME="AIU Portfolio"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

#### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiu_portfolio
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### IPFS/Pinata Configuration

Sign up at [Pinata.cloud](https://pinata.cloud) and get your API credentials:

```env
IPFS_ENABLED=true
IPFS_DEFAULT_SERVICE=pinata

# Pinata Configuration
PINATA_ENABLED=true
PINATA_API_KEY=your_pinata_api_key
PINATA_SECRET_KEY=your_pinata_secret_key
PINATA_JWT=your_pinata_jwt_token
```

#### Blockchain Configuration

**Option 1: Local Hardhat Network (for development)**

```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_NETWORK=localhost
BLOCKCHAIN_RPC_URL=http://127.0.0.1:8545
```

**Option 2: Ethereum Sepolia Testnet (for testing)**

```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_NETWORK=sepolia
BLOCKCHAIN_RPC_URL=https://sepolia.infura.io/v3/YOUR_INFURA_PROJECT_ID
```

**Option 3: Disable Blockchain (for initial setup)**

```env
BLOCKCHAIN_ENABLED=false
```

## Database Setup

### Step 1: Create Database

Create a MySQL database:

```sql
CREATE DATABASE aiu_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: Seed Database (Optional)

To create test users:

```bash
php artisan db:seed --class=TestUsersSeeder
```

**Default Test Users:**

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@aiu.edu.my | password |
| Issuer | issuer@aiu.edu.my | password |
| Student | student@aiu.edu.my | password |
| Verifier | verifier@aiu.edu.my | password |

## Blockchain Setup

### Option 1: Local Hardhat Network (Recommended for Development)

#### Step 1: Install Hardhat

```bash
npm install --save-dev hardhat
```

#### Step 2: Start Local Blockchain

```bash
npx hardhat node
```

This will start a local Ethereum network on `http://127.0.0.1:8545` with test accounts.

#### Step 3: Deploy Smart Contracts

In a new terminal:

```bash
npx hardhat run scripts/deploy.js --network localhost
```

#### Step 4: Update .env with Contract Addresses

After deployment, update your `.env` file:

```env
BLOCKCHAIN_ISSUER_REGISTRY_ADDRESS=0x5FbDB2315678afecb367f032d93F642f64180aa3
BLOCKCHAIN_CREDENTIAL_REGISTRY_ADDRESS=0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512
```

### Option 2: Ethereum Sepolia Testnet

#### Step 1: Get Sepolia ETH

Get free testnet ETH from [Sepolia Faucet](https://sepoliafaucet.com/)

#### Step 2: Deploy to Sepolia

```bash
npx hardhat run scripts/deploy.js --network sepolia
```

#### Step 3: Update Contract Addresses

Update `.env` with deployed contract addresses.

### Option 3: Skip Blockchain (For Testing UI Only)

Set in `.env`:

```env
BLOCKCHAIN_ENABLED=false
```

## IPFS Setup

### Using Pinata (Recommended)

#### Step 1: Create Pinata Account

1. Go to [Pinata.cloud](https://pinata.cloud)
2. Sign up for free account (1GB storage)
3. Verify your email

#### Step 2: Generate API Keys

1. Go to **API Keys** in Pinata dashboard
2. Click **New Key**
3. Enable **pinFileToIPFS** permission
4. Copy **API Key**, **API Secret**, and **JWT**

#### Step 3: Update .env

```env
PINATA_API_KEY=your_api_key_here
PINATA_SECRET_KEY=your_secret_key_here
PINATA_JWT=your_jwt_token_here
PINATA_ENABLED=true
```

### Testing IPFS Connection

```bash
php artisan tinker
```

Then run:

```php
$service = app(App\Services\IpfsService::class);
$service->testConnection();
```

## Running the Application

### Development Mode

Start all services in development mode:

```bash
composer run dev
```

This will concurrently run:
- Laravel development server (http://localhost:8000)
- Vite development server (for hot module replacement)
- Queue worker (for background jobs)
- Log viewer

**Or run services separately:**

#### Terminal 1: Laravel Server

```bash
php artisan serve
```

#### Terminal 2: Vite Dev Server

```bash
npm run dev
```

#### Terminal 3: Queue Worker (for blockchain transactions)

```bash
php artisan queue:work
```

### Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

### Login Credentials

Use the test users created during seeding (see Database Setup section).

## User Roles

### Student Role

**Access:** `http://localhost:8000/student/dashboard`

**Permissions:**
- Upload evidence files
- View personal credentials
- Download QR codes
- View public verification pages

### Issuer Role

**Access:** `http://localhost:8000/issuer/dashboard`

**Permissions:**
- Create credentials for students
- Issue credentials to blockchain
- Revoke credentials
- View issued credentials history

### Verifier Role

**Access:** `http://localhost:8000/verifier/dashboard`

**Permissions:**
- Verify credentials
- View blockchain status
- Access verification reports

### Admin Role

**Access:** `http://localhost:8000/admin/dashboard`

**Permissions:**
- Full system access
- View all users
- View activity logs
- Export audit reports

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suites

```bash
# Run unit tests only
php artisan test --testsuite=Unit

# Run feature tests only
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Code Quality

```bash
# Run code style fixer
./vendor/bin/pint

# Run static analysis
./vendor/bin/phpstan analyse
```

## Deployment

### Production Environment Setup

#### Step 1: Environment Configuration

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use production database
DB_HOST=your-production-db-host

# Enable caching
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### Step 2: Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

#### Step 3: Build Frontend Assets

```bash
npm run build
```

#### Step 4: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

#### Step 5: Run Migrations

```bash
php artisan migrate --force
```

#### Step 6: Start Queue Worker

Use supervisor or systemd to keep queue worker running:

```bash
php artisan queue:work --daemon
```

### Server Requirements

- **Web Server:** Nginx or Apache
- **PHP:** 8.3+ with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Database:** MySQL 8.0+
- **SSL Certificate:** Required for production
- **Memory:** 2GB minimum, 4GB recommended

### Recommended Hosting Providers

- **DigitalOcean** - Droplets or App Platform
- **AWS** - EC2 or Elastic Beanstalk
- **Linode** - Compute Instances
- **Vultr** - Cloud Compute

## Troubleshooting

### Common Issues

#### Issue: "Permission denied" errors

**Solution:**

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**

1. Check MySQL is running:
   ```bash
   sudo systemctl status mysql
   ```

2. Verify database credentials in `.env`

3. Test connection:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

#### Issue: Blockchain transactions failing

**Solutions:**

1. Check Hardhat node is running (if using local blockchain)
2. Verify contract addresses in `.env`
3. Check account has sufficient ETH for gas
4. Review logs:
   ```bash
   php artisan pail
   ```

#### Issue: IPFS uploads failing

**Solutions:**

1. Verify Pinata API credentials in `.env`
2. Check API key permissions on Pinata dashboard
3. Test connection:
   ```bash
   php artisan tinker
   app(App\Services\IpfsService::class)->testConnection();
   ```

4. Check file size (max 10MB by default)

#### Issue: Frontend not loading/blank page

**Solution:**

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild assets
npm run build

# Check for JavaScript errors in browser console
```

#### Issue: Queue jobs not processing

**Solution:**

```bash
# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Logging

View logs in real-time:

```bash
php artisan pail
```

Or check log files:

```bash
tail -f storage/logs/laravel.log
```

## Project Structure

```
aiu-portfolio/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Request handlers
│   │   │   ├── Admin/
│   │   │   ├── Issuer/
│   │   │   ├── Student/
│   │   │   ├── Verifier/
│   │   │   └── ...
│   │   └── Middleware/            # Route middleware
│   ├── Models/                    # Eloquent models
│   │   ├── Credential.php
│   │   ├── Evidence.php
│   │   └── User.php
│   ├── Services/                  # Business logic
│   │   ├── BlockchainService.php
│   │   └── IpfsService.php
│   └── Enums/                     # Enumerations
├── blockchain/
│   └── contracts/                 # Solidity smart contracts
│       ├── CredentialRegistry.sol
│       └── IssuerRegistry.sol
├── config/                        # Configuration files
│   ├── blockchain.php
│   └── ipfs.php
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── resources/
│   ├── js/                        # Vue.js frontend
│   │   ├── Pages/                 # Inertia pages
│   │   └── Components/            # Vue components
│   └── views/                     # Blade templates
├── routes/
│   └── web.php                    # Web routes
├── tests/                         # Automated tests
│   ├── Unit/
│   └── Feature/
└── public/                        # Public assets
```

## API Documentation

### Key Endpoints

#### Authentication
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout

#### Evidence
- `GET /evidence/create` - Upload evidence form
- `POST /evidence` - Store evidence to IPFS

#### Credentials
- `GET /credentials/create` - Create credential form
- `POST /credentials` - Create credential
- `PATCH /credentials/{id}/issue` - Issue to blockchain
- `PATCH /credentials/{id}/revoke` - Revoke credential

#### Verification
- `GET /verify/{credential}` - Public verification page
- `GET /credentials/{credential}/qr` - QR code page

#### Dashboards
- `GET /student/dashboard` - Student dashboard
- `GET /issuer/dashboard` - Issuer dashboard
- `GET /verifier/dashboard` - Verifier dashboard
- `GET /admin/dashboard` - Admin dashboard

## Contributing

This is an academic project for AlBukhary International University. For inquiries, please contact the project team.

## Security

For security concerns, please contact the development team.

**Security Features:**
- HTTPS enforcement in production
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Vue.js escaping
- Rate limiting on authentication
- Role-based access control
- Activity logging for audit trails

## Technologies Used

### Backend
- **Laravel 12** - PHP framework
- **PHP 8.3** - Programming language
- **MySQL** - Relational database
- **Redis** - Caching and queues

### Frontend
- **Vue.js 3** - JavaScript framework
- **Inertia.js** - SPA adapter
- **Tailwind CSS** - Utility-first CSS
- **TypeScript** - Type-safe JavaScript

### Blockchain
- **Ethereum** - Blockchain platform
- **Solidity 0.8.20** - Smart contract language
- **Hardhat** - Development environment
- **Web3.php** - PHP Ethereum client

### Storage
- **IPFS** - Decentralized storage protocol
- **Pinata** - IPFS pinning service

## License

This project is licensed under the MIT License.

## Acknowledgments

- **AlBukhary International University** - School of Computing and Informatics
- **Laravel Framework** - PHP web framework
- **Ethereum Foundation** - Blockchain platform
- **IPFS/Protocol Labs** - Decentralized storage
- **Pinata** - IPFS pinning service

## Support

For issues and questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review Laravel documentation: https://laravel.com/docs
3. Review Vue.js documentation: https://vuejs.org/guide
4. Contact project maintainers

---

**Built with ❤️ for AlBukhary International University**

*Final Year Project - School of Computing and Informatics*
