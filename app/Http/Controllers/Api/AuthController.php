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
     * Login aplikasi Android.
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

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda sedang tidak aktif. Silakan hubungi administrator.',
            ], 403);
        }

        /**
         * Aplikasi Android saat ini khusus digunakan oleh Pengawas.
         */
        if (! $user->isPengawas()) {
            return response()->json([
                'message' => 'Akun ini tidak memiliki akses ke aplikasi mobile.',
            ], 403);
        }

        $deviceName = $validated['device_name']
            ?? 'android-device';

        /**
         * Simpan waktu login terakhir.
         */
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        /**
         * Generate Sanctum Personal Access Token.
         */
        $token = $user
            ->createToken(
                $deviceName,
                ['mobile'],
            )
            ->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token_type' => 'Bearer',
            'token' => $token,

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
