<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022,BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use BADDIServices\ClnkGO\Models\ScheduledMedia;

class ScheduledMediaRequest extends FormRequest
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
        return [
            'id'             => 'nullable|uuid',
            'file'           => 'required|array|min:1',
            'file.*'         => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/tiff,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/mpeg,video/x-ms-wmv|max:75000',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'scheduled_frequency' => ['nullable', sprintf('in:%s', implode(',', ScheduledMedia::SCHEDULED_FREQUENCIES))],
        ];
    }
}