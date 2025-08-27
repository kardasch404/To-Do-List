<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $r)
    {
        try {

            $user = $this->userRepository->createUser($r->validated());

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'User registered'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $r)
    {
        try {
            $credentials = $r->validated();

            $user = $this->userRepository->findUserByEmail($credentials['email']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'email or password invalid'
                ], 401);
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'user' => [
                    'full_name' => $user->full_name,
                    'phone_number' => $user->phone_number,
                    'email' => $user->email,
                    'address' => $user->address,
                    'image' => $user->image,
                ],
                'token' => $token
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
            return response()->json([
                'success' => true,
                'message' => 'logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'logged out'
            ]);
        }
    }
}
