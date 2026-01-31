<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationToken;
use App\Models\PasswordResetToken;
use App\Models\TwoFactorToken;
use App\Services\VeilMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(private VeilMailService $mail) {}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = VerificationToken::create([
            'email' => $user->email,
            'token' => Str::random(64),
            'expires_at' => now()->addHour(),
        ]);

        $this->mail->sendVerificationEmail($user->email, $user->name, $token->token);

        return response()->json(['message' => 'Verification email sent'], 201);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $record = VerificationToken::where('token', $request->token)->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        if ($record->expires_at->isPast()) {
            return response()->json(['error' => 'Token expired'], 400);
        }

        $user = User::where('email', $record->email)->firstOrFail();
        $user->update(['email_verified_at' => now()]);
        $record->delete();

        $this->mail->sendWelcomeEmail($user->email, $user->name);

        return response()->json(['message' => 'Email verified']);
    }

    public function login(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$user->email_verified_at) {
            $token = VerificationToken::create([
                'email' => $user->email,
                'token' => Str::random(64),
                'expires_at' => now()->addHour(),
            ]);
            $this->mail->sendVerificationEmail($user->email, $user->name, $token->token);

            return response()->json(['error' => 'Email not verified. Verification email resent.'], 403);
        }

        if ($user->two_factor_enabled) {
            $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            TwoFactorToken::create([
                'email' => $user->email,
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
            ]);
            $this->mail->sendTwoFactorCode($user->email, $code);

            return response()->json(['two_factor_required' => true]);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
    }

    public function verify2fa(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $record = TwoFactorToken::where('email', $user->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid code'], 400);
        }

        if ($record->expires_at->isPast()) {
            return response()->json(['error' => 'Code expired'], 400);
        }

        $record->delete();
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = PasswordResetToken::create([
                'email' => $user->email,
                'token' => Str::random(64),
                'expires_at' => now()->addHour(),
            ]);
            $this->mail->sendPasswordResetEmail($user->email, $token->token);
        }

        return response()->json(['message' => 'If an account exists, a reset email has been sent']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $record = PasswordResetToken::where('token', $request->token)->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        if ($record->expires_at->isPast()) {
            return response()->json(['error' => 'Token expired'], 400);
        }

        $user = User::where('email', $record->email)->firstOrFail();
        $user->update(['password' => Hash::make($request->password)]);
        $record->delete();

        $this->mail->sendPasswordChangedEmail($user->email);

        return response()->json(['message' => 'Password updated']);
    }
}
