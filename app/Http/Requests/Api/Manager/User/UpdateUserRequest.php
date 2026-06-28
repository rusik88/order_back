<?php

namespace App\Http\Requests\Api\Manager\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('id')),],
            'name'      => ['required', 'string', 'max:255'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed', Password::defaults()]
        ];
    }
}
