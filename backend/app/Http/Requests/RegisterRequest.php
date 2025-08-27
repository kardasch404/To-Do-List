<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
    public function rules(): array {
    return [
        'full_name' => 'required|string|min:3|max:100',
        'email'     => 'required|email|unique:users,email',
        'phone_number' => 'nullable|string|max:20',
        'address'   => 'nullable|string|max:500',
        'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'mot_de_passe'  => 'required|string|min:8',
    ];
}

}
