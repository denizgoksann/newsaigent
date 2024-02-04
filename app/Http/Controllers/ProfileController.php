<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Bu kısım kullanıcının kendi profil ayarları sayfasını görmesi için hazırlanan fonksiyon
    public function show(){
        $user = User::where('id', Auth::user()->id);
        return view('pages.profil', compact('user'));
    }
    // Bu kısımda kullanıcı kendi profilinin ayarlarını değiştirebilmesini sağlayan fonksiyon
    public function userUpdate(Request $request){
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $password_again = $request->password_again;
    
        $updateData = [];
        
        if (!empty($name)) {
            $updateData['name'] = $name;
        }
    
        if (!empty($email)) {
            $updateData['email'] = $email;
        }
    
        if (!empty($password) && $password == $password_again) {
            if($password == $password_again && strlen($password) < 6){
                return response()->json([
                    'success' => "short"
                ]);
            }else if(!preg_match('/[A-Z]/', $password)){
                return response()->json([
                    'success' => "no_uppercase"
                ]);
            }
            $updateData['password'] = Hash::make($password);
        } elseif (!empty($password) && $password != $password_again) {
            return response()->json([
                'success' => "password"
            ]);
        }
    
        if (empty($updateData)) {
            return response()->json([
                'success' => "empty"
            ]);
        }
    
        $user = User::where('id', Auth::id())->update($updateData);
    
        if($user){
            return response()->json([
                'success' => "success"
            ]);
        } else {
            return response()->json([
                'success' => "error"
            ]);
        }
    }
    
}
