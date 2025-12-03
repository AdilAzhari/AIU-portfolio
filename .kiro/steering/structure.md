# Project Structure

## Backend (Laravel)

### `/app`
- **Enums/**: Application enums (e.g., `RoleEnum.php` for user roles)
- **Http/Controllers/**: Request handlers
  - `Auth/`: Authentication controllers (login, register, password reset, email verification)
  - `ProfileController.php`: User profile management
- **Http/Middleware/**: Custom middleware
  - `RoleMiddleware.php`: Role-based access control
  - `HandleInertiaRequests.php`: Inertia.js request handling
- **Http/Requests/**: Form request validation classes
- **Models/**: Eloquent models (`User.php`, `Role.php`)
- **Providers/**: Service providers

### `/database`
- **factories/**: Model factories for testing
- **migrations/**: Database schema migrations
- **seeders/**: Database seeders (includes `RoleSeeder.php`)

### `/routes`
- Route definitions (web, api, console)

### `/config`
- Application configuration files

## Frontend (Vue + Inertia)

### `/resources/js`
- **Components/**: Reusable Vue components (buttons, inputs, modals, dropdowns, etc.)
- **Layouts/**: Page layout components
- **Pages/**: Inertia page components
  - `Admin/Dashboard.vue`: Admin dashboard
  - `Issuer/Dashboard.vue`: Issuer dashboard
  - `Student/Dashboard.vue`: Student dashboard
  - `Verifier/Dashboard.vue`: Verifier dashboard
  - `Auth/`: Authentication pages
  - `Profile/`: Profile management pages
  - `Welcome.vue`: Landing page
- **app.js**: Application entry point
- **bootstrap.js**: Bootstrap configuration (Axios, etc.)

### `/resources/css`
- **app.css**: Main stylesheet (Tailwind)

### `/resources/views`
- **app.blade.php**: Root Blade template for Inertia

## Testing

### `/tests`
- **Feature/**: Feature tests (Auth, Profile)
- **Unit/**: Unit tests
- **Pest.php**: Pest configuration
- **TestCase.php**: Base test case

## Public Assets

### `/public`
- Publicly accessible files (index.php, .htaccess, favicon, robots.txt)

## Key Conventions

1. **Role-Based Architecture**: Each user role has dedicated dashboard pages and middleware protection
2. **Inertia Pages**: Vue components in `/resources/js/Pages` map to routes
3. **Shared Components**: Reusable UI components in `/resources/js/Components`
4. **Eloquent Relationships**: User `belongsTo` Role, Role `hasMany` Users
5. **Enum Usage**: Role types defined in `RoleEnum` and cast in models
6. **Middleware Protection**: Routes protected with `RoleMiddleware` for role-specific access
