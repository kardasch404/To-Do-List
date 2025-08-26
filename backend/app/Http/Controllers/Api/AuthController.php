<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
   protected $userRepository; 

   public function __construct(UserRepository $userRepository)
   {
    $this->userRepository = $userRepository; 
   }

   public function register (RegisterRequest $r)
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
}

