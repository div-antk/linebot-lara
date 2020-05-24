<?php

namespace App\Http\Controllers;

use App\Services\Gurunavi;
use App\Services\RestaurantBubbleBuilder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;


class LineBotController extends Controller
{
    public function index()
    {
        return view('linebot.index');
    }

    public function restaurants(Request $request)
    {
        Log::debug($request->header());
        Log::debug($request->input());

        // 環境変数を利用してインスタンスを作成
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    
        // 署名の検証
        $signature = $request->header('x-line-signature');
        if (!$lineBot->validateSignature($request->getContent(), $signature)) {
            abort(400, 'Invalid signature');
        }

        // リクエストからイベントを取り出す
        // テキスト、画像、スタンプといったメッセージの種類に応じたクラスのインスタンスを返す
        $events = $lineBot->parseEventRequest($request->getContent(), $signature);
        
        Log::debug($events);

        foreach ($events as $event) {
            if (!($event instanceof TextMessage)) {
                Log::debug('Non text message has come');
                continue;
            }

            $gurunavi = new Gurunavi();
            $gurunaviResponse = $gurunavi->searchRestaurants($event->getText());

            // エラーだった場合の処理
            if (array_key_exists('error', $gurunaviResponse)) {
                $replyText = $gurunaviResponse['error'][0]['message'];
                $replyToken = $event->getReplyToken();
                $lineBot->replyText($replyToken, $replyText);
                continue;
            }

            // $replyText = '';
            // foreach($gurunaviResponse['rest'] as $restaurant) {
            //     $replyText .=
            //         $restaurant['name'] . "\n" .
            //         $restaurant['url'] . "\n" .
            //         "\n";
            // }

            // $replyToken = $event->getReplyToken();
            // $lineBot->replyText($replyToken, $replyText);

            $bubble = [];
            foreach ($gurunaviResponse['rest'] as $restaurant) {
                $bubble = RestaurantBubbleBuilder::builder();
                $bubble->setContents($restaurant);
                $bubbles[] = $bubble;
            }

            $carousel = CarouselContainerBuilder::builder();
            $carousel->setContents($bubbles);

            $flex = FlexMessageBuilder::builder();
            $flex->setAltText('飲食店検索結果');
            $flex->setContents($carousel);

            // Flex Messageのようにテキスト以外のタイプの返信を行う
            $lineBot->replyMessage($event->getReplyToken(), $flex);
        }
    }
}
