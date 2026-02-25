# Create a New Repository

Create a new repository class for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Repository name and the model it manages (e.g., "SubscriptionRepository for Subscription model with paginate and findByUserId methods")

## Instructions

1. Parse the repository name and model from the argument
2. Create the file at `src/app/Repositories/<RepositoryName>.php`
3. Follow these conventions exactly:

### Template

```php
<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Repositories;

use BADDIServices\ClnkGO\App;
use BADDIServices\ClnkGO\Models\<ModelName>;
use BADDIServices\ClnkGO\Http\Filters\QueryFilter;
use Illuminate\Pagination\LengthAwarePaginator;

class <RepositoryName>
{
    public function paginate(QueryFilter $queryFilter): LengthAwarePaginator
    {
        return <ModelName>::query()
            ->filter($queryFilter)
            ->paginate(App::PAGINATION_LIMIT, ['*'], 'page', $queryFilter->getPage());
    }
}
```

### Rules
- Namespace: `BADDIServices\ClnkGO\Repositories`
- No base class to extend (repositories are plain classes)
- Use `App::PAGINATION_LIMIT` for pagination (from `src/app/App.php`)
- Use `->filter($queryFilter)` for filtered queries (models use `Filterable` trait)
- Eager load relationships with `->with([...])` when appropriate
- Use model column constants for query conditions (e.g., `Model::USER_ID_COLUMN`)
- Return Eloquent models, collections, or paginators - not arrays
- Methods: `paginate()`, `findById()`, `findByUserId()`, `create()`, `update()`, `delete()` as needed
