<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Lama masa berlaku token mobile.
     *
     * Token berlaku selama 6 jam sejak login berhasil.
     */
    private const TOKEN_LIFETIME_HOURS = 6;

    /**
     * Login aplikasi Android.
     *
     * Setiap login akan menghasilkan Sanctum Personal Access Token
     * yang berlaku selama 6 jam.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
            ],
            'password' => [
                'required',
                'string',
            ],
            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $username = trim($validated['username']);

        $user = User::query()
            ->where('username', $username)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Validasi username dan password
        |--------------------------------------------------------------------------
        */

        if (
            ! $user ||
            ! Hash::check($validated['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'username' => [
                    'Username atau password tidak sesuai.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi status akun
        |--------------------------------------------------------------------------
        */

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda sedang tidak aktif. Silakan hubungi administrator.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi akses aplikasi mobile
        |--------------------------------------------------------------------------
        |
        | Saat ini aplikasi Android hanya diperuntukkan bagi Pengawas.
        |
        */

        if (! $user->isPengawas()) {
            return response()->json([
                'message' => 'Akun ini tidak memiliki akses ke aplikasi mobile.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Device name
        |--------------------------------------------------------------------------
        */

        $deviceName = $validated['device_name']
            ?? 'android-device';

        /*
        |--------------------------------------------------------------------------
        | Waktu login dan expiration token
        |--------------------------------------------------------------------------
        |
        | Gunakan waktu server Laravel sebagai sumber waktu.
        |
        | Contoh:
        |
        | login     : 02:50
        | expired   : 08:50
        |
        */

        $loggedInAt = now();

        $expiresAt = $loggedInAt->copy()->addHours(
            self::TOKEN_LIFETIME_HOURS
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan waktu login terakhir
        |--------------------------------------------------------------------------
        */

        $user->forceFill([
            'last_login_at' => $loggedInAt,
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | Generate Sanctum Personal Access Token
        |--------------------------------------------------------------------------
        |
        | Parameter ketiga createToken() adalah expiresAt.
        |
        | Dengan demikian token ini secara eksplisit hanya berlaku
        | sampai waktu $expiresAt.
        |
        */

        $accessToken = $user->createToken(
            $deviceName,
            ['mobile'],
            $expiresAt,
        );

        /*
        |--------------------------------------------------------------------------
        | Response login
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' => 'Login berhasil.',

            'token_type' => 'Bearer',

            'token' => $accessToken->plainTextToken,

            /*
            | Kirim expiration ke mobile.
            |
            | Mobile akan menyimpan nilai ini di SecureStore
            | untuk mengetahui kapan session lokal harus berakhir.
            */
            'expires_at' => $expiresAt->toISOString(),

            /*
            | Waktu login server.
            */
            'logged_in_at' => $loggedInAt->toISOString(),

            /*
            | Lama session dalam jam.
            */
            'expires_in_hours' => self::TOKEN_LIFETIME_HOURS,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'position' => $user->position,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Data user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'position' => $user->position,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Logout device yang sedang digunakan.
     *
     * Hanya token yang sedang digunakan yang dihapus.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ?->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
