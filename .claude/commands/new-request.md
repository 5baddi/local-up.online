# Create a New Form Request

Create a new form request validation class for the LOCAL-UP application.

## Arguments
- $ARGUMENTS: Request name and validation rules description (e.g., "UpdateSubscriptionRequest - plan_name required string, status required in:active,cancelled,expired")

## Instructions

1. Parse the request name and validation rules from the argument
2. Create the file at `src/app/Http/Requests/<RequestName>.php`
3. Follow these conventions exactly:

### Template

```php
<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class <RequestName> extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Use model column constants: ModelName::COLUMN_COLUMN => 'rules'
        ];
    }
}
```

### Rules
- Namespace: `BADDIServices\ClnkGO\Http\Requests`
- Extend `Illuminate\Foundation\Http\FormRequest`
- `authorize()` returns `true` (auth is handled by middleware)
- Reference model column constants in rule keys (e.g., `ScheduledPost::SUMMARY_COLUMN => 'required|string|min:1|max:1500'`)
- For enum validation, use `sprintf('required|string|in:%s', implode(',', array_keys(Model::ENUM_ARRAY)))` pattern
- Use `prepareForValidation()` for any input preprocessing (e.g., merging route parameters)
- Conditional rules can use `switch` on input type (see `ScheduledPostRequest` for reference)
