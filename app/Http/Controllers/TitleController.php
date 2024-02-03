<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TitleController extends Controller
{
    public function TitleShow(){
        if(Auth::check()){
            $news = Title::where('user_id', Auth::user()->id)
            ->get();
            return view('pages.title', compact('news'));
          }else{
            return view('index');
          }
    }
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
                                    "text" => "Bana Anadolu Ajans tarzında $title_draft haber metnine uygun, içinde kesinlikle $uniq_words kelimelerinin her birinin geçtiği başlıklar yaz konusu hackathon olsun her bir metin en az 20 kelime olsun ve en az 3 farklı başlık metni olsun. Her metnin sonuna /++ sembolünü koy"
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

        public function seeMessage(Request $req){
            $data = Title::where('id', $req->dataID)->first();
            return response()->json(['success' => true, 'data' => $data]);
        }
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
                                        "text" => "Bana Anadolu Ajans tarzında $title_draft_return haber metnine uygun, içinde kesinlikle $uniq_words_return kelimelerinin her birinin geçtiği başlıklar yaz konusu hackathon olsun her bir metin en az 20 kelime olsun ve en az 3 farklı başlık metni olsun. Her metnin sonuna /++ sembolünü koy"
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
