<?php

namespace App\Services;

use Illuminate\Support\Facades;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(){}

    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if(! Auth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return Auth::user()->toArray();
    }
}