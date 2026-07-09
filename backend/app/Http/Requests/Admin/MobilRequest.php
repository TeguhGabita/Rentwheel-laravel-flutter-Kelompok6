<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mobilId = $this->route('mobil')?->id;

        return [
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'nama_mobil' => ['required', 'string', 'max:255'],
            'merk' => ['required', 'string', 'max:255'],
            'plat_nomor' => [
                'required', 'string', 'max:20',
                Rule::unique('mobils', 'plat_nomor')->ignore($mobilId),
            ],
            'harga_sewa_per_hari' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['tersedia', 'disewa', 'servis'])],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'plat_nomor.unique' => 'Plat nomor ini sudah terdaftar.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
