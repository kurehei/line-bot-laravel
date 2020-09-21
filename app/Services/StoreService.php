<?php
// サービスクラス
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\User;

class StoreService {
  // DBを名前に保存する
  public function store($name) {
    $user = new User;
    $user->name = $name;
    // 保存に成功したかどうかの判定
    return $user->save()? $user: "保存に失敗しました";
  }

  // リクエストの値とDBを照会する。
  public function search($name) {
    $user = DB::table('users')->where('name', 'like', "%{$name}%")->first();

    //$user = $name == "むらかみ" ? $name : null;
    // 照会したユーザーがnullかどうか判定
    $reply = is_null($user) ? "お前の名前知らないなあ、名前教えてくれよ！！" : $user."じゃん、久しぶりだなあ";
    return $reply;
  }
}
?>
