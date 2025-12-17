<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'isbn' => [
                'required',
                'string',
                Rule::unique('books', 'isbn')->ignore($this->route('book')->id),
            ],
            'description' => 'nullable|string',
            'author_id' => 'required|exists:authors,id',
            'genre' => 'nullable|string|max:100',
            'published_date' => 'nullable|date',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cover_image' => 'nullable|url',
        ];
    }
}
