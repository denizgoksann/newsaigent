<?php

namespace App\Http\Controllers;

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
        return view('pages.news', compact('news'));
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
            try{
                $data = [
                    "model" => "gpt-3.5-turbo-1106",
                    "response_format"=> [ "type"=> "json_object" ],
                    "messages"=> [
                        [
                        "role"=> "system",
                        "content"=> "You are a helpful assistant designed to output JSON."
                        ],
                        [
                        "role"=> "user",
                        "content"=> mb_convert_encoding($news_title , "UTF-8" , "UTF-8") . " konusunda içerisinde kesinlikle ".  mb_convert_encoding($uniq_words  , "UTF-8" , "UTF-8") . " kelimelerinin geçtiği ".strip_tags( $news_text). ' şeklinde bir en az 500 kelimeden oluşan Anadolu Ajans tarzında haber metni yaz.'
                        ]
                    ]
                ];
                $response = self::httpPost('https://api.openai.com/v1/chat/completions', $data);
                $response = json_decode($response);
                $chatgpt_message = '';
                if($response){
                    if(is_array($response->choices) && count($response->choices) > 0){
                        $chatgpt_message = json_decode($response->choices[0]->message->content);
                    }
                }

                $chatgpt_message_str = '';
                foreach ($chatgpt_message as $key => $value) {
                    $chatgpt_message_str .= $value. ',';
                }

                $chatgpt_message_str = rtrim($chatgpt_message_str, ',');

                News::where('id', $news_id)->update([
                    'news' => $chatgpt_message_str
                ]);

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
        $news = $req->news_last;
        $newsID = $req->news_id;
        
        $update= News::where('id', $newsID)
        ->update(['news' => $news]); 

        if($update){
            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['success' => 'error']);

        }
    }
}
