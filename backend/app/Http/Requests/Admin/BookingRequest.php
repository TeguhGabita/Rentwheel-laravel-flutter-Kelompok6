<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobil_id' => ['required', 'exists:mobils,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['dipesan', 'berjalan', 'selesai', 'batal'])],
        ];
    }

    public function messages(): array
    {
        return [
            'mobil_id.required' => 'Mobil wajib dipilih.',
            'user_id.required' => 'Pelanggan wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ];
    }
}
