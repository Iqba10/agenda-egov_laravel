<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'jenis_agenda' => ['required', 'in:internal,eksternal'],
            'perihal_kegiatan' => ['required', 'string'],
            'waktu_mulai' => ['required', 'date'],
            'waktu_selesai' => ['required', 'date', 'after_or_equal:waktu_mulai'],
            'tempat' => ['required', 'string', 'max:255'],
            'asal_surat' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['nullable', 'date'],
            'pakaian' => ['nullable', 'string', 'max:255'],
            'disposisi' => ['nullable', 'string'],
            'petugas_ditugaskan' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:terjadwal,selesai,dibatalkan'],
            'keterangan' => ['nullable', 'string'],
            'documents' => ['nullable', 'array', 'max:5'],
            // Allow upload first; controller will enforce byte-level validation and file integrity.
            'documents.*' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.max' => 'Maksimal 5 dokumen per upload.',
            'documents.*.file' => 'File :position gagal diupload. Coba file lain.',
            'documents.*.required' => 'File :position wajib diisi.',
        ];
    }
}
