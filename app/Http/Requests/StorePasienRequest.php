<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePasienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['nullable', 'digits:16', 'unique:pasien,nik'],
            'nama' => ['required', 'string', 'max:150'],
            'tgl_lahir' => ['required', 'date', 'before:today'],
            'jk' => ['required', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'telp' => ['nullable', 'string', 'max:20'],
            'gol_darah' => ['nullable', 'in:A,B,AB,O'],
            'alergi' => ['nullable', 'string'],
            'penjamin' => ['required', 'in:umum,bpjs,asuransi'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 digit.',
            'tgl_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }
}
