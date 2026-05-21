<?php

namespace App\Http\Controllers;

use App\Models\AgendaDocument;
use Illuminate\Http\Response;

class DocumentController extends Controller
{
    public function show(AgendaDocument $document): Response
    {
        abort_unless($document->content, 404, 'Dokumen tidak ditemukan');

        return response($document->content, 200, [
            'Content-Type'        => $document->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
            'Content-Length'      => $document->file_size ?? strlen($document->content),
            'Cache-Control'       => 'public, max-age=86400',
        ]);
    }

    public function download(AgendaDocument $document): Response
    {
        abort_unless($document->content, 404, 'Dokumen tidak ditemukan');

        return response($document->content, 200, [
            'Content-Type'        => $document->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $document->original_name . '"',
            'Content-Length'      => $document->file_size ?? strlen($document->content),
        ]);
    }
}
