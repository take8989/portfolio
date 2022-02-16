<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$user_name = '';
$goods_id = '';
$sum_price1 = 0;
$sum_price2 = 0;
$amount = '';
$message = [];
$err_msg = [];
$user_data = [];
$cart_data = [];

if (isset($_COOKIE['user_name']) === false) {
   header("location: login.php");
} else {
   $user_name = $_COOKIE['user_name'];
}

$request_method = get_request_method();
if ($request_method === 'POST') {
   // DB接続
   $link = get_db_connect();
   if (isset($_POST['delete']) === TRUE) {
     $goods_id = get_post_data('item_id');
     if (delete_cart_table($link, $goods_id) === TRUE) {
         $message[] = '削除しました';
     } else {
         $err_msg[] = '削除失敗';
     }
 }
 
 //カート内の個数を変更
 if (isset($_POST['select_amount']) === TRUE) {
    if(check_number($_POST['select_amount']) === TRUE) {
       $goods_id = get_post_data('item_id');
       $amount = get_post_data('select_amount');
       if (update_cart_amount($link, $goods_id, $amount) === TRUE) {
          $message[] = '更新しました。';
       } else {
          $err_msg[] = '更新失敗';
       }
    } else {
       $err_msg[] = '個数は半角数字を入力してください';
    }
 }
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

// DB切断
close_db_connect($link);


include_once 'view/cart_view.php';