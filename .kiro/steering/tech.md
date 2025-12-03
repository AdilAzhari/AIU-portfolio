# Technology Stack

## Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Authentication**: Laravel Breeze with Sanctum
- **Admin Panel**: Filament 4.2
- **Testing**: Pest PHP

## Frontend
- **Framework**: Vue 3
- **SPA**: Inertia.js 2.0
- **Styling**: Tailwind CSS 3
- **Build Tool**: Vite 7
- **Routing Helper**: Ziggy 2.0

## Development Tools
- **Code Style**: Laravel Pint (PHP), Prettier (JS)
- **Refactoring**: Rector
- **Linting**: ESLint 9
- **Process Management**: Concurrently

## Common Commands

### Setup
```bash
composer run setup
```
Installs dependencies, creates .env, generates key, runs migrations, and builds assets.

### Development
```bash
composer run dev
```
Starts Laravel server, queue worker, logs viewer (Pail), and Vite dev server concurrently.

### Testing
```bash
composer run test
# or
php artisan test
```

### Code Style
```bash
# PHP
./vendor/bin/pint

# JavaScript
npm run format  # if configured
```

### Build for Production
```bash
npm run build
```

### Database
```bash
php artisan migrate
php artisan db:seed
```

## Architecture Notes
- Uses PSR-4 autoloading
- Follows Laravel conventions
- Inertia.js for seamless SPA without API
- Queue system configured (run with `php artisan queue:listen`)
