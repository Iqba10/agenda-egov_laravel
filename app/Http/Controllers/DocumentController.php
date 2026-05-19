<?php

namespace App\Http\Controllers;

use App\Models\AgendaDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function show(AgendaDocument $document): StreamedResponse
    {
        $path = 'agendas/documents/' . $document->nama_file;

        abort_unless(Storage::disk('public')->exists($path), 404);

        $mime = Storage::disk('public')->mimeType($path);

        return Storage::disk('public')->response($path, $document->original_name, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
        ]);
    }

    public function download(AgendaDocument $document): StreamedResponse
    {
        $path = 'agendas/documents/' . $document->nama_file;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, $document->original_name);
    }
}
