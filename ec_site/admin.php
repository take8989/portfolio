<?php
// 設定ファイル読み込み
require_once 'const/const.php';
// 関数ファイル読み込み
require_once 'function/function.php';

$goods_id = '';
$goods_name = '';
$price = '';
$status = '';
$goods_image = '';
$stock = '';
$message = [];
$err_msg = [];
$goods_data = [];

if (isset($_COOKIE['user_name']) === false) {
    header("location: login.php");
}

$request_method = get_request_method();
if ($request_method === 'POST') {
    //商品を追加
    if (isset($_POST['new_name']) === TRUE) {
        $goods_name = get_post_data('new_name');
        $price = get_post_data('new_price');
        $stock = get_post_data('new_stock');
        $status = get_post_data('new_status');
        $goods_image = get_files_data('new_img');

        if (check_postdata($goods_name) !== TRUE) {
            $err_msg[] = '名前を入力してください。';
        }
        if (check_postdata($price) !== TRUE) {
            $err_msg[] = '値段を入力してください。';
        } else if (check_number($price) !== TRUE) {
            $err_msg[] = '値段は半角数字を入力してください。';
        }
        if (check_postdata($stock) !== TRUE) {
            $err_msg[] = '個数を入力してください。';
        } else if (check_number($stock) !== TRUE) {
            $err_msg[] = '個数は半角数字を入力してください。';
        }
        if (check_postdata($goods_image) !== TRUE) {
            $err_msg[] = 'ファイルを選択してください。';
        } else if (preg_match('/\.png$|\.jpg$/', $goods_image) !== 1) {
            $err_msg[] = 'ファイルが正しくありません。';
        }
        if ($status !== '0' && $status !== '1') {
            $err_msg[] = '公開ステータスが正しくありません。';
        }
        if (count($err_msg) === 0) {
            // DB接続
            $link = get_db_connect();
            if (transaction($link, $goods_name, $price, $stock, $status, $goods_image, 'new_img') === TRUE) {
                $message[] = '追加成功';
            } else if (transaction($link, $goods_name, $price, $stock, $status, $goods_image, 'new_img') === 0) {
                $err_msg[] = 'goods_stock_table: insertエラー';
            } else if (transaction($link, $goods_name, $price, $stock, $status, $goods_image, 'new_img') === 1) {
                $err_msg[] = 'goods_information_table: insertエラー';
            } else {
                $err_msg[] = 'アップロード失敗';
            }
        }
    }

    //商品の在庫の変更
    if (isset($_POST['update_stock']) === TRUE) {
        if (check_number($_POST['update_stock']) === TRUE) {
            $goods_id = get_post_data('goods_id');
            $stock = get_post_data('update_stock');
            if (update_goods_stock_table($goods_id, $stock) === TRUE) {
                $message[] = '更新成功';
            } else {
                $err_msg[] = '更新失敗';
            }
        } else {
            $err_msg[] = '個数は半角数字を入力してください';
        }
    }

    //商品の表示、非表示切り替え
    if (isset($_POST['change_status']) === TRUE) {
        $goods_id = get_post_data('goods_id');
        $status = get_post_data('change_status');
        if (change_status($goods_id, $status) === TRUE) {
            $message[] = '更新成功';
        } else {
            $err_msg[] = '更新失敗';
        }
    }

    if (isset($_POST['delete']) === TRUE) {
        $goods_id = get_post_data('goods_id');
        if (transaction_delete($goods_id) === TRUE) {
            $message[] = '削除しました';
        } else {
            $err_msg[] = '削除失敗';
        }
    }
}

//商品のデータの表示
// DB接続
$link = get_db_connect();
// 商品の一覧を取得
$goods_data = get_goods_table_list($link);
// DB切断
close_db_connect($link);

include_once 'view/admin_view.php';
