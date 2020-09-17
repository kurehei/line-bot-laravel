<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Exception;

class LineWebhookController extends Controller
{

  public function webhook(Request $request)
  {
    $lineAccessToken = env('LINE_ACCESS_TOKEN', "");
    $lineChannelSecret = env('LINE_CHANNEL_SECRET', "");

    // 署名のチェック
    $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
    if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
      // TODO 不正アクセス
      return;
    }

    $httpClient = new CurlHTTPClient($lineAccessToken);
    $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

    try {
      // イベント取得
      $events = $lineBot->parseEventRequest($request->getContent(), $signature);

      foreach ($events as $event) {
        // ハローと応答する
        $replyToken = $event->getReplyToken();
        $helloMessage = new TextMessageBuilder("おいら、むらけん宜しくな！お前の名前教えてくれよ！w");
        $lineBot->replyMessage($replyToken, $helloMessage);
      }
    } catch (Exception $e) {
      // TODO 例外
      return;
    }

    return;
  }
}
