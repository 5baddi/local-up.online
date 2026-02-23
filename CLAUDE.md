# CLAUDE.md

## Project Overview

LOCAL-UP is a Google My Business (GMB) auto-posting web application. It enables users to authenticate with Google My Business, schedule posts and media, manage reviews, and automatically publish content at scheduled times. The app also integrates OpenAI GPT-4 for AI-assisted text generation (post summaries and review replies).

## Tech Stack

- **Backend:** PHP 8.4, Laravel 11.0
- **Frontend:** Vue 3, Bootstrap 5, jQuery, Summernote (WYSIWYG editor)
- **Database:** MySQL 8.0
- **Cache:** Memcached 1.6
- **Testing:** Pest PHP 3 / PHPUnit 11
- **Build:** Laravel Mix 6 (Webpack)
- **Containerization:** Docker & Docker Compose
- **CI/CD:** GitHub Actions
- **Monitoring:** Bugsnag
- **Payments:** Stripe, 2Checkout
- **External APIs:** Google My Business API, OpenAI API

## Repository Structure

```
/
├── .github/workflows/tests.yml    # CI pipeline
├── Dockerfile                     # PHP 8.4 Apache container
├── docker-compose.yml             # App + MySQL + Memcached
├── src/                           # Laravel application root
│   ├── app/
│   │   ├── Console/
│   │   │   ├── Kernel.php                              # Task scheduling
│   │   │   └── Commands/                               # Artisan commands
│   │   │       ├── AutoPostScheduledPostsCommand.php   # Publish posts (every min)
│   │   │       ├── AutoPostScheduledMediaCommand.php   # Publish media (every min)
│   │   │       ├── RefreshGoogleAccessTokenCommand.php # Token refresh (every min)
│   │   │       └── RemoveOutdatedDraftScheduledPostsCommand.php  # Cleanup (daily)
│   │   ├── Domains/                  # Core business services
│   │   │   ├── GoogleService.php               # OAuth2 token management
│   │   │   ├── GoogleMyBusinessService.php     # GMB API wrapper
│   │   │   └── OpenAIService.php               # AI text generation
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/             # Sign-in/out controllers
│   │   │   │   └── Dashboard/        # Dashboard feature controllers
│   │   │   │       ├── Posts/        # Post CRUD + scheduling
│   │   │   │       ├── Media/        # Media library management
│   │   │   │       ├── Reviews/      # Review viewing + replies
│   │   │   │       ├── Account/      # Account settings + OAuth
│   │   │   │       ├── Ai/           # AI text generation endpoint
│   │   │   │       └── Errors/       # Error pages
│   │   │   ├── Middleware/           # Auth, CSRF, admin, robots
│   │   │   ├── Requests/            # Form validation classes
│   │   │   └── Filters/             # Query filter classes
│   │   ├── Models/                   # Eloquent models
│   │   ├── Repositories/            # Data access layer
│   │   ├── Services/                # Application services
│   │   ├── Jobs/                    # Queue jobs
│   │   ├── Entities/                # Value objects / DTOs
│   │   ├── Helpers/                 # Utility classes
│   │   ├── Traits/                  # HasUUID, Filterable
│   │   ├── Rules/                   # Custom validation rules
│   │   ├── Providers/               # Service providers
│   │   └── Exceptions/              # Exception handler
│   ├── database/migrations/         # 19 migration files
│   ├── resources/views/             # 46 Blade templates
│   ├── routes/web/                  # Route definitions
│   │   ├── guest.php                # Auth routes
│   │   └── dashboard/               # Feature-based route files
│   │       ├── account.php
│   │       ├── posts.php
│   │       ├── scheduled.php
│   │       ├── media.php
│   │       ├── reviews.php
│   │       └── ai.php
│   ├── tests/
│   │   ├── Unit/                    # Model & service unit tests
│   │   └── Feature/                 # HTTP & command feature tests
│   ├── public/                      # Web root & static assets
│   ├── composer.json
│   ├── package.json
│   ├── phpunit.xml
│   └── webpack.mix.js
```

## Common Commands

All commands below run from the `src/` directory unless noted otherwise.

### Running Tests

```bash
cd src && php artisan test --env=testing
```

Tests use SQLite `:memory:` database (configured in `phpunit.xml`). No external services required.

### Building Frontend Assets

```bash
cd src && npm run dev        # Development build
cd src && npm run watch      # Watch mode with auto-rebuild
cd src && npm run prod       # Production (minified) build
```

### Docker Environment

From the repository root:

```bash
docker-compose up -d                    # Start all services
docker-compose exec gmb_app bash        # Shell into app container
docker-compose down                     # Stop all services
```

