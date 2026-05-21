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
use Illuminate\Support\Str;

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

        if ($request->hasFile('documents')) {
            $this->uploadDocuments($agenda, $request->file('documents'));
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

        if ($request->hasFile('documents')) {
            $this->uploadDocuments($agenda, $request->file('documents'));
        }

        return redirect()->route('admin.agendas.show', $agenda)->with('toast', [
            'type'    => 'success',
            'message' => 'Agenda berhasil diperbarui.',
        ]);
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        // Delete documents from storage
        foreach ($agenda->documents as $doc) {
            Storage::disk('public')->delete('agendas/documents/' . $doc->nama_file);
        }

        $agenda->delete();

        return redirect()->route('admin.dashboard')->with('toast', [
            'type'    => 'success',
            'message' => 'Agenda berhasil dihapus.',
        ]);
    }

    public function destroyDocument(Agenda $agenda, AgendaDocument $document): JsonResponse|RedirectResponse
    {
        Storage::disk('public')->delete('agendas/documents/' . $document->nama_file);
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
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $contentHash  = hash_file('sha256', $file->getRealPath());

            if (AgendaDocument::query()->where('content_hash', $contentHash)->exists()) {
                continue;
            }

            $fileName = date('YmdHis') . '_' . Str::random(10) . '.' . $extension;
            $file->storeAs('agendas/documents', $fileName, 'public');

            $agenda->documents()->create([
                'nama_file'     => $fileName,
                'original_name' => $originalName,
                'content_hash'  => $contentHash,
            ]);
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
