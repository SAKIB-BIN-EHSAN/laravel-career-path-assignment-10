<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $userData = User::create($data);

        if ($userData) {
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'token' => $userData->createToken('url-shortener')->plainTextToken,
                'data' => UserResource::class($userData)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 400);
        }
        
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user['password'])) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are not correct!'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User logged-in successfully',
            'token' => $user->createToken('url-shortener')->plainTextToken,
            'data' => new UserResource($user)
        ], 200);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            "message"=>"User logged out successfully"
        ], 200);
    }
}
