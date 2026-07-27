<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'agenda_ids'        => ['required', 'array', 'min:1', 'max:10'],
            'agenda_ids.*'      => ['integer', 'exists:agenda,id'],
            'input_method'      => ['required', 'in:manual,file'],
            'manual_numbers'    => ['required_if:input_method,manual', 'nullable', 'string', 'max:5000'],
            'bulk_file'         => ['required_if:input_method,file', 'nullable', 'file', 'mimes:csv,txt,tsv', 'max:5120'],
            'reminder_minutes'  => ['nullable', 'integer', 'min:1'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'agenda_ids.required' => 'Pilih minimal satu agenda.',
            'agenda_ids.min'      => 'Pilih minimal satu agenda.',
            'input_method.required' => 'Pilih metode input.',
            'manual_numbers.required_if' => 'Masukkan minimal satu nomor WhatsApp.',
            'bulk_file.required_if' => 'Upload file CSV berisi data nomor WhatsApp.',
            'bulk_file.mimes'     => 'Format file harus CSV, TXT, atau TSV.',
            'bulk_file.max'       => 'Ukuran file maksimal 5 MB.',
            'reminder_minutes.min' => 'Waktu pengingat minimal 1 menit.',
        ];
    }
}
