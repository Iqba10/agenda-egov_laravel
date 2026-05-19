<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->hasAnyRole($roles)) {
            return redirect()->route('profile.edit')->with('toast', [
                'type'    => 'warning',
                'message' => 'Akses terbatas. Anda hanya dapat mengelola profil akun Anda.',
            ]);
        }

        return $next($request);
    }
}
