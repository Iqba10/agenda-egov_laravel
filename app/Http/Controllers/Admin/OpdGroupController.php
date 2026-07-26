<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpdGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpdGroupController extends Controller
{
    public function index()
    {
        $groups = OpdGroup::latest()->get();
        return view('admin.opd-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.opd-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|string|unique:opd_groups,group_id',
            'description' => 'nullable|string',
        ]);

        OpdGroup::create($validated);

        return redirect()->route('admin.opd-groups.index')
            ->with('success', 'Grup OPD berhasil ditambahkan.');
    }

    public function edit(OpdGroup $opdGroup)
    {
        return view('admin.opd-groups.edit', compact('opdGroup'));
    }

    public function update(Request $request, OpdGroup $opdGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|string|unique:opd_groups,group_id,' . $opdGroup->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $opdGroup->update($validated);

        return redirect()->route('admin.opd-groups.index')
            ->with('success', 'Grup OPD berhasil diperbarui.');
    }

    public function destroy(OpdGroup $opdGroup)
    {
        $opdGroup->delete();

        return redirect()->route('admin.opd-groups.index')
            ->with('success', 'Grup OPD berhasil dihapus.');
    }

    /**
     * Fetch WhatsApp group list from Fonnte API
     */
    public function fetchGroups()
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'Fonnte token tidak dikonfigurasi',
            ], 400);
        }

        try {
            // First, update group list
            $updateResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/fetch-group');

            Log::info('Fonnte fetch-group response', [
                'status' => $updateResponse->status(),
                'body' => $updateResponse->body(),
            ]);

            // Then, get group list
            $getResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/get-whatsapp-group');

            Log::info('Fonnte get-whatsapp-group response', [
                'status' => $getResponse->status(),
                'body' => $getResponse->body(),
            ]);

            if ($getResponse->successful()) {
                $data = $getResponse->json();

                if (isset($data['data']) && is_array($data['data'])) {
                    return response()->json([
                        'success' => true,
                        'groups' => $data['data'],
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'error' => $data['detail'] ?? 'Gagal mengambil daftar grup',
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Gagal menghubungi Fonnte API',
            ], $getResponse->status());

        } catch (\Throwable $e) {
            Log::error('Fonnte fetch groups error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
