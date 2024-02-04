<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TitleController extends Controller
{
    // Burda Eğer kullanıcı giriş yapmışsa desifre veritabınından kendi datasına ait verileri görecek şekilde desifre sayfasına yolluyoruz
    public function TitleShow(){
        if(Auth::check()){
            $news = Title::where('user_id', Auth::user()->id)
            ->get();
            return view('pages.title', compact('news'));
          }else{
            return view('index');
          }
    }
    // Burda formdan gelen veriyi kontrolden geçirip api bağlantısı ile Geminiye gönderip cevap alıp bunu geri kullanıcı ekranına gönderiyoruz
    public function CreateNews(Request $req){
        $title_draft = $req->title_draft;
        $uniq_words = $req->uniq_words;

        
        if(empty($title_draft)){
            return response()->json(['success' => 'emptyTitle']);
        }else if(empty($uniq_words)){
            return response()->json(['success' =>'uniqWords']);
        }else{
            $news = Title::create([
                'user_id' => Auth::user()->id,
                'title_draft' => $title_draft,
                'uniq_words' => $uniq_words,
            ]);

            $news_id = DB::getPdo()->lastInsertId();
            try{
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "İçinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği $title_draft verilen bu haberi Anadolu Ajansı kuralları ve standartları çerçevesinde 3 tane haber başlığı öner. Her metnin sonuna /++ sembolünü koy"
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
                    Title::where('id', $news_id)->update([
                        'title' => $spotContentString
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
        $data = Title::where('id', $req->dataID)->first();
        return response()->json(['success' => true, 'data' => $data]);
    }
    // Burda kullanıcıya ait geçmiş dataları listeliyoruz. Görüntü kirliliğini önlemek için 50 karakterden fazla ise sonunu 50 den itibaren kesip sonuna'...' ekliyoruz
    public function historyNews() {
        $news = Title::where('user_id', Auth::user()->id)
        ->where('title', '!=', '')
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

                            if($item->title){
                                if(strlen($item->title) > 50) {
                                    $dataHtml .= mb_convert_encoding( substr($item->title, 0, 50) . '...' ,  "UTF-8" , "UTF-8");
                                } else {
                                    $dataHtml .= $item->title;
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
        $title_draft_return = $req->title_draft_return;
        $uniq_words_return = $req->uniq_words_return;
        $titleID = $req->titleID;
        if(empty($title_draft_return)){
            return response()->json(['success' => 'emptyTitle']);
        }else if(empty($uniq_words_return)){
            return response()->json(['success' =>'uniqWords']);
        }else{
            $news = Title::create([
                'user_id' => Auth::user()->id,
                'title_draft' => $title_draft_return,
                'uniq_words' => $uniq_words_return,
            ]);
            $news_id = DB::getPdo()->lastInsertId();
            try{
                $data = [
                    "contents" => [
                        [   
                            "parts" => [
                                [
                                    "text" => "İçinde kesinlikle $uniq_words_return kelimelerinin her birinin geçtiği $title_draft_return verilen bu haberi Anadolu Ajansı kuralları ve standartları çerçevesinde yeni 3 tane haber başlığı öner. Her metnin sonuna /++ sembolünü koy"
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
                    Title::where('id', $news_id)->update([
                        'title' => $spotContentString
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
