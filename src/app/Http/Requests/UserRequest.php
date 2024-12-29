<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rule = $this->method == 'POST' ? 'required' : 'nullable';
        return [
            'name' => $rule.'|string',
            'email' => $rule.'|email|unique:users',
            'password' => $rule.'|string|min:6',
        ];
    }
}
