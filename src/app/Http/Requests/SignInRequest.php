<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use BADDIServices\ClnkGO\Rules\ValidateHCaptcha;

class SignInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            User::EMAIL_COLUMN         => ['required', 'email'],
            User::PASSWORD_COLUMN      => ['required', 'string'],
            'h-captcha-response'       => [new ValidateHCaptcha()],
            'timezone'                 => ['nullable', 'string'],
        ];
    }
}