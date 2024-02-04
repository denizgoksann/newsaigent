<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NytController extends Controller
{

    // Bu kısım Newyork Times'dan gelen verileri çekmemizi ve nyt sayfasına yönlendirmemizi sağlayan api
    public function NytShow(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.nytimes.com/svc/news/v3/content/nyt/world.json?api-key=21rIPFc4Qlb84VAu79YUjwygL9WtwqYj',
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
    
        $nyt = json_decode($response, true);
        return view('pages.nyt', compact('nyt'));
    }
}
