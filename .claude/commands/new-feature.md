# Scaffold a Complete Feature

Scaffold all files needed for a new feature in the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Feature description including the domain, resource name, and behavior (e.g., "Notifications feature - users can view and dismiss notifications with title, message, read_at columns")

## Instructions

Generate all of the following files for the feature, following the project conventions described in CLAUDE.md:

### Files to create

1. **Model** - `src/app/Models/<Name>.php`
   - Extend `ModelEntity`, use column name constants, add casts and relationships

2. **Migration** - `src/database/migrations/<timestamp>_create_<table>.php`
   - UUID primary key, timestamps, proper foreign keys

3. **Repository** (if data access is needed) - `src/app/Repositories/<Name>Repository.php`
   - Namespace: `BADDIServices\ClnkGO\Repositories`
   - Implement `paginate()` with `QueryFilter` if listing data
   - Follow `ScheduledPostRepository` pattern

4. **Controller(s)** - `src/app/Http/Controllers/Dashboard/<Domain>/<Action>Controller.php`
   - Extend `DashboardController`, use `__invoke()` for single-action
   - Use `$this->render()` for views, `Alert` for flash messages

5. **Form Request** (if accepting input) - `src/app/Http/Requests/<Name>Request.php`
   - Extend `FormRequest`, reference model column constants in rules

6. **Routes** - Add to existing or create new file in `src/routes/web/dashboard/`
   - Use named routes with dot notation
   - Group under appropriate middleware

7. **Blade Views** - `src/resources/views/dashboard/<domain>/`
   - Follow existing dashboard layout patterns

8. **Feature Test** - `src/tests/Feature/Dashboard/<Name>Test.php`
   - Pest 3.0, `RefreshDatabase`, `describe()`/`it()` blocks

### Architecture pattern
```
Route -> Controller -> Service (optional) -> Repository -> Model
                    -> FormRequest (validation)
                    -> View (Blade template)
```

### Checklist
- [ ] All namespaces use `BADDIServices\ClnkGO\` (not `App\`)
- [ ] Model extends `ModelEntity` with UUID support
- [ ] Column names defined as class constants
- [ ] Controller extends `DashboardController`
- [ ] Routes registered with proper names and middleware
- [ ] Tests cover the main happy paths
- [ ] Run `php artisan test --env=testing` from `src/` to verify tests pass
