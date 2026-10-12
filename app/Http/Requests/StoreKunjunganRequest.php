<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pasien_id' => ['required', 'exists:pasien,id'],
            'poli_id' => ['required', 'exists:poli,id'],
            'dokter_id' => ['required', 'exists:dokter,id', Rule::exists('dokter', 'id')->where('poli_id', $this->input('poli_id'))],
            'keluhan_utama' => ['nullable', 'string', 'max:1000'],
            'penjamin' => ['required', 'in:umum,bpjs,asuransi'],
            'tgl_kunjungan' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return ['dokter_id.exists' => 'Dokter tidak sesuai dengan poli yang dipilih.'];
    }
}
