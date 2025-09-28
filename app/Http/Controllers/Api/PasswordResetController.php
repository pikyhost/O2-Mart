<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    /**
     * Send reset password link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We can’t find a user with that email address.'
            ], 404);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Reset the given user's password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        switch ($status) {
            case Password::PASSWORD_RESET:
                return response()->json([
                    'message' => 'Password has been reset successfully.'
                ], 200);

            case Password::INVALID_TOKEN:
                return response()->json([
                    'message' => 'Reset link expired, please request a new one.'
                ], 422);

            case Password::INVALID_USER:
                return response()->json([
                    'message' => 'We can’t find a user with that email address.'
                ], 404);

            default:
                return response()->json([
                    'message' => __($status) 
                ], 400);
        }
    }

    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $table = config('auth.passwords.users.table', 'password_reset_tokens');
        $record = DB::table($table)->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return response()->json(['valid' => false, 'reason' => 'invalid']);
        }

        $expires = (int) config('auth.passwords.users.expire', 60);
        $expired = Carbon::parse($record->created_at)->addMinutes($expires)->isPast();

        if ($expired) {
            return response()->json(['valid' => false, 'reason' => 'expired']);
        }

        return response()->json(['valid' => true]);
    }
}
