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
        
        // Fetch all to calculate stats and handle computed statuses
        $allAgendas = (clone $base)
            ->select(['id', 'status', 'waktu_mulai', 'waktu_selesai'])
            ->get()
            ->map(function ($agenda) {
                return $agenda->computedStatus();
            });

        $statsCounts = $allAgendas->countBy();

        $agendas = (clone $base)
            ->select(['id', 'slug', 'jenis_agenda', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'asal_surat', 'status', 'diinput_oleh'])
            ->status($status)
            ->latest('waktu_mulai')
            ->paginate(12)
            ->withQueryString();

        return view('admin.agendas.index', [
            'agendas' => $agendas,
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
        $startDate = $request->string('start_date')->toString();
        $endDate = $request->string('end_date')->toString();

        $query = $this->filteredQuery($request);

        // Filter by date range
        if ($startDate) {
            $query->whereDate('waktu_mulai', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('waktu_mulai', '<=', $endDate);
        }

        return view('admin.agendas.print', [
            'agendas' => $query->status($status)->latest('waktu_mulai')->get(),
            'filters' => [
                'status'     => $status,
                'search'     => $request->string('search')->toString(),
                'month'      => $request->string('month')->toString(),
                'year'       => $request->string('year')->toString(),
                'start_date' => $startDate,
                'end_date'   => $endDate,
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

            $sourcePath = $file->getRealPath() ?: $file->getPathname();
            if (! $sourcePath || ! is_file($sourcePath) || ! is_readable($sourcePath)) {
                \Log::warning('Upload source path is not readable', [
                    'name' => $originalName,
                    'path' => $sourcePath,
                ]);
                continue;
            }

            $content = file_get_contents($sourcePath);
            if ($content === false || $content === '') {
                \Log::warning('Failed to read uploaded file content', [
                    'name' => $originalName,
                    'path' => $sourcePath,
                ]);
                throw new \RuntimeException("File {$originalName} kosong atau tidak bisa dibaca.");
            }

            if ($extension === '' && str_starts_with($content, '%PDF-')) {
                $extension = 'pdf';
            }

            if ($extension === '') {
                $mimeType = $this->detectMimeFromContent($content);
                $extension = match ($mimeType) {
                    'application/pdf' => 'pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'application/msword' => 'doc',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    default => 'bin',
                };
            }

            $fileName = date('YmdHis') . '_' . Str::random(10) . '.' . ($extension !== '' ? $extension : 'bin');
            $storageRelativePath = 'agendas/documents/' . $fileName;
            Storage::disk('public')->put($storageRelativePath, $content);
            $storageFullPath = storage_path('app/public/' . $storageRelativePath);

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

        // Fallback to raw PHP $_FILES if Laravel didn't hydrate the uploads.
        if (empty($files) && isset($_FILES['documents'])) {
            foreach ($this->normalizeRawFiles($_FILES['documents']) as $rawFile) {
                $files[] = $rawFile;
            }
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

    /**
     * Normalize raw $_FILES payload into UploadedFile instances.
     */
    private function normalizeRawFiles(array $rawFiles): array
    {
        $normalized = [];

        if (! isset($rawFiles['name'])) {
            return $normalized;
        }

        $names = $rawFiles['name'];
        $types = $rawFiles['type'] ?? [];
        $tmps = $rawFiles['tmp_name'] ?? [];
        $errors = $rawFiles['error'] ?? [];
        $sizes = $rawFiles['size'] ?? [];

        $iterate = function ($name, $type, $tmp, $error, $size) use (&$normalized, &$iterate) {
            if (is_array($name)) {
                foreach ($name as $index => $item) {
                    $iterate(
                        $item,
                        $type[$index] ?? null,
                        $tmp[$index] ?? null,
                        $error[$index] ?? null,
                        $size[$index] ?? null
                    );
                }
                return;
            }

            if (! is_string($tmp) || $tmp === '' || ! is_file($tmp)) {
                return;
            }

            $normalized[] = new UploadedFile(
                $tmp,
                (string) $name,
                is_string($type) ? $type : null,
                (int) ($error ?? UPLOAD_ERR_OK),
                true
            );
        };

        $iterate($names, $types, $tmps, $errors, $sizes);

        return $normalized;
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
