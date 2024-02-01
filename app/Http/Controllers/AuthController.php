<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $password_again = $request->password_again;
                
    
        if ($name == "" || $email == "" || $password == "" || $password_again == "") {
            return response()->json([
                'success' => "empty"
            ]);
        } else if ($password != $password_again) {
            return response()->json([
                'success' => "password"
            ]);
        } else if (strlen($password) < 6) {
            return response()->json([
                'success' => "short"
            ]);
        } else if (!preg_match('/[A-Z]/', $password)) {
            return response()->json([
                'success' => "no_uppercase"
            ]);
        }
    
        $existingUser = User::where('email', $email)->orWhere('name', $name)->exists();
        if ($existingUser) {
            return response()->json([
                'success' => "existing"
            ]);
        }
    
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    
        if ($user) {
            Auth::login($user); 
            return response()->json([
                'success' => "success"
            ]);
        } else {
            return response()->json([
                'success' => "error"
            ]);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json([
                'success' => "success"
            ]);
        }else{
            return response()->json([
                'success' => "error"
            ]);
        }

        return redirect()->route('index');

    }

    public function logout()
    {
       Auth::logout();
       
            return response()->json([
                'success' => 'success'
            ]);
      
    }
}
