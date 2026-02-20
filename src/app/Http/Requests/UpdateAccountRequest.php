<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use BADDIServices\ClnkGO\Rules\ValidateCurrentPassword;

class UpdateAccountRequest extends FormRequest
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
            User::FIRST_NAME_COLUMN    => 'required|string|min:1',
            User::LAST_NAME_COLUMN     => 'required|string|min:1',
            User::PHONE_COLUMN         => 'nullable|string|max:25',
            'current_password'         => [new ValidateCurrentPassword()],
            User::PASSWORD_COLUMN      => 'nullable|string|min:8|required_with:current_password|same:confirm_password',
            'confirm_password'         => 'nullable|string|min:8',
            'emails'                   => ['nullable', 'string'],
        ];
    }
}