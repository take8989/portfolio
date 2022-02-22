<?php
//ログアウト処理(クッキーに保存されているログイン情報を削除する)
if (isset($_COOKIE['user_name']) === TRUE) {
   setcookie('user_name', '', time() - 3600);
}
//ログイン画面に戻る
header("location: login.php");