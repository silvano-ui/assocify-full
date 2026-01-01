<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\Users\User;

class AuthController extends BaseApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            return $this->success([
                'token' => $token,
                'user' => $user,
            ], 'Login successful');
        }

        return $this->error('Invalid credentials', 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success([], 'Logged out successfully');
    }

    public function me(Request $request)
    {
        return $this->success($request->user());
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('api_token')->plainTextToken;

        return $this->success([
            'token' => $token,
        ], 'Token refreshed');
    }
}
