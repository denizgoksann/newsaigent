<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    // Bu kısımda sadece ana sayfaya erişim amaçlı Route veriyoruz
    public function index(){
        return view('index');
    }
}
