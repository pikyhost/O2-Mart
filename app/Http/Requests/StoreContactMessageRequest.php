<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                auth()->check() ? 'nullable' : 'required',
                'string',
                'min:3',
                'max:255',
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:8', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }


    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.min' => 'The name must be at least 3 characters if provided.',
            'name.max' => 'The name cannot exceed 255 characters.',
            'email.required' => 'Your email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email cannot exceed 255 characters.',
            'phone.min' => 'The phone number must be at least 10 characters if provided.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'subject.required' => 'A subject is required.',
            'subject.max' => 'The subject cannot exceed 255 characters.',
            'message.required' => 'A message is required.',
            'message.min' => 'The message must be at least 10 characters.',
            'message.max' => 'The message cannot exceed 5000 characters.',
        ];
    }
}
