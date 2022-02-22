<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$user_name = '';
$user_id = '';
$goods_id = '';
$sum_price1 = 0;
$sum_price2 = 0;
$amount = '';
$message = [];
$err_msg = [];
$user_data = [];
$cart_data = [];

//クッキーにログイン情報が保存されていなければ、ログイン画面に移り、保存されていれば、ヘッダーにユーザー名が表示される
if (isset($_COOKIE['user_name']) === false) {
    header("location: login.php");
} else {
    $user_name = $_COOKIE['user_name'];
}

//カートのデータの表示
// DB接続
$link = get_db_connect();
//ユーザーIDを取得
$user_data = get_user_id2($link, $user_name);
foreach ($user_data as $value) {
    $user_id = $value['id'];
}
// カート内の量、商品ID、イメージ画像、商品名、値段を取得
$cart_data = get_cart_data($link, $user_id);

if ($cart_data === []) {
    $err_msg[] = '商品がありません';
}

$request_method = get_request_method();
if ($request_method === 'POST') {
    if (transaction_result($link, $user_id, $cart_data) === TRUE) {
        $message[] = '';
    } else if (transaction_result($link, $user_id, $cart_data) === 0) {
        $err_msg[] = '削除失敗';
    } else if (transaction_result($link, $user_id, $cart_data) === 1) {
        $err_msg[] = '在庫が足りません';
    } else {
        $err_msg[] = 'アップデート失敗';
        //var_dump(transaction_result($link, $user_id, $cart_data));
    }
}

// DB切断
close_db_connect($link);

include_once 'view/finish_view.php';
