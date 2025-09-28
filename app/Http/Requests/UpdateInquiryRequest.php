<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Inquiry;

class UpdateInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add authorization logic as needed
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(array_keys(Inquiry::STATUSES))],
            'priority' => ['sometimes', Rule::in(array_keys(Inquiry::PRIORITIES))],
            'admin_notes' => 'nullable|string|max:1000',
            'quoted_price' => 'nullable|numeric|min:0|max:999999.99',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }
}
