<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Requests\UpdateAgendaRequest;
use App\Models\Agenda;
use App\Models\AgendaDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $status      = $request->string('status')->toString();
        $base        = $this->filteredQuery($request);
        
        // Calculate effective status counts based on timestamps
        $statsCounts = (clone $base)
            ->selectRaw("
                CASE
                    WHEN status = 'dibatalkan' THEN 'dibatalkan'
                    WHEN NOW() < waktu_mulai THEN 'terjadwal'
                    WHEN NOW() BETWEEN waktu_mulai AND waktu_selesai THEN 'berlangsung'
                    ELSE 'selesai'
                END as effective_status,
                COUNT(*) as count
            ")
            ->groupByRaw("
                CASE
                    WHEN status = 'dibatalkan' THEN 'dibatalkan'
                    WHEN NOW() < waktu_mulai THEN 'terjadwal'
                    WHEN NOW() BETWEEN waktu_mulai AND waktu_selesai THEN 'berlangsung'
                    ELSE 'selesai'
                END
            ")
            ->pluck('count', 'effective_status');

        return view('admin.agendas.index', [
            'agendas' => (clone $base)->status($status)->select(['id', 'slug', 'jenis_agenda', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'asal_surat', 'status', 'diinput_oleh'])->latest('waktu_mulai')->paginate(12)->withQueryString(),
            'stats'   => [
                'total'       => $statsCounts->sum(),
                'terjadwal'   => $statsCounts->get('terjadwal', 0),
                'berlangsung' => $statsCounts->get('berlangsung', 0),
                'selesai'     => $statsCounts->get('selesai', 0),
                'dibatalkan'  => $statsCounts->get('dibatalkan', 0),
            ],
            'status'  => $status,
            'search'  => $request->string('search')->toString(),
            'month'   => $request->string('month')->toString(),
            'year'    => $request->string('year')->toString(),
        ]);
    }

    public function print(Request $request): View
    {
        $status = $request->string('status')->toString();

        return view('admin.agendas.print', [
            'agendas' => $this->filteredQuery($request)->status($status)->latest('waktu_mulai')->get(),
            'filters' => [
                'status' => $status,
                'search' => $request->string('search')->toString(),
                'month'  => $request->string('month')->toString(),
                'year'   => $request->string('year')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.agendas.create', ['agenda' => new Agenda()]);
    }

    public function store(StoreAgendaRequest $request): RedirectResponse
    {
        $agenda = Agenda::create($request->validated() + [
            'diinput_oleh' => $request->user()->name,
            'created_by'   => $request->user()->id,
        ]);

        $documents = $this->collectUploadedDocuments($request);
        if (empty($documents) && $request->hasFile('documents')) {
            \Log::warning('documents input present but no uploaded files collected', [
                'all_files_keys' => array_keys($request->allFiles()),
                'files_count' => count($request->allFiles()),
            ]);
        }

        if (! empty($documents)) {
            try {
                $this->uploadDocuments($agenda, $documents);
            } catch (\Throwable $e) {
                \Log::error('Document upload failed', ['error' => $e->getMessage()]);
                return redirect()->route('admin.agendas.show', $agenda)->with('toast', [
                    'type'    => 'warning',
                    'message' => 'Agenda ditambahkan, tapi upload dokumen gagal: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.agendas.show', $agenda)->with('toast', [
            'type'    => 'success',
            'message' => 'Agenda berhasil ditambahkan.',
        ]);
    }

    public function show(Agenda $agenda): View
    {
        return view('admin.agendas.show', [
            'agenda'         => $agenda->loadMissing('documents'),
            'relatedAgendas' => Agenda::query()
                ->select(['id', 'slug', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'status'])
                ->whereKeyNot($agenda->id)
                ->latest('waktu_mulai')
                ->limit(8)
                ->get(),
        ]);
    }

    public function edit(Agenda $agenda): View
    {
        return view('admin.agendas.edit', ['agenda' => $agenda->load('documents')]);
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda): RedirectResponse
    {
        $agenda->update($request->validated());

        $documents = $this->collectUploadedDocuments($request);
        if (empty($documents) && $request->hasFile('documents')) {
            \Log::warning('documents input present but no uploaded files collected', [
                'all_files_keys' => array_keys($request->allFiles()),
                'files_count' => count($request->allFiles()),
            ]);
        }

        if (! empty($documents)) {
            try {
                $this->uploadDocuments($agenda, $documents);
            } catch (\Throwable $e) {
                \Log::error('Document upload failed', ['error' => $e->getMessage()]);
                return redirect()->route('admin.agendas.show', $agenda)->with('toast', [
                    'type'    => 'warning',
                    'message' => 'Agenda diperbarui, tapi upload dokumen gagal: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.agendas.show', $agenda)->with('toast', [
            'type'    => 'success',
            'message' => 'Agenda berhasil diperbarui.',
        ]);
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        foreach ($agenda->documents as $doc) {
            $this->deleteDocumentFile($doc);
        }

        $agenda->delete();

        return redirect()->route('admin.dashboard')->with('toast', [
            'type'    => 'success',
            'message' => 'Agenda berhasil dihapus.',
        ]);
    }

    public function destroyDocument(Agenda $agenda, AgendaDocument $document): JsonResponse|RedirectResponse
    {
        $this->deleteDocumentFile($document);
        $document->delete();

        if (request()->wantsJson() || request()->hasHeader('X-HTTP-Method-Override')) {
            return response()->json(['success' => true]);
        }

        return back()->with('toast', [
            'type'    => 'success',
            'message' => 'Dokumen berhasil dihapus.',
        ]);
    }

    private function uploadDocuments(Agenda $agenda, array $files): void
    {
        $hasContentColumn = Schema::hasColumn('dokumen_agenda', 'content');
        $hasMimeColumn = Schema::hasColumn('dokumen_agenda', 'mime_type');
        $hasSizeColumn = Schema::hasColumn('dokumen_agenda', 'file_size');

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension    = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
            $fileSize     = (int) $file->getSize();
            \Log::info('Processing uploaded document', [
                'name' => $originalName,
                'extension' => $extension,
                'mime' => $extension,
                'size' => $fileSize,
                'error' => $file->getError(),
            ]);

            if ($file->getError() !== UPLOAD_ERR_OK) {
                \Log::warning('Uploaded file has non-OK error code', [
                    'name' => $originalName,
                    'error' => $file->getError(),
                    'message' => $file->getErrorMessage(),
                ]);
            }

            $fileName = date('YmdHis') . '_' . Str::random(10) . '.' . ($extension !== '' ? $extension : 'bin');
            $storageRelativePath = 'agendas/documents/' . $fileName;
            $storageFullPath = storage_path('app/public/' . $storageRelativePath);
            if (! is_dir(dirname($storageFullPath))) {
                mkdir(dirname($storageFullPath), 0777, true);
            }

            try {
                $file->storeAs('agendas/documents', $fileName, 'public');
            } catch (\Throwable $storeError) {
                \Log::warning('Failed to store uploaded document', [
                    'name' => $originalName,
                    'file_name' => $fileName,
                    'error' => $storeError->getMessage(),
                ]);
            }

            $content = '';
            if (is_file($storageFullPath) && is_readable($storageFullPath)) {
                $content = file_get_contents($storageFullPath) ?: '';
            }

            $contentHash = $content !== ''
                ? hash('sha256', $content)
                : hash('sha256', $originalName . '|' . $fileSize . '|' . $fileName);

            // Skip duplicate files only for THIS agenda
            if ($agenda->documents()->where('content_hash', $contentHash)->exists()) {
                \Log::info('Skipping duplicate uploaded document', [
                    'name' => $originalName,
                    'hash' => $contentHash,
                    'agenda_id' => $agenda->id,
                ]);
                if (is_file($storageFullPath)) {
                    @unlink($storageFullPath);
                }
                continue;
            }

            $dbContent = ($hasContentColumn && $content !== '') ? $content : null;

            $attributes = [
                'nama_file'     => $fileName,
                'original_name' => $originalName,
                'content_hash'  => $contentHash,
            ];

            if ($hasContentColumn) {
                $attributes['content'] = $dbContent;
            }

            if ($hasMimeColumn) {
                $attributes['mime_type'] = $this->detectMimeType($extension, $content);
            }

            if ($hasSizeColumn) {
                $attributes['file_size'] = is_file($storageFullPath) ? filesize($storageFullPath) : ($file->getSize() ?: null);
            }

            $agenda->documents()->create($attributes);
        }
    }

    private function deleteDocumentFile(AgendaDocument $document): void
    {
        $path = 'agendas/documents/' . $document->nama_file;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function detectMimeType(string $extension, string $content): string
    {
        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => $this->detectMimeFromContent($content) ?? 'application/octet-stream',
        };
    }

    private function detectMimeFromContent(string $content): ?string
    {
        if (str_starts_with($content, '%PDF-')) {
            return 'application/pdf';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($content);

        return is_string($mime) ? $mime : null;
    }

    /**
     * Collect uploaded files from any documents-related input key.
     */
    private function collectUploadedDocuments(Request $request): array
    {
        $files = [];

        foreach ($request->allFiles() as $key => $value) {
            if ($key !== 'documents' && ! str_starts_with($key, 'documents')) {
                continue;
            }

            $this->flattenUploadedFiles($value, $files);
        }

        return $files;
    }

    /**
     * Recursively flatten nested uploaded file arrays.
     */
    private function flattenUploadedFiles(mixed $value, array &$files): void
    {
        if ($value instanceof UploadedFile) {
            $files[] = $value;
            return;
        }

        if (! is_array($value)) {
            return;
        }

        foreach ($value as $item) {
            $this->flattenUploadedFiles($item, $files);
        }
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = Agenda::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(fn (Builder $q) => $q
                ->where('perihal_kegiatan', 'like', "%{$search}%")
                ->orWhere('tempat', 'like', "%{$search}%")
                ->orWhere('asal_surat', 'like', "%{$search}%")
            );
        }

        if ($month = $request->string('month')->toString()) {
            $query->whereMonth('waktu_mulai', $month);
        }

        if ($year = $request->string('year')->toString()) {
            $query->whereYear('waktu_mulai', $year);
        }

        return $query;
    }
}
