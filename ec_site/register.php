<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$user_name = '';
$password = '';
$message = [];
$err_msg = [];


// リクエストメソッド取得
$request_method = get_request_method();
if ($request_method === 'POST') {
    $user_name = get_post_data('user_name');
    $password = get_post_data('password');
    
    if(check_user($user_name) === 0) {
        $err_msg[] = 'ユーザ名は半角英数字を入力してください';
    } else if (check_user($user_name) === 1) {
        $err_msg[] = 'ユーザー名は6文字以上の文字を入力してください';
    }
   
   
    if (check_same_user($user_name) !== TRUE) {
        $err_msg[] = '同じユーザー名が既に登録されています';
    }
    //var_dump(check_same_user($user_name)); 
    
    if(check_user($password) === 0) {
        $err_msg[] = 'パスワードは半角英数字を入力してください';
    } else if (check_user($password) === 1) {
        $err_msg[] = 'パスワードは6文字以上の文字を入力してください';
    }
    

    if(count($err_msg) === 0) {
        if(register_user($user_name,$password) === TRUE) {
            $message[] = '登録完了';
        } else {
            $err_msg[] = 'user2_table: insertエラー';
        }
        
    } 
}

include_once 'view/register_view.php';
