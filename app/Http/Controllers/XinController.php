<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class XinController extends Controller
{
    // Bu kısım Xinhua Post'tan gelen verileri çekmemizi ve xin sayfasına yönlendirmemizi sağlayan api
    public function XinShow(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://english.news.cn/list/ds_a950c54446a0421f9b94c0b053efdf33.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    
        $xin = json_decode($response, true);
        return view('pages.xin', compact('xin'));
    }
}