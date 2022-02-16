<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$user_data = [];

if (isset($_COOKIE['user_name']) === false) {
   header("location: login.php");
}

//商品のデータの表示
// DB接続
$link = get_db_connect();
// 商品の一覧を取得
$user_data = get_user_information($link);
// DB切断
close_db_connect($link);

include_once 'view/admin_user_view.php';
