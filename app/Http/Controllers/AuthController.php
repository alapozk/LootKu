<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', [
            'pageTitle' => 'Masuk | Lootku Market',
            'authMethods' => [
                ['label' => 'Email & Password', 'status' => 'Aktif sekarang'],
                ['label' => 'Google', 'status' => 'Siap ditambahkan'],
                ['label' => 'Steam', 'status' => 'Siap ditambahkan'],
                ['label' => 'WhatsApp OTP', 'status' => 'Siap ditambahkan'],
            ],
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak cocok.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($this->redirectRouteFor($request->user())))
            ->with('status', 'Berhasil masuk ke akun.');
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'pageTitle' => 'Daftar | Lootku Market',
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['buyer', 'seller'])],
            'store_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'store_name' => $validated['role'] === 'seller'
                ? ($validated['store_name'] ?: $validated['name'].' Store')
                : null,
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->redirectRouteFor($user))
            ->with('status', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda sudah logout.');
    }

    private function redirectRouteFor(User $user): string
    {
        return $user->dashboardRouteName();
    }
}
