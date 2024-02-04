<?php

namespace App\Http\Controllers;

use App\Models\Desifre;
use App\Models\News;
use Illuminate\Http\Request;

class LiveNewsController extends Controller
{
    // Bu kısımda canlıya alınmış verileri listeliyor ve live sayfasına yönlendiriyoruz
    public function LiveShow(){
        $news = News::leftJoin('categories', 'news.category_id', '=', 'categories.id')
        ->leftJoin('bulteins', 'news.bultein_id', '=', 'bulteins.id')
        ->leftJoin('new_styles', 'news.new_style_id', '=', 'new_styles.id')
        ->where('news.durum', '1')
        ->select('news.*', 'categories.category_name', 'bulteins.bultein_name', 'new_style', 'categories.id')
        ->get();

        $desifre = Desifre::where('active', '1')->get();
        return view('pages.live', compact('news', 'desifre'));
    }
    
}

