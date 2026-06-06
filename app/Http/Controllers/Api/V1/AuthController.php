<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Handle user login via API
     */
    public function login(LoginRequest $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        if (Auth::attempt([$fieldType => $login, 'password' => $password])) {
            $user = Auth::user();
            
            // Delete existing tokens to ensure only one active session per device if needed
            // Or just create a new one
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'must_change_password' => (bool) $user->must_change_password
            ], 'Login berhasil');
        }

        return $this->errorResponse('Kredensial tidak valid', 401);
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request)
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Data profil berhasil diambil'
        );
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil');
    }

    /**
     * Handle change password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false
        ]);

        return $this->successResponse(
            new UserResource($user),
            'Password berhasil diubah'
        );
    }
}
