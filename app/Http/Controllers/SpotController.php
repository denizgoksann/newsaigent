<?php

namespace App\Http\Controllers;

use App\Models\Spot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpotController extends Controller
{
    public function SpotShow(){
        if(Auth::check()){

            $news = Spot::where('user_id', Auth::user()->id)
            ->get();
            return view('pages.spot', compact('news'));
          }else{
            return view('index');
          }
    }
    public function CreateNews(Request $req){
        $spot_draft = $req->spot_draft;
        $uniq_words = $req->uniq_words;

        
        if(empty($spot_draft)){
            return response()->json(['success' => 'emptyTitle']);
        }else if(empty($uniq_words)){
            return response()->json(['success' =>'uniqWords']);
        }else{
            $news = Spot::create([
                'user_id' => Auth::user()->id,
                'spot_draft' => $spot_draft,
                'uniq_words' => $uniq_words,
            ]);

            $news_id = DB::getPdo()->lastInsertId();
            try{
                $data = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "Bana Anadolu Ajans tarzında $spot_draft haber metnine uygun, içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği spotlar yaz konusu hackathon olsun her bir metin en az 20 kelime olsun ve en az 3 farklı spot metni olsun. Her metnin sonuna /++ sembolünü koy"
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
                    Spot::where('id', $news_id)->update([
                        'spot' => $spotContentString
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
            $data = Spot::where('id', $req->dataID)->first();
            return response()->json(['success' => true, 'data' => $data]);
        }
        public function historyNews() {
            $news = Spot::where('user_id', Auth::user()->id)
            ->where('spot', '!=', '')
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

                                if($item->spot){
                                    if(strlen($item->spot) > 50) {
                                        $dataHtml .= mb_convert_encoding( substr($item->spot, 0, 30) . '...' ,  "UTF-8" , "UTF-8");
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

        public function lastNew(Request $req){
           if(isset($_POST)){
            $spot_draft_return = $req->spot_draft_return;
            $uniq_words_return = $req->uniq_words_return;
            $spotID = $req->spotID;
            if(empty($spot_draft_return)){
                return response()->json(['success' => 'emptyTitle']);
            }else if(empty($uniq_words_return)){
                return response()->json(['success' =>'uniqWords']);
            }else{
                $news = Spot::create([
                    'user_id' => Auth::user()->id,
                    'spot_draft' => $spot_draft_return,
                    'uniq_words' => $uniq_words_return,
                ]);
                $news_id = DB::getPdo()->lastInsertId();
                try {
                    $data = [
                        "contents" => [
                            [
                                "parts" => [
                                    [
                                        "text" => "Bana Anadolu Ajans tarzında $spot_draft_return haber metnine uygun, içinde kesinlikle $uniq_words_return kelimelerinin her birinin geçtiği spotlar yaz konusu hackathon olsun her bir metin en az 20 kelime olsun ve en az 3 farklı spot metni olsun. Her metnin sonuna /++ sembolünü koy"
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
                        Spot::where('id', $news_id)->update([
                            'spot' => $spotContentString
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

        private function gemini($params){
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
                CURLOPT_URL => '',
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
