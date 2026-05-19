<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicAgendaController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->toString();
        $month = $request->string('month')->toString();
        $year = $request->string('year')->toString();

        $baseQuery = Agenda::query();

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('perihal_kegiatan', 'like', "%{$search}%")
                  ->orWhere('tempat', 'like', "%{$search}%")
                  ->orWhere('asal_surat', 'like', "%{$search}%");
            });
        }

        if ($month) {
            $baseQuery->whereMonth('waktu_mulai', $month);
        }

        if ($year) {
            $baseQuery->whereYear('waktu_mulai', $year);
        }

        $statsCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $query = (clone $baseQuery)->status($status);

        return view('agenda.index', [
            'agendas' => $query->select(['id', 'slug', 'jenis_agenda', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'asal_surat', 'status'])->latest('waktu_mulai')->paginate(10)->withQueryString(),
            'stats' => [
                'total'       => $statsCounts->sum(),
                'terjadwal'   => $statsCounts->get('terjadwal', 0),
                'berlangsung' => $statsCounts->get('berlangsung', 0),
                'selesai'     => $statsCounts->get('selesai', 0),
                'dibatalkan'  => $statsCounts->get('dibatalkan', 0),
            ],
            'status' => $status,
            'search' => $search,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function show(Agenda $agenda): View
    {
        return view('agenda.show', [
            'agenda'         => $agenda->loadMissing('documents'),
            'relatedAgendas' => Agenda::query()
                ->select(['id', 'slug', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'status'])
                ->whereKeyNot($agenda->id)
                ->latest('waktu_mulai')
                ->limit(10)
                ->get(),
        ]);
    }
}
