<?php

namespace App\Http\Requests\Api\Manager\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name'      => ['required', 'string', 'max:255'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'password'  => ['required', 'string', 'min:8', 'confirmed', Password::defaults()]
        ];
    }
}
