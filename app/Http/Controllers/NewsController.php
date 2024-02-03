<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{

    public function NewsShow(){
      if(Auth::check()){
        $news = News::where('user_id', Auth::user()->id)
        ->get();
        $category = Category::all();
        return view('pages.news', compact('news', 'category'));
      }else{
        return view('index');
      }
    }
    
    public function CreateNews(Request $req){
        $news_title = $req->news_title;
        $news_text = $req->news_text;
        $uniq_words = $req->uniq_words;
        $spot = $req->spot;
        $editor = $req->editor;
        $location = $req->location;
        
        if(empty($news_title)){
            return response()->json(['success' => 'emptyTitle']);
        }else if(empty($uniq_words)){
            return response()->json(['success' =>'uniqWords']);
        }else if(empty($news_text)){
            return response()->json(['success' =>'emptyText']);
        }else if(empty($editor)){
            return response()->json(['success' =>'emptyEditor']);
        }else{
         
            $news = News::create([
                'user_id' => Auth::user()->id,
                'news_title' => $news_title,
                'news_draft' => $news_text,
                'uniq_words' => $uniq_words,
                'news_draft' => $news_text,
                'spot' => $spot,
                'location' => $location,
                'editor' => $editor
            ]);

            $news_id = DB::getPdo()->lastInsertId();
            try {
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "Bana Anadolu Ajans tarzında $news_title haber başlığına uygun, içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği haber metinleri yaz konusu $spot olsun her bir metin en az 200 kelime olsun ve en az 3 farklı metin metni olsun. Her metnin sonuna /++ sembolünü koy"
                                ]
                            ]
                        ]
                    ]
                ];
                $response = $this->gemini($data);

                if (is_object($response) && isset($response->candidates[0]->content->parts)) {
                    $parts = $response->candidates[0]->content->parts;
                    $spotContents = array_map(function ($part) {
                        return $part->text;
                    }, $parts);
            
                    // Burada $spotContents, istediğiniz metinlerin bir dizisi olacak
                    // Bu metinleri birleştirin veya gerektiği şekilde işleyin
                    $spotContentString = implode(" ", $spotContents);
            
                    // Veritabanını güncelle
                    News::where('id', $news_id)->update([
                        'text' => $spotContentString
                    ]);
                } else {
                    throw new Exception("Invalid response format");
                }
            
            }catch(Exception $e){
                return response()->json(['success' => 'system', 'response' => $response]);
            }

            if($news) {
                return response()->json(['success' => 'success', 'response' => $response]);
            } else {
                return response()->json(['success' => 'error', 'response' => $response]);
            }
        }
       
    }

    public function seeMessage(Request $req){
        $data = News::where('id', $req->dataID)->first();
        return response()->json(['success' => true, 'data' => $data]);
    }
    public function historyNews() {
        $news = News::where('user_id', Auth::user()->id)
        ->where('news', '!=', '')
        ->orderBy('created_at', 'desc')
        ->get();
            $dataHtml = "";
            foreach ($news as $item) {
                $dataHtml .= '
                    <div class="d-flex flex-column see_message p-2 mb-2" data-id="'.$item->id.'">
                        <div class="news_history_content">
                            <div class="d-flex justify-content-between align-items-center w-100 p-1 ">
                                <span class="news_history_content_title">'.$item->news_title.'</span>
                                <span class="news_time">'.$item->created_at.'</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center w-100 p-1">
                                <span class="news_content_text">';

                            if($item->news){
                                if(strlen($item->news) > 50) {
                                    $dataHtml .= mb_convert_encoding( substr($item->news, 0, 50) . '...' ,  "UTF-8" , "UTF-8");
                                } else {
                                    $dataHtml .= $item->news;
                                }
                            }else{
                                $dataHtml .=  '';
                            }


                $dataHtml .= '</span>
                            </div>
                        </div>
                    </div>';
            }
        
        return response()->json(['success' => true, 'data' => $dataHtml]);
    }

    private function httpPost($url,$params) { 
        $ch = curl_init( $url );
        # Setup request to send json via POST.
        $payload = json_encode( $params );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , 'Authorization:Bearer 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'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.
        return $result;
    }
    public function lastNew(Request $req){
            $newsID = $req->news_id;
            $indexID = $req->indexID - 1;
            
            $arrNews = News::where('id', $newsID)->first();
        
            $arrNewsParse = explode('/++', $arrNews->news);

            $arrNewsParse[$indexID] = $req->news_last;

            $arrNewsStr = implode('/++', $arrNewsParse);
        
            $update = News::where('id', $newsID)->update(['news' => $arrNewsStr]); 

        if($update){
            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['success' => 'error']);

        }
    }

    public function lastNewReturn(Request $req){
        $news_title = $req->news_title;
        $news_text = $req->news_text;
        $uniq_words = $req->uniq_words;
        $spot = $req->spot;
        $editor = $req->editor;
        $location = $req->location;
        $newsID = $req->newsID;
        if(empty($news_title)){
            return response()->json(['success' => 'emptyTitle']);
        }else if(empty($uniq_words)){
            return response()->json(['success' =>'uniqWords']);
        }else if(empty($news_text)){
            return response()->json(['success' =>'emptyText']);
        }else if(empty($editor)){
            return response()->json(['success' =>'emptyEditor']);
        }else{
            $news = News::create([
                'user_id' => Auth::user()->id,
                'news_title' => $news_title,
                'news_draft' => $news_text,
                'uniq_words' => $uniq_words,
                'spot' => $spot,
                'location' => $location,
                'editor' => $editor
            ]);
            $news_id = DB::getPdo()->lastInsertId();
                try {
                    $data = [
                        "contents" => [
                            [
                                "parts" => [
                                    [
                                        "text" => "Bana Anadolu Ajans tarzında $news_title haber başlığına uygun, içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği haber metinleri yaz konusu $news_text olsun her bir metin en az 500 kelime olsun ve en az 3 farklı metin olsun. Her metnin sonuna /++ sembolünü koy"
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $response = $this->gemini($data);

                    if (is_object($response) && isset($response->candidates[0]->content->parts)) {
                        $parts = $response->candidates[0]->content->parts;
                        $spotContents = array_map(function ($part) {
                            return $part->text;
                        }, $parts);
                
                        // Burada $spotContents, istediğiniz metinlerin bir dizisi olacak
                        // Bu metinleri birleştirin veya gerektiği şekilde işleyin
                        $spotContentString = implode(" ", $spotContents);
                
                        // Veritabanını güncelle
                        News::where('id', $news_id)->update([
                            'news' => $spotContentString
                        ]);
                    } else {
                        throw new Exception("Invalid response format");
                    }
                
            }catch(Exception $e){
                return response()->json(['success' => 'system', 'response' => $response]);
            }

            if($news) {
                return response()->json(['success' => 'success', 'response' => $response]);
            } else {
                return response()->json(['success' => 'error', 'response' => $response]);
            }
        }
    }


    private function returnhttpPost($url,$params) { 
        $ch = curl_init( $url );
        # Setup request to send json via POST.
        $payload = json_encode( $params );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , 'Authorization:Bearer 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'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.
        return $result;
    }
    private function gemini($params){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=AIzaSyBaELhqZcBVk6fS0ppkrVnywrTK0ITBX1o',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        return json_decode($response);
    }
}
