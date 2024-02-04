<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Desifre;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DesifreController extends Controller
{
    // Burda Eğer kullanıcı giriş yapmışsa desifre veritabınından kendi datasına ait verileri görecek şekilde desifre sayfasına yolluyoruz

    public function DesifreShow(){
        if(Auth::check()){
            $news = Desifre::where('user_id', Auth::user()->id)
            ->get();
           
            return view('pages.desifre', compact('news'));
          }else{
            return view('index');
          }
    }
    // Burda formdan gelen veriyi kontrolden geçirip api bağlantısı ile Geminiye gönderip cevap alıp bunu geri kullanıcı ekranına gönderiyoruz
    public function CreateNews(Request $req){
        $desifre_draft = $req->desifre_draft;

        
        if(empty($desifre_draft)){
            return response()->json(['success' => 'emptyTitle']);
        }else{
            $news = Desifre::create([
                'user_id' => Auth::user()->id,
                'desifre_draft' => $desifre_draft,
            ]);

            $news_id = DB::getPdo()->lastInsertId();
            try{
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" =>"$desifre_draft\"Verilen bu kapalı senaryoyu  Anadolu Ajansı formatında, kesinlikle hiçbir kelime çıkartılmamış ve hiçbir duygudan bahsetmeyen uzun bir deşifre haberi oluştur."
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
                    Desifre::where('id', $news_id)->update([
                        'desifre' => $spotContentString
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
        $data = Desifre::where('id', $req->dataID)->first();
        return response()->json(['success' => true, 'data' => $data]);
    }
    // Burda kullanıcıya ait geçmiş dataları listeliyoruz. Görüntü kirliliğini önlemek için 50 karakterden fazla ise sonunu 50 den itibaren kesip sonuna'...' ekliyoruz
    public function historyNews() {
        $news = Desifre::where('user_id', Auth::user()->id)
        ->where('desifre', '!=', '')
        ->orderBy('created_at', 'desc')
        ->get();
            $dataHtml = "";
            foreach ($news as $item) {
                $dataHtml .= '
                    <div class="d-flex flex-column see_message p-2 mb-2" data-id="'.$item->id.'">
                        <div class="news_history_content">
                            <div class="d-flex justify-content-between align-items-center w-100 p-1 ">
                                <span class="news_time">'.$item->created_at.'</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center w-100 p-1">
                                <span class="news_content_text">';

                            if($item->desifre){
                                if(strlen($item->desifre) > 50) {
                                    $dataHtml .= mb_convert_encoding( substr($item->desifre, 0, 30) . '...' ,  "UTF-8" , "UTF-8");
                                } else {
                                    $dataHtml .= $item->desifre;
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
    public function lastNew(Request $req){
        if(isset($_POST)){
        $desifre_draft_return = $req->desifre_draft_return;
        if(empty($desifre_draft_return)){
            return response()->json(['success' => 'emptyTitle']);
        }else{
            $news = Desifre::create([
                'user_id' => Auth::user()->id,
                'desifre_draft' => $desifre_draft_return,
            ]);
            $news_id = DB::getPdo()->lastInsertId();
            try {
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" =>"$desifre_draft_return\"Verilen bu kapalı senaryoyu  Anadolu Ajansı formatında, kesinlikle hiçbir kelime çıkartılmamış ve hiçbir duygudan bahsetmeyen uzun yeni bir deşifre haberi oluştur."
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
                    Desifre::where('id', $news_id)->update([
                        'desifre' => $spotContentString
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
    }
    // Haberi en son canlıya alıyoruz bu kısımda da 
    public function lastNewLive(Request $req){
        $news=$req->desifre_reply;
        
        $update = Desifre::create([
            'user_id' => Auth::user()->id,
            'desifre' => $news,
            'desifre_title' =>   "DEŞİFRE HABER",
            'active' => '1',

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
