<?php

namespace App\Http\Requests;

class PaginatedReviewsRequest extends PaginationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'has_replies' => ['nullable', 'boolean'],
            ]
        );
    }

    public function casts(): array
    {
        return [
            'has_replies' => ['boolean'],
        ];
    }
}