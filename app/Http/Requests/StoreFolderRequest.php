<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:pmik_folders,name',
            'description' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama folder wajib diisi.',
            'name.unique' => 'Nama folder sudah digunakan.',
            'name.max' => 'Nama folder maksimal 255 karakter.',
        ];
    }
}
