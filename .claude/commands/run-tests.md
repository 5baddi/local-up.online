# Run Tests

Run the project test suite.

## Arguments
- $ARGUMENTS: Optional filter (e.g., "Feature/Auth", "Unit/Models", or a specific test file path)

## Instructions

1. Change to the `src/` directory
2. Run the test command based on the argument:

### Commands
- **All tests**: `cd src && php artisan test --env=testing`
- **Specific directory**: `cd src && php artisan test --env=testing tests/<path>`
- **Specific file**: `cd src && php artisan test --env=testing tests/<file>`
- **With Pest directly**: `cd src && ./vendor/bin/pest`
- **With filter**: `cd src && php artisan test --env=testing --filter="<pattern>"`

### After running
- Report the test results (passed/failed/skipped counts)
- If tests fail, analyze the failure output and suggest fixes
- If the failure is in code you recently changed, fix it and re-run

### Test environment notes
- Tests use SQLite in-memory database (configured in `.env.testing`)
- Cache, session, and queue use array/sync drivers
- No external services are called during tests
- Feature tests use `RefreshDatabase` to reset the DB between tests
