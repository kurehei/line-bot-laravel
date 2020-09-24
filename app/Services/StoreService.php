<?php
// サービスクラス
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Log;


class StoreService {

  private function filterName($name) {
   $name = str_replace('名前: ','',$name);
   return $name;
  }

  // DBに名前を保存する
  public function store($name) {
    $user = new User;
    $name = $this->filterName($name);
    $user->name = $name;
    // 保存に成功したかどうかの判定
    if(!$user->save()) {
      return "保存に失敗しました";
    }
    $user = DB::table('users')->orderBy('id', 'desc')->first();
    return $user->name."だな宜しくな！！";
  }

  // リクエストの値とDBを照会する。
  public function search($name) {
    $user = DB::table('users')->where('name', 'like', "%{$name}%")->first();

    // 照会したユーザーがnullかどうか判定
    return is_null($user) ? "お前の名前知らないなあ、名前教えてくれよ！！" : $user->name."じゃん、久しぶりだなあ";
  }
}
?>
