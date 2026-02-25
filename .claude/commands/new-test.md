# Create a New Test File

Create a new Pest test file for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Test type (feature/unit) and what to test (e.g., "feature Dashboard/SubscriptionTest - test subscription CRUD endpoints" or "unit Models/SubscriptionTest - test model attributes and relationships")

## Instructions

1. Parse the test type, path, and description from the argument
2. Create the test file at `src/tests/<Feature|Unit>/<Path>Test.php`
3. Follow these conventions exactly:

### Feature Test Template
```php
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('<Feature Description>', function () {
    it('<test description>', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('route.name'))
            ->assertStatus(200);
    });
});
```

### Unit Test Template
```php
<?php

describe('<Unit Description>', function () {
    it('<test description>', function () {
        // Arrange
        // Act
        // Assert
        expect($result)->toBe($expected);
    });
});
```

### Rules
- Use **Pest 3.0** syntax (not PHPUnit class-based tests)
- Feature tests MUST use `RefreshDatabase` trait: `uses(RefreshDatabase::class)`
- Unit tests run without database - do NOT use `RefreshDatabase`
- Group related tests inside `describe()` blocks
- Use `it()` for individual test cases with descriptive strings
- Use `User::factory()->create()` for creating test users
- Use `$this->actingAs($user)` for authenticated requests
- Use `$this->mock(ServiceClass::class, fn ($mock) => ...)` for mocking services
- Use named routes: `route('dashboard.posts')`, NOT raw URLs
- Use Pest expectations: `expect($value)->toBe()`, `->toBeTrue()`, `->toHaveCount()`, etc.
- Feature test files go in `src/tests/Feature/` organized by domain (Auth/, Dashboard/, Commands/)
- Unit test files go in `src/tests/Unit/` organized by layer (Models/, Domains/)
- The test environment uses SQLite in-memory (`.env.testing`), array cache, sync queue
