<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'isbn' => ['required', 'string', 'max:50', 'unique:books,isbn'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'publication_year' => ['required', 'integer', 'min:-380', 'max:'.date('Y')],
            'stock' => ['required', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:30720'],
            'synopsis' => ['nullable', 'string'],
        ];
    }
}
