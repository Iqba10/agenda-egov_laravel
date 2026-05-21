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
        // Try database first (new storage method)
        if ($document->content) {
            return response($document->content, 200, [
                'Content-Type'        => $document->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
                'Content-Length'      => $document->file_size ?? strlen($document->content),
                'Cache-Control'       => 'public, max-age=86400',
            ]);
        }

        // Fallback to filesystem (old storage method)
        $path = 'agendas/documents/' . $document->nama_file;
        
        if (Storage::disk('public')->exists($path)) {
            $mime = Storage::disk('public')->mimeType($path);
            return Storage::disk('public')->response($path, $document->original_name, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
            ]);
        }

        abort(404, 'Dokumen tidak ditemukan. File mungkin perlu di-upload ulang.');
    }

    public function download(AgendaDocument $document): Response|StreamedResponse
    {
        // Try database first (new storage method)
        if ($document->content) {
            return response($document->content, 200, [
                'Content-Type'        => $document->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $document->original_name . '"',
                'Content-Length'      => $document->file_size ?? strlen($document->content),
            ]);
        }

        // Fallback to filesystem (old storage method)
        $path = 'agendas/documents/' . $document->nama_file;
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, $document->original_name);
        }

        abort(404, 'Dokumen tidak ditemukan. File mungkin perlu di-upload ulang.');
    }
}
