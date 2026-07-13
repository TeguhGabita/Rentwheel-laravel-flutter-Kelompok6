<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriMobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kategoriId = $this->route('kategori')?->id;

        return [
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('kategoris', 'nama_kategori')->ignore($kategoriId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori dengan nama ini sudah ada.',
        ];
    }
}
