# Create a New Eloquent Model

Create a new Eloquent model for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Model name and description of columns (e.g., "Subscription - user_id, plan_name, status, expires_at")

## Instructions

1. Parse the model name and column list from the argument
2. Create the model file at `src/app/Models/<ModelName>.php`
3. Follow these conventions exactly:

### Template

```php
<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use BADDIServices\ClnkGO\Entities\ModelEntity;

class <ModelName> extends ModelEntity
{
    public const string <COLUMN_NAME>_COLUMN = '<column_name>';
    // ... one constant per column

    protected $casts = [
        // Cast datetime columns to 'datetime'
        // Cast boolean columns to 'boolean'
    ];

    // Define relationships as needed
}
```

### Rules
- Namespace: `BADDIServices\ClnkGO\Models`
- Extend `ModelEntity` (provides UUID PKs via `HasUUID` trait, `Filterable` trait, `$guarded = []`, non-incrementing string PKs)
- Define every column name as a `public const string` with `_COLUMN` suffix (e.g., `public const string USER_ID_COLUMN = 'user_id'`)
- Use `$casts` for datetime and boolean columns
- Do NOT add `$guarded`, `$incrementing`, `$keyType`, or `$primaryKey` - these are inherited from `ModelEntity`
- Define enum-like values as class constants with an array mapping (see `ScheduledPost::TYPES` pattern)
- Add getter methods for accessing attributes: `getId()`, `getName()`, etc.
- Define relationships using Eloquent methods (`hasMany`, `belongsTo`, etc.) with column constants
- Also create the corresponding migration file in `src/database/migrations/`
