<?php

namespace App\Http\Controllers;

use App\Models\AgendaDocument;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function show(AgendaDocument $document): Response|StreamedResponse
    {
        $path = 'agendas/documents/' . $document->nama_file;
        
        if (Storage::disk('public')->exists($path)) {
            $mime = $document->mime_type ?: Storage::disk('public')->mimeType($path);
            if ($document->extension === 'pdf' || str_ends_with(strtolower($document->original_name ?? ''), '.pdf')) {
                $mime = 'application/pdf';
            }
            return Storage::disk('public')->response($path, $document->original_name, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
            ]);
        }

        if ($document->content) {
            $mime = $document->mime_type ?: 'application/octet-stream';
            if ($document->extension === 'pdf' || str_ends_with(strtolower($document->original_name ?? ''), '.pdf')) {
                $mime = 'application/pdf';
            }

            return response($document->content, 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
                'Content-Length'      => $document->file_size ?? strlen($document->content),
                'Cache-Control'       => 'public, max-age=86400',
            ]);
        }

        abort(404, 'Dokumen tidak ditemukan. File mungkin perlu di-upload ulang.');
    }

    public function download(AgendaDocument $document): Response|StreamedResponse
    {
        // Fallback to filesystem (old storage method)
        $path = 'agendas/documents/' . $document->nama_file;
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, $document->original_name);
        }

        if ($document->content) {
            $mime = $document->mime_type ?: 'application/octet-stream';
            if ($document->extension === 'pdf' || str_ends_with(strtolower($document->original_name ?? ''), '.pdf')) {
                $mime = 'application/pdf';
            }

            return response($document->content, 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'attachment; filename="' . $document->original_name . '"',
                'Content-Length'      => $document->file_size ?? strlen($document->content),
            ]);
        }

        abort(404, 'Dokumen tidak ditemukan. File mungkin perlu di-upload ulang.');
    }
}
