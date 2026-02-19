<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use BADDIServices\ClnkGO\Models\ScheduledPost;

class ScheduledPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            ScheduledPost::SUMMARY_COLUMN       => 'required|string|min:1|max:1500',
            ScheduledPost::ACTION_TYPE_COLUMN   => sprintf(
                'required|string|in:%s', implode(',', array_keys(ScheduledPost::ACTION_TYPES))
            ),
            ScheduledPost::ACTION_URL_COLUMN    => 'required|url',
            'scheduled_date'                    => 'nullable|date',
            'scheduled_time'                    => 'nullable|date_format:H:i',
        ];

        if ($this->input(ScheduledPost::ACTION_TYPE_COLUMN) === ScheduledPost::CALL_ACTION_TYPE) {
            $rules[ScheduledPost::ACTION_URL_COLUMN] = ['nullable', 'string'];
        }

        switch ($this->input('type')) {
            case ScheduledPost::EVENT_TYPE:
                $rules = array_merge(
                    $rules,
                    [
                        ScheduledPost::EVENT_TITLE_COLUMN   => 'required|string|min:1|max:150',
                        'event_start_date'                  => 'required|date',
                        'event_start_time'                  => 'nullable|date_format:H:i',
                        'event_end_date'                    => 'required|date',
                        'event_end_time'                    => 'nullable|date_format:H:i',
                    ]
                );

                break;
            case ScheduledPost::OFFER_TYPE:
                $rules = array_merge(
                    $rules,
                    [
                        ScheduledPost::OFFER_COUPON_CODE_COLUMN         => 'nullable|string|min:1|max:150',
                        ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN   => 'nullable|url',
                        ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN    => 'nullable|string|min:1|max:1000',
                    ]
                );

                break;
            case ScheduledPost::ALERT_TYPE:
                $rules = array_merge(
                    $rules,
                    [
                        ScheduledPost::ALERT_TYPE_COLUMN => sprintf(
                            'required|string|in:%s', implode(',', array_keys(ScheduledPost::ALERT_TYPES))
                        ),
                    ]
                );

                break;
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['type' => $this->route('type', ScheduledPost::STANDARD_TYPE)]);
    }
}