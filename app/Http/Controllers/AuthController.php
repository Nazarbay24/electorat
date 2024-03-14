<?php

namespace App\Http\Controllers;

use App\Models\ProfileManager;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = ProfileManager::where('login', $request->login)
            ->where('parol', $request->password)
            ->where('status', 1)
            ->first();

        if( !$user ) {
            return response()->json(['message' => __('Неверный логин или пароль')], 400);
        }

        $user->last_visit = date("Y-m-d H:i:s");
        $user->save();

        $token = $user->createToken($user->id, ['manager']);

        return response()->json([
            'token' => $token->plainTextToken,
        ], 200);
    }

    public function logout(Request $request) {
        if($request->user()->currentAccessToken()->delete()) {
            return response()->json([
                'message' => 'success',
            ], 200);
        }
        else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
