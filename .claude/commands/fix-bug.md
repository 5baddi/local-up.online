# Debug and Fix a Bug

Investigate and fix a bug in the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Bug description, error message, or symptoms (e.g., "500 error when saving a scheduled post with event type" or "Google OAuth callback returns blank page")

## Instructions

### Investigation steps

1. **Understand the symptom** - Parse the bug description from the argument
2. **Locate relevant code** - Search for related controllers, models, services, and routes
3. **Trace the request flow**:
   - Route definition in `src/routes/web/dashboard/`
   - Controller in `src/app/Http/Controllers/Dashboard/`
   - Form request validation in `src/app/Http/Requests/`
   - Service/Repository logic
   - Blade view in `src/resources/views/dashboard/`
4. **Check for common issues**:
   - Missing/wrong route names or parameters
   - Validation rule mismatches
   - Incorrect column constants or model relationships
   - Missing `use` imports
   - GMB credential checks in `DashboardController` middleware
   - OAuth token expiration issues
5. **Read test files** for the affected area to understand expected behavior

### Fix steps

1. Make the minimal fix required
2. Verify the fix doesn't break existing tests: `cd src && php artisan test --env=testing`
3. Add a test case covering the bug if one doesn't exist

### Key debugging locations
- **Auth issues**: `src/app/Http/Controllers/Auth/`, middleware in `src/app/Http/Middleware/`
- **Dashboard 302 redirects**: Check `DashboardController` GMB credential middleware
- **API errors**: `src/app/Domains/GoogleMyBusinessService.php`, check Guzzle responses
- **Validation errors**: `src/app/Http/Requests/` form request classes
- **Scheduling issues**: `src/app/Console/Commands/` artisan commands
- **Logging**: `AppLogger` singleton wraps Bugsnag + Laravel Log
