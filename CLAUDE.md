# CLAUDE.md

## Project Overview

**LOCAL-UP** (internally "ClnkGO") is a Laravel 11 web application for managing Google My Business (GMB) locations. It provides auto-posting of scheduled posts and media to GMB, review management with AI-assisted reply generation (via OpenAI), and Google OAuth integration for authenticating business accounts.

- **Copyright**: BADDI Services (https://baddi.info)
- **License**: MIT

## Tech Stack

- **Language**: PHP 8.4
- **Framework**: Laravel 11
- **Database**: MySQL 8.0
- **Cache**: Memcached 1.6
- **Testing**: Pest 3.0 / PHPUnit 11
- **Containerization**: Docker + Docker Compose
- **CI/CD**: GitHub Actions
- **Error Tracking**: Bugsnag
- **External APIs**: Google My Business API, Google OAuth2, OpenAI (GPT-4 Turbo)

## Repository Structure

```
/
├── .github/workflows/tests.yml   # CI pipeline
├── docker-compose.yml            # Docker services (app, db, cache)
├── Dockerfile                    # PHP 8.4 Apache image
└── src/                          # Laravel application root
    ├── app/
    │   ├── App.php               # Global constants (pagination, limits)
    │   ├── AppLogger.php         # Singleton logger (Bugsnag + Laravel Log)
    │   ├── Helpers.php           # Global helper functions (autoloaded)
    │   ├── Console/
    │   │   ├── Kernel.php        # Scheduler definitions
    │   │   └── Commands/         # Artisan commands
    │   ├── Database/Seeders/     # Database seeders
    │   ├── Domains/              # External service integrations
    │   │   ├── GoogleMyBusinessService.php  # GMB API client (Guzzle)
    │   │   ├── GoogleService.php            # Google OAuth client
    │   │   └── OpenAIService.php            # OpenAI text generation
    │   ├── Entities/             # Base model/value classes
    │   │   ├── ModelEntity.php   # Base Eloquent model (UUID PKs, filterable)
    │   │   ├── Alert.php         # Flash message DTO
    │   │   └── ArrayValue.php    # JSON-serializable array wrapper
    │   ├── Exceptions/
    │   ├── Helpers/              # Helper classes (EmojiParser, FormatHelper)
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── Auth/         # SignIn, SignOut, Authenticate
    │   │   │   ├── Dashboard/    # All dashboard controllers
    │   │   │   │   ├── Account/  # GMB connection, location settings
    │   │   │   │   ├── Ai/       # AI text generation endpoint
    │   │   │   │   ├── Errors/   # Error pages
    │   │   │   │   ├── Media/    # Media CRUD + scheduling
    │   │   │   │   ├── Posts/    # Post CRUD + scheduling
    │   │   │   │   └── Reviews/  # Review listing + reply
    │   │   │   └── DashboardController.php  # Base controller (auth, GMB check)
    │   │   ├── Filters/          # Query filter classes
    │   │   ├── Middleware/        # Auth, CSRF, CAPTCHA, etc.
    │   │   └── Requests/         # Form request validation
    │   ├── Jobs/                 # Queue jobs (PullAccountLocations)
    │   ├── Models/               # Eloquent models
    │   │   ├── User.php          # (App\ namespace)
    │   │   ├── Authenticatable.php  # Base auth model with UUID
    │   │   ├── ScheduledPost.php
    │   │   ├── ScheduledPostMedia.php
    │   │   ├── ScheduledMedia.php
    │   │   ├── AccountLocation.php
    │   │   ├── UserGoogleCredentials.php
    │   │   └── ObjectValues/     # Value objects for API payloads
    │   ├── Providers/            # Service providers
    │   ├── Repositories/         # Data access layer
    │   ├── Rules/                # Custom validation rules
    │   ├── Services/             # Business logic services
    │   └── Traits/               # Reusable traits (HasUUID, Filterable)
    ├── bootstrap/
    ├── config/                   # Laravel + custom config files
    ├── database/
    │   ├── factories/            # Model factories (UserFactory)
    │   ├── migrations/           # Database migrations
    │   └── seeders/
    ├── public/                   # Web root
    ├── resources/
    │   ├── views/                # Blade templates
    │   │   ├── auth/
    │   │   ├── dashboard/        # Dashboard pages
    │   │   ├── errors/           # Error pages (403, 404, 500, etc.)
    │   │   ├── layouts/          # Layout templates (auth, dashboard, errors)
    │   │   └── partials/         # Reusable UI partials
    │   └── lang/                 # Translations
    ├── routes/
    │   ├── web/
    │   │   ├── guest.php         # Auth routes (signin, signout)
    │   │   └── dashboard/        # Dashboard route groups
    │   │       ├── account.php
    │   │       ├── ai.php
    │   │       ├── errors.php
    │   │       ├── media.php
    │   │       ├── posts.php
    │   │       ├── reviews.php
    │   │       ├── scheduled.php
    │   │       └── stats.php
    │   └── console.php
    ├── storage/
    └── tests/
        ├── Pest.php              # Pest configuration
        ├── TestCase.php
        ├── Feature/              # Feature tests (with RefreshDatabase)
        │   ├── Auth/
        │   ├── Commands/
        │   └── Dashboard/
        └── Unit/                 # Unit tests (in-memory, no DB)
            ├── Domains/
            └── Models/
```

## Namespaces

The project uses dual PSR-4 namespaces for the `app/` directory:

- `App\` - Standard Laravel namespace (User model, Providers)
- `BADDIServices\ClnkGO\` - Primary application namespace (most code)
- `BADDIServices\Framework\` - Core framework code in `core/src`

Both `App\` and `BADDIServices\ClnkGO\` map to `src/app/`. When creating new files, use `BADDIServices\ClnkGO\` unless extending a Laravel class that expects `App\`.

## Key Conventions

### Models
- All models use **UUID primary keys** (non-incrementing, string type) via the `HasUUID` trait
- Base model class is `ModelEntity` (extends Eloquent, adds UUID + Filterable)
- Auth models extend `Authenticatable` (which extends Laravel's auth User with UUID)
- Column names are defined as **class constants** (e.g., `public const string EMAIL_COLUMN = 'email'`)
- Models use `$guarded = []` (mass-assignment unguarded)
- Access data through **getter methods** (e.g., `getId()`, `getEmail()`) rather than direct property access

### Controllers
- Dashboard controllers extend `DashboardController` which handles auth and GMB credential checks
- Single-action controllers use `__invoke()` pattern
- Controllers organized by domain under `Dashboard/` (Account, Media, Posts, Reviews, Ai)
- Flash messages use the `Alert` entity class

### Architecture Layers
- **Controllers** -> **Services** -> **Repositories** (for data access)
- **Domains/** contains external API integration classes (Google, OpenAI)
- External API calls use **Guzzle HTTP client** directly (not Laravel HTTP)
- Repository pattern for database queries (e.g., `UserRepository`)

### Routes
- Routes are split into subdirectory files under `routes/web/` and `routes/api/`
- Route files are auto-loaded via glob patterns in `RouteServiceProvider`
- Dashboard routes are grouped by feature (account, media, posts, reviews, etc.)
- Named routes use dot notation: `dashboard.scheduled.posts`, `dashboard.account.gmb.callback`

### Scheduled Commands
Four scheduled commands run via the Laravel scheduler:
- `auto-post:scheduled-posts` - Posts scheduled posts to GMB (every minute)
- `auto-post:scheduled-media` - Posts scheduled media to GMB (every minute)
- `remove:outdated-draft-scheduled-posts` - Cleanup drafts (daily at midnight)
- `user:refresh-google-access-token` - Refresh OAuth tokens (every minute)

## Development Setup

### Docker Environment

```bash
# Start all services (app, MySQL, Memcached)
docker compose up -d

# The app is available at http://localhost:8088
# MySQL is exposed on port 33061
# Memcached is exposed on port 11222
```

Docker services:
- `gmb_app` - PHP 8.4 Apache container, mounts `./src` to `/var/www`
- `gmb_db` - MySQL 8.0 (user: `gmb_db_user`, password: `deployer`, database: `gmb_app`)
- `gmb_cache` - Memcached 1.6

### Initial Setup

```bash
# Enter the app container
docker compose exec gmb_app bash

# Inside the container:
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed    # Seeds users via UsersSeeder
```

### Environment Variables

Key environment variables (see `src/.env.example`):
- `DB_*` - MySQL connection settings
- `CACHE_DRIVER` - Set to `memcached` for Docker, `array` for testing
- `HCAPTCHA_*` - hCaptcha configuration (can be disabled via `HCAPTCHA_FEATURE_ENABLED`)
- `OPENAI_API_KEY` - OpenAI API key for AI text generation
- `BUGSNAG_API_KEY` - Bugsnag error reporting
- Google OAuth credentials go in `config/google.json`

## Testing

### Test Framework
- **Pest 3.0** with the Laravel plugin
- Feature tests use `RefreshDatabase` trait (SQLite in-memory)
- Unit tests run without database

### Running Tests

```bash
# From src/ directory (or inside Docker container)
php artisan test --env=testing

# Run specific test file
php artisan test tests/Feature/Auth/SignInTest.php

# Run with Pest directly
./vendor/bin/pest
```

### Test Environment
- Uses SQLite in-memory database (see `.env.testing`)
- Cache/session/queue all use `array`/`sync` drivers
- Tests are in `src/tests/Feature/` and `src/tests/Unit/`

### Test Coverage Areas
- **Feature tests**: Auth (signin/signout), Dashboard access, Account management, Posts CRUD, Media CRUD, Reviews, AI text generation, GMB account features, Scheduled posts, Artisan commands
- **Unit tests**: GoogleMyBusinessService, GoogleBusinessLocalPostObjectValue, UserGoogleCredentials, User model

## CI/CD

GitHub Actions workflow (`.github/workflows/tests.yml`):
- Triggers on push to `main` and PRs to `main`
- Skips draft PRs
- Matrix: PHP 8.4
- Steps: checkout, setup PHP, cache Composer, copy `.env.example`, `composer install`, generate key, run `php artisan test --env=testing`

## Important Notes

- The app's default locale is `fr` (French) - translations and default language codes reflect this
- Google OAuth requires a `config/google.json` credentials file
- The `DashboardController` constructor middleware redirects users without valid GMB credentials to an error page (except for account and error routes)
- Logging uses `AppLogger` singleton which wraps both Laravel's Log facade and Bugsnag client
- The queue connection is `database` in production but `sync` in testing
