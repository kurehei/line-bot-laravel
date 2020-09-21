<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use App\Services\StoreService;
use Illuminate\Support\Facades\Log;
use App\User;
use Exception;

class LineWebhookController extends Controller
{

  private $store_service; // サービスクラスのprivate

  // サービスクラスのコンストラクタ
  public function __construct(StoreService $store_service)
  {
    $this->store = $store_service;
  }
  public function webhook(Request $request)
  {
    $lineAccessToken = env('LINE_ACCESS_TOKEN', "");
    $lineChannelSecret = env('LINE_CHANNEL_SECRET', "");

    // 署名のチェック
    $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
    if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
      // TODO 不正アクセス
      Log::error('TEST LOG');
      return;
    }

    $httpClient = new CurlHTTPClient($lineAccessToken);
    $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

    try {
      // イベント取得
      $events = $lineBot->parseEventRequest($request->getContent(), $signature);
      foreach ($events as $event) {
        $replyToken = $event->getReplyToken();
        // リクエストに対して、DBの値と照会して値が同値ならば名前を返す。ない場合、名前を聞く。

        $message = $this->store->search($event->getText());

        if($event->getText() == "教える") {
          $message = "名前: ◯◯って感じで教えてくれ";
        }
        else if(strpos($event->getText(), "名前:")){
          //strpos($xample,'bc') 特定の文字列を含むかどうかのチェック　含まない場合 => false
          $message = $this->store->store($event->getText());
        }
        else {
          $message = $message;
        }

        $checkMessage = is_null($message) ? "nullやないかい": $message;
        $replyMessage = new TextMessageBuilder($checkMessage);

        $lineBot->replyText($replyToken, $replyMessage);
        Log::info(var_export($lineBot->replyMessage($replyToken, $replyMessage), true));
      }
    } catch (Exception $e) {
      // TODO 例外
      return;
    }

    return;
  }
}
