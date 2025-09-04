<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'folder_id' => 'required|exists:pmik_folders,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul dokumen wajib diisi.',
            'file.required' => 'File PDF wajib diunggah.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'folder_id.required' => 'Folder harus dipilih.',
            'folder_id.exists' => 'Folder tidak ditemukan.',
        ];
    }
}
