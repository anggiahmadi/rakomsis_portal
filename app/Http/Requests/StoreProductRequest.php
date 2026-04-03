<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_per_location' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'price_per_user' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'product_type' => ['required', 'in:single,bundle'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'This product code is already in use.',
            'price_per_location.decimal' => 'Price per location must have at most 2 decimal places.',
            'price_per_user.decimal' => 'Price per user must have at most 2 decimal places.',
            'tax_percentage.decimal' => 'Tax percentage must have at most 2 decimal places.',
        ];
    }
}
