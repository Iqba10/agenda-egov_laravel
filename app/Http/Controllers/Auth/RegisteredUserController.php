<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const RESERVED_USERNAMES = [
        'admin', 'administrator', 'root', 'system', 'superuser', 'su',
        'api', 'null', 'undefined', 'guest', 'anonymous', 'support',
        'help', 'webmaster', 'postmaster', 'noreply', 'no-reply',
        'moderator', 'mod', 'staff', 'operator', 'bot',
    ];

    public function create(): View|RedirectResponse
    {
        $noUsers = User::doesntExist();

        if (! $noUsers && ! $this->isRegistrationOpen()) {
            return redirect()->route('login')->with('toast', [
                'type'    => 'warning',
                'message' => 'Pendaftaran akun saat ini tidak dibuka. Hubungi administrator.',
            ]);
        }

        return view('auth.register', [
            'isFirstSetup' => $noUsers,
        ]);
    }

    /** @throws ValidationException */
    public function store(Request $request): RedirectResponse
    {
        $noUsers = User::doesntExist();

        if (! $noUsers && ! $this->isRegistrationOpen()) {
            abort(403, 'Pendaftaran tidak dibuka.');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:users',
                Rule::notIn(self::RESERVED_USERNAMES),
            ],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.not_in' => 'Username tersebut tidak diperbolehkan, gunakan username lain.',
        ]);

        $user = DB::transaction(function () use ($request) {
            $isFirstUser = User::lockForUpdate()->count() === 0;

            return User::create([
                'name'     => $request->name,
                'username' => strtolower($request->username),
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $isFirstUser ? 'admin' : 'user',
            ]);
        });

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil. Silakan login ke akun Anda.');
    }

    private function isRegistrationOpen(): bool
    {
        return (bool) config('app.registration_open', false);
    }
}
