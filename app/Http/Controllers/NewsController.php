<?php

namespace App\Http\Controllers;

use App\Models\Bultein;
use App\Models\Category;
use App\Models\News;
use App\Models\NewStyle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    // Burda Eğer kullanıcı giriş yapmışsa desifre veritabınından kendi datasına ait verileri görecek şekilde desifre sayfasına yolluyoruz
    public function NewsShow(){
      if(Auth::check()){
        $news = News::where('user_id', Auth::user()->id)
        ->get();
        $category = Category::all();
        $bulten = Bultein::all();
        $style = NewStyle::where('durum', 1)->get();
        return view('pages.news', compact('news', 'category', 'bulten', 'style'));
      }else{
        return view('index');
      }
    }
    // Burda formdan gelen veriyi kontrolden geçirip api bağlantısı ile Geminiye gönderip cevap alıp bunu geri kullanıcı ekranına gönderiyoruz
    public function CreateNews(Request $req){
        $news_title = $req->news_title;
        $news_text = $req->news_text;
        $uniq_words = $req->uniq_words;
        $spot = $req->spot;
        $editor = $req->editor;
        $location = $req->location;
        $category_id = $req->category_id;
        $bultein_id = $req->bultein_id;
        $new_style_id = $req->new_style_id;

        
        if(empty($news_text)){
            return response()->json(['success' => 'emptyText']);
        }else if(empty($uniq_words)){
            return response()->json(['success' =>'uniqWords']);
        }else{
            $news = News::create([
                'user_id' => Auth::user()->id,
                'news_draft' => $news_text,
                'news_title' => $news_title,
                'uniq_words' => $uniq_words,
                'spot' => $spot,
                'editor' => $editor,
                'location' => $location,
                'category_id' => $category_id,
                'bultein_id' => $bultein_id,
                'new_style_id' => $new_style_id,
            ]);

            $news_id = DB::getPdo()->lastInsertId();
            try{
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği $news_text Verilen bu metinden Anadolu Ajansı formatında, metnin önemli kısmı başlıktan hemen sonra gelecek şekilde, hiçbir duygudan bahsetmeyen 3 haber oluştur. haberin en başında noktalama işareti kullanma ve her metnin sonuna /++ sembolü koy"
                                ]
                            ]
                        ]
                                ],
                    "safetySettings" => [
                        [
                            "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                            "threshold" => "BLOCK_NONE"
                        ],
                        [
                            "category" => "HARM_CATEGORY_HATE_SPEECH",
                            "threshold" => "BLOCK_NONE"
                        ],
                        [
                            "category" => "HARM_CATEGORY_HARASSMENT",
                            "threshold" => "BLOCK_NONE"
                        ],
                        [
                            "category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                            "threshold" => "BLOCK_NONE"
                        ],
        ],

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
    // Burda tıklanan dataya erişim sağlatıyoruz
    public function seeMessage(Request $req){
        $data = News::leftJoin('categories', 'news.category_id', '=', 'categories.id')
        ->leftJoin('bulteins', 'news.bultein_id', '=', 'bulteins.id')
        ->leftJoin('new_styles', 'news.new_style_id', '=', 'new_styles.id')
        ->where('news.id', $req->dataID)
        ->select('news.*', 'categories.category_name', 'bulteins.bultein_name', 'new_style', 'categories.id')
        ->first();
        return response()->json(['success' => true, 'data' => $data]);
    }
    // Burda kullanıcıya ait geçmiş dataları listeliyoruz. Görüntü kirliliğini önlemek için 50 karakterden fazla ise sonunu 50 den itibaren kesip sonuna'...' ekliyoruz
    public function historyNews() {
        $news = News::where('user_id', Auth::user()->id)
        ->where('news', '!=', '')
        ->orderBy('created_at', 'desc')
        ->get();
            $dataHtml = "";
            foreach ($news as $item) {
                $titleParse = "";
                if(empty($item->news_title))
                {
                    $titleParse = 'Doldurulmadı';
                }else{
                    if(strlen($item->news_title) > 50){
                        $titleParse .= mb_convert_encoding( substr($item->news_title, 0, 50) . '...' ,  "UTF-8" , "UTF-8");
                    }else{
                        $titleParse = $item->news_title;
                    }
                }
             
                $dataHtml .= '
                    <div class="d-flex flex-column see_message p-2 mb-2" data-id="'.$item->id.'">
                        <div class="news_history_content">
                            <div class="d-flex justify-content-between align-items-center w-100 p-1 ">
                                <span class="news_history_content_title">'.$titleParse.'</span>
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
    // Burda oluşturulan metinleri beğenmeyen kullanıcını yeniden oluştur butonuna basması sonucun mevcutta bastığı id 'li datanın kayıtlı verilerini tekrar Gemine ile yeniden oluşturup kullanıcı ekranına basıyoruz
    public function lastNewReturn(Request $req){
        $news_title = $req->news_title;
        $news_text = $req->news_text;
        $uniq_words = $req->uniq_words;
        $spot = $req->spot;
        $editor = $req->editor;
        $location = $req->location;
        $category_id = $req->category_id;
        $new_style_id = $req->new_style_id;
        $bultein_id = $req->bultein_id;
        $newsID = $req->newsID;
        if(empty($uniq_words)){
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
                'editor' => $editor,
                'category_id' => $category_id,
                'new_style_id' => $new_style_id,
                'bultein_id' => $bultein_id,
            ]);
            $news_id = DB::getPdo()->lastInsertId();
                try {
                    $data = [
                        "contents" => [
                            [
                                "parts" => [
                                    [
                                        "text" => "içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği $news_text Verilen bu metinden Anadolu Ajansı formatında, metnin önemli kısmı başlıktan hemen sonra gelecek şekilde, hiçbir duygudan bahsetmeyen 3 yeni haber oluştur. haberin en başında noktalama işareti kullanma ve her metnin sonuna /++ sembolü koy"
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
    // Haberi en son canlıya alıyoruz bu kısımda da 
    public function lastNew(Request $req){
            $news=$req->news_reply;
            $news_title=$req->news_title_reply;
            $bultein_id=$req->bultein_id;
            $new_style_id=$req->new_style_id;
            $category_id=$req->category_id;
            $spot=$req->spot_reply;
            $editor=$req->editor_reply;
            $location=$req->location;

        
            $update = News::create([
                'user_id' => Auth::user()->id,
                'news' => $news,
                'news_title' => $news_title,
                'spot' => $spot,
                'editor' => $editor,
                'category_id' => $category_id,
                'bultein_id' => $bultein_id,
                'new_style_id' => $new_style_id,
                'location' => $location,
                'durum' => "1" ,
            ]);

        if($update){
            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['success' => 'error']);

        }
    }
    // Burası gemini api entegrasyonu
    private function gemini($params){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => '$API_KEY',
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
