<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineBotController extends Controller
{
    public function index()
    {
        return view('linebot.index');
    }

    public function parrot(Request $request)
    {
        Log::debug($request->header());
        Log::debug($request->input());

        // 環境変数を利用してインスタンスを作成
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $linebot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    
        // 署名の検証
        $signature = $request->header('x-line-signature');
        if (!$lineBot->validateSignature($request->getContent(), $signature)) {
            abort(400, 'Invalid signature');
        }

        // リクエストからイベントを取り出す
        // テキスト、画像、スタンプといったメッセージの種類に応じたクラスのインスタンスを返す
        $events = $lineBot->parseEventRequest($request->getContent(), $signature);
        
        Log::debug($events);
    }
}
