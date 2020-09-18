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
    $user = DB::table('users')->where('name', 'LIKE', $name)->first();
    // 照会したユーザーがnullかどうか判定
    return is_null($user)? "そんなやついねー" : $user;
  }
}
?>
