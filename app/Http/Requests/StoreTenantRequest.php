<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && !Auth::user()->is_employee;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:tenants,code|max:50',
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:63|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'address' => 'nullable|string|max:500',
            'business_type' => 'nullable|string|max:50',
        ];
    }
}
