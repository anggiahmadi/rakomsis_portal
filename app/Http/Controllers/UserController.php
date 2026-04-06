<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? Redirect::route('login.page')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function googleLogin(Request $request)
    {
        $validated = $request->validate([
            'credential' => ['required', 'string'],
        ]);

        $googleClientId = (string) config('services.google.client_id');

        if ($googleClientId === '') {
            $message = 'Google login is not configured.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 503);
            }

            return Redirect::route('login')->with('error', $message);
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $validated['credential'],
            ]);

        if (! $response->ok()) {
            $message = 'Google credential could not be verified.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return Redirect::route('login')->with('error', $message);
        }

        $googleUser = $response->json();

        if (($googleUser['aud'] ?? null) !== $googleClientId) {
            $message = 'Google credential is not intended for this application.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return Redirect::route('login')->with('error', $message);
        }

        if (($googleUser['email_verified'] ?? 'false') !== 'true') {
            $message = 'Google account email is not verified.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return Redirect::route('login')->with('error', $message);
        }

        $email = (string) ($googleUser['email'] ?? '');

        if ($email === '') {
            $message = 'Google account email is missing.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return Redirect::route('login')->with('error', $message);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => (string) ($googleUser['name'] ?? 'Google User'),
                'password' => Hash::make(Str::random(40)),
                'avatar' => $googleUser['picture'] ?? null,
                'email_verified_at' => now(),
            ]
        );

        $user->forceFill([
            'name' => $user->name ?: (string) ($googleUser['name'] ?? 'Google User'),
            'avatar' => $googleUser['picture'] ?? $user->avatar,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        if (! $user->isAdmin() && ! $user->customer()->exists()) {
            Customer::create([
                'user_id' => $user->id,
                'code' => 'CUST-' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            ]);
        }

        Auth::login($user, true);

        $request->session()->regenerate();

        $redirectUrl = $user->isAdmin() ? url('admin') : url('dashboard');

        $message = $user->isAdmin()
            ? 'Welcome back, Admin!'
            : 'Hello, ' . $user->name . '!!! How are you today? Welcome back to your dashboard!';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => $redirectUrl,
            ]);
        }

        return Redirect::to($redirectUrl)->with('success', $message);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user && $user->isAdmin()) {
                return Redirect::intended('admin')->with('success', 'Welcome back, Admin!');
            }

            return Redirect::intended('dashboard')->with('success', 'Hello, ' . $user->name . '!!! How are you today? Welcome back to your dashboard!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::route('login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Customer::create([
            'user_id' => $user->id,
            'code' => 'CUST-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
        ]);

        Auth::login($user);

        return Redirect::intended('dashboard');
    }
}
