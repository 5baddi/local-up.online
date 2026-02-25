# Create a New Database Migration

Create a new database migration for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Description of what the migration does (e.g., "create subscriptions table with user_id, plan, status, expires_at" or "add verified_at column to users table")

## Instructions

1. Parse the migration purpose from the argument
2. Create the migration file at `src/database/migrations/<timestamp>_<description>.php`
3. Use today's date for the timestamp prefix in format: `YYYY_MM_DD_HHMMSS`

### Rules
- All tables use UUID primary keys: `$table->uuid('id')->primary()`
- Always include `$table->timestamps()` for new tables
- Include `$table->softDeletes()` if the model uses soft deletes
- Foreign keys reference UUID columns: `$table->uuid('user_id')` with `->constrained('users')->cascadeOnDelete()`
- Use descriptive migration names:
  - New table: `create_<table_name>` (e.g., `create_subscriptions`)
  - Add column: `add_<column>_to_<table>` (e.g., `add_verified_at_to_users`)
  - Modify column: `modify_<column>_in_<table>`
  - Drop column: `drop_<column>_from_<table>`
- Always implement both `up()` and `down()` methods
- Reference the corresponding model's column constants in comments for clarity

### Example for new table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // columns here
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```
