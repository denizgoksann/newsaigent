<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwpController extends Controller
{
    // Bu kısım Washington Post'tan gelen verileri çekmemizi ve twp sayfasına yönlendirmemizi sağlayan api
    public function TwpShow(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.washingtonpost.com/prism/api/prism-query?_website=washpost&query=%7B%22query%22%3A%22prism%3A%2F%2Fprism.query%2Fsite-articles-only%2C%2Fpolitics%26offset%3D20%26limit%3D20%22%7D',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: ak_bmsc=5344611D5760AACD17A735F03BB3F2A3~000000000000000000000000000000~YAAQvIYUAm315VWNAQAAfSLSbRZ/5jNSHda5bXUE4wjBQZRdb2sFFoZYpU71ZHajsAW5YhJeLiHBImHvA/cM0DEJs0HVP6zkbKRZWfkEn63A9TtP+CJdAwlQl2Oc0wLDt1bhsyV6AZPB+gQ8WuuV/dA4dPnBZIGmHZAvX9v5fnttgGnvODoJUf8LzhusFY0RoK6NXHX9hkHbJLd4Ao1emUnzSlK2qEblmzSgvmfZ+YxUCb992R8MDycsgf0e5YA1TIjKLwXQdvW69m/4A3v4FqhQQSnZJLZAuaCfEzj02488//sAka0S5qJXk0VkyrdJWiNOpK0nG6KTU0CTbwfDxv2NihAaZ/W4IWy4FI079mv/jLc+XK1cQSFZjjLIF8dOO1DRGA==; wp_ak_om=1|20230731; wp_ak_pct=0|20230131; wp_ak_signinv2=1|20230125; wp_ak_v_mab=0|0|0|1|20231130; wp_ak_wab=0|2|1|0|0|1|0|0|1|20230418; wp_devicetype=0; wp_geo=TR||||INTL'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $twp = json_decode($response, true);
        return view('pages.twp', compact('twp'));

    }
}