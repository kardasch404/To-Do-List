<?php

namespace App\Repositories;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public function createUser(array $data)
    {
        $userData = [
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'address' => $data['address'],
            'mot_de_passe' => Hash::make($data['mot_de_passe']),
            'image' => $data['image'],
        ];
        $user = User::create($userData);
        return $user;
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
    public function findUserById($id)
    {
        return User::find($id);
    }

}
