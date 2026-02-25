# Create a New Dashboard Controller

Create a new single-action dashboard controller for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Description of the controller purpose and domain (e.g., "Stats/ExportStatsController - exports stats as CSV")

## Instructions

1. Determine the controller name and domain folder from the argument
2. Create the controller file at `src/app/Http/Controllers/Dashboard/<Domain>/<ControllerName>.php`
3. Follow these conventions exactly:

### Template

```php
<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\<Domain>;

use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class <ControllerName> extends DashboardController
{
    public function __invoke(): mixed
    {
        // Implementation here
    }
}
```

### Rules
- Namespace: `BADDIServices\ClnkGO\Http\Controllers\Dashboard\<Domain>`
- Extend `DashboardController` (provides `$this->user`, `$this->googleMyBusinessService`, `$this->googleService`, `$this->userService`)
- Use `__invoke()` for single-action controllers
- Use `$this->render('dashboard.<view>', [...])` to return views
- Use `abort_if()` for not-found checks
- Flash messages use `Alert` entity class
- Import types from existing codebase models/services, not Laravel facades when possible
- Add the route to the appropriate file in `src/routes/web/dashboard/`