Services: `gmb_app` (PHP/Apache on :8088), `gmb_db` (MySQL on :33061), `gmb_cache` (Memcached on :11222).

### Laravel Artisan

```bash
php artisan migrate                     # Run migrations
php artisan db:seed                     # Seed database
php artisan schedule:work               # Run task scheduler locally
php artisan config:cache                # Cache config (production)
php artisan route:cache                 # Cache routes (production)
```

## Architecture & Key Patterns

### Controller Pattern

Most controllers use the single-action `__invoke()` pattern. Dashboard controllers extend `DashboardController`, which injects `UserService`, `GoogleService`, and `GoogleMyBusinessService` via constructor and validates GMB credentials.

### Domain Services (`app/Domains/`)

Business logic lives in domain services, not controllers:
- **GoogleService** - OAuth2 flows (authorize, token exchange, refresh, revoke)
- **GoogleMyBusinessService** - All GMB API calls (posts, media, reviews, locations)
- **OpenAIService** - GPT-4 text completions for post summaries and review replies

### Models

- All models use UUID primary keys (`HasUUID` trait)
- `ScheduledPost` has states: `unspecified` (draft), `processing`, `live`, `rejected`
- `ScheduledPost` has types: `STANDARD`, `EVENT`, `OFFER`, `ALERT`
- Column name constants defined on models (e.g., `USER_ID_COLUMN = 'user_id'`)
- `Filterable` trait enables query filter classes for search/filtering

### Repositories

Data access goes through repository classes (`UserRepository`, `ScheduledPostRepository`, `ScheduledMediaRepository`) that encapsulate Eloquent queries.

### Scheduled Commands

The task scheduler (`Console/Kernel.php`) runs these commands:
- **Every minute:** auto-post scheduled posts, auto-post scheduled media, refresh expired Google tokens
- **Daily at midnight:** remove outdated draft posts

### Namespaces

The project uses dual PSR-4 namespaces:
- `App\` -> `app/`
- `BADDIServices\ClnkGO\` -> `app/` (legacy namespace alias)
- `BADDIServices\Framework\` -> `core/src` (framework layer)

### Routes

Routes are organized by feature in `routes/web/`:
- `guest.php` - Authentication (sign-in/out)
- `dashboard/account.php` - Account settings, OAuth callbacks
- `dashboard/posts.php` - Published posts
- `dashboard/scheduled.php` - Scheduled posts and media
- `dashboard/media.php` - Media library
- `dashboard/reviews.php` - Reviews
- `dashboard/ai.php` - AI text generation

All dashboard routes are auth-protected. Admin routes use `is.super-admin` middleware.

## CI/CD

GitHub Actions workflow (`.github/workflows/tests.yml`):
- Triggers on push to `main` and PRs targeting `main`
- Skips draft PRs
- PHP 8.4 matrix
- Steps: checkout, setup PHP, cache Composer, copy `.env.example`, install dependencies, generate app key, run tests
- Working directory: `./src`

## Environment Configuration

Key environment variables (see `src/.env.example`):
- `APP_*` - Application settings
- `DB_*` - MySQL connection (`DB_HOST=mysql`, `DB_DATABASE=clnkgo`)
- `CACHE_DRIVER=memcached`, `MEMCACHED_HOST=memcached`
- `OPENAI_API_KEY` - For AI text generation
- `BUGSNAG_API_KEY` - Error tracking
- `HCAPTCHA_*` - Captcha (feature-flagged, disabled by default)
- `ADMINER_ENABLED` - Database admin tool (disabled by default)
- `QUEUE_CONNECTION=database` - Queue driver
- `SESSION_DRIVER=file` - Session storage

## Testing Conventions

- Test framework: Pest PHP 3 with Laravel plugin
- Unit tests: `tests/Unit/` - Models, services, object values
- Feature tests: `tests/Feature/` - HTTP endpoints, auth flows, commands
- Test environment uses SQLite in-memory, array cache, sync queue, null logger
- Run with `php artisan test --env=testing`

## Code Conventions

- **PHP 8.4** features and strict typing used throughout
- **Single-action controllers** with `__invoke()` for most endpoints
- **Form Request classes** for input validation (`app/Http/Requests/`)
- **UUID primary keys** on all models via `HasUUID` trait
- **Column name constants** on models for type-safe column references
- **Domain services** for external API integrations
- **Repository pattern** for database queries
- **Feature flags** for optional functionality (hCaptcha, cache, Adminer)
- **Laravel conventions** for naming: migrations timestamped, routes resource-style, views in `resources/views/`
