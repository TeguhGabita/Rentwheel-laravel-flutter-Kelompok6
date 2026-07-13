<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'tanggal_bayar' => ['required', 'date'],
            'metode_bayar' => ['required', 'string', 'max:100'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'status_bayar' => ['required', Rule::in(['pending', 'lunas', 'gagal'])],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking wajib dipilih.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
        ];
    }
}
