<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::latest()->paginate(15),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('toast', [
                    'type'    => 'error',
                    'message' => 'Tidak dapat mengubah role. Sistem harus memiliki minimal satu admin.',
                ]);
            }
        }

        $user->update($validated);

        return back()->with('toast', [
            'type'    => 'success',
            'message' => 'Role pengguna berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('toast', [
                'type'    => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('toast', [
                'type'    => 'error',
                'message' => 'Tidak dapat menghapus admin terakhir. Sistem harus memiliki minimal satu admin.',
            ]);
        }

        $user->delete();

        return back()->with('toast', [
            'type'    => 'success',
            'message' => "Akun {$user->name} berhasil dihapus.",
        ]);
    }
}
