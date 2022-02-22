<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$now = time();
$user_name = '';
$password = '';
$message = [];
$err_msg = [];

//クッキーにユーザーとしてのログイン情報が保存されていれば購入画面、管理者としてのログイン情報が保存されていれば管理者画面に移る
if (isset($_COOKIE['user_name']) === TRUE) {
   if ($_COOKIE['user_name'] === 'admin') {
      header("location: admin.php");
   } else {
      header("location: top.php");
   }
}

$request_method = get_request_method();
if ($request_method === 'POST') {
   $user_name = get_post_data('user_name');
   $password = get_post_data('password');
   //var_dump($user_name);
   //var_dump($password);
   if (check_postdata($user_name) !== TRUE) {
      $err_msg[] = 'ユーザー名を入力してください。';
   }

   if (check_postdata($password) !== TRUE) {
      $err_msg[] = 'パスワードを入力してください。';
   }

   //エラーがなければ、ログインし、ログイン情報がクッキーに保存される
   if (count($err_msg) === 0) {
      //ユーザーとしてのログイン処理
      if (check_login($user_name, $password) === 0) {
         setcookie('user_name', $user_name, $now + 60 * 60 * 24 * 365);
         header("location: admin.php");
      //管理者としてのログイン処理
      } else if (check_login($user_name, $password) === 1) {
         setcookie('user_name', $user_name, $now + 60 * 60 * 24 * 365);
         header("location: top.php");
      } else {
         $err_msg[] = 'ユーザー名あるいはパスワードが違います';
      }
   }
}

include_once 'view/login_view.php';
