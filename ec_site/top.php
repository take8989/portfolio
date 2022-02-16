<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$user_id = '';
$goods_id = '';
$user_name = '';
$goods_name = '';
$price = '';
$goods_image = '';
$stock = '';
$user_data = [];
$goods_data = [];
$message = [];
$err_msg = [];


if (isset($_COOKIE['user_name']) === false) {
   header("location: login.php");
} else {
   $user_name = $_COOKIE['user_name'];
}
$request_method = get_request_method();
if ($request_method === 'POST') {
   // DB接続
   $link = get_db_connect();
   $user_data = get_user_id2($link, $user_name);
   foreach ($user_data as $value) {
       $user_id = $value['id'];
   }
   $goods_id = get_post_data('item_id');
   if (insert_cart($link, $user_id, $goods_id) === TRUE) {
      $message[] = 'カートに追加しました';
   } else {
      $err_msg[] = '追加失敗';
   }
} 

//商品のデータの表示
// DB接続
$link = get_db_connect();
// 商品の一覧を取得
$goods_data = get_goods_table_list($link);
// DB切断
close_db_connect($link);
//var_dump($_COOKIE['user_name']);
include_once 'view/top_view.php';
