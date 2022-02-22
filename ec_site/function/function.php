<?php
//数字文字が入力されているかのチェック
function check_postdata($data)
{
    if ($data !== '') {
        return TRUE;
    } else {
        return FALSE;
    }
}

//半角数字チェック
function check_number($number)
{
    if (preg_match('/^[0-9]+$/', $number) === 1) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//ユーザー名が半角英数字かつ6文字以上かチェック
function check_user($number)
{
    if (preg_match('/^[0-9a-zA-Z]+$/', $number) === 1 && mb_strlen($number) >= 6) {
        return TRUE;
    } else if (mb_strlen($number) < 6) {
        return 1;
    } else {
        return 0;
    }
}

//入力されたユーザー名がデータベースにすでに登録されていないかチェック
function check_same_user($user_name)
{
    // DB接続
    $link = get_db_connect();
    $user_data = get_user_name($link, $user_name);
    if ($user_data !== []) {
        return FALSE;
    } else {
        return TRUE;
    }
}

//入力されたユーザー名とパスワードがデータベースのユーザー情報と一致しているかチェック
function check_login($user_name, $password)
{
    $user_id = '';
    $link = get_db_connect();
    $user_data = get_user_id($link, $user_name, $password);
    foreach ($user_data as $value) {
        $user_id = $value['id'];
    }
    if ($user_id === '7') {
        return 0;
    } else if ($user_data !== []) {
        return 1;
    } else {
        return FALSE;
    }
}

function transaction($link, $goods_name, $price, $stock, $status, $goods_image, $key)
{
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
    mysqli_autocommit($link, false);
    if (insert_goods_table($link, $goods_name, $price, $date, $status, $goods_image) === TRUE) {
        if (insert_goods_stock_table($link, $stock, $date) === TRUE) {
            if (upload_file($key) === TRUE) {
                //処理確定
                return mysqli_commit($link);
            } else {
                mysqli_rollback($link);
                return 2;
            }
        } else {
            mysqli_rollback($link);
            return 0;
        }
    } else {
        mysqli_rollback($link);
        return 1;
    }
}

function transaction_result($link, $user_id, $cart_data)
{
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
    mysqli_autocommit($link, false);
    if (delete_cart_all($link, $user_id) === TRUE) {
        //goods_stock_tableから在庫数を取得する
        foreach ($cart_data as $value) {
            $goods_id = $value['id'];
            $amount = $value['amount'];
            $stock_data = get_goods_stock($link, $goods_id);
            foreach ($stock_data as $value) {
                $stock = $value['stock'];
            }
            if ($stock >= $amount) {
                $stock = $stock - $amount;
                //var_dump(update_goods_stock_table($goods_id, $amount));
                if (update_goods_stock_table($goods_id, $stock) === FALSE) {
                    mysqli_rollback($link);
                    return 2;
                    break;
                }
            } else {
                mysqli_rollback($link);
                return 1;
            }
        }
        return mysqli_commit($link);
    } else {
        mysqli_rollback($link);
        return 0;
    }
}

function insert_goods_table($link, $goods_name, $price, $date, $status, $goods_image)
{
    // 挿入情報をまとめる
    $data = [
        'name' => $goods_name,
        'price' => $price,
        'created_date' => $date,
        'updated_date' => $date,
        'status' => $status,
        'img' => $goods_image
    ];
    // insertのSQL
    $sql = 'INSERT INTO goods_information_table (name, price, created_date, updated_date, status, img) VALUES(\'' . implode('\',\'', $data) . '\')';
    // クエリ実行
    return insert_db($link, $sql);
}

function insert_goods_stock_table($link, $stock, $date)
{
    // A_Iを取得
    $goods_id = mysqli_insert_id($link);
    /**
     * 発注詳細情報を挿入
     */
    // 挿入情報をまとめる
    $data = [
        'goods_id' => $goods_id,
        'stock' => $stock,
        'created_date' => $date,
        'updated_date' => $date
    ];
    // insertのSQL
    $sql = 'INSERT INTO goods_stock_table(goods_id, stock, created_date, updated_date) VALUES(\'' . implode('\',\'', $data) . '\')';
    // クエリ実行
    return insert_db($link, $sql);
}

function register_user($user_name, $password)
{
    // DB接続
    $link = get_db_connect();
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    $data = [
        'user_name' => $user_name,
        'password' => $password,
        'created_date' => $date,
        'updated_date' => $date,
    ];
    // insertのSQL
    $sql = 'INSERT INTO user2_table (user_name, password, created_date, updated_date) VALUES(\'' . implode('\',\'', $data) . '\')';
    // クエリ実行
    return insert_db($link, $sql);
}

function insert_cart($link, $user_id, $goods_id)
{
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    $data = get_cart_amount($link, $user_id, $goods_id);
    if ($data === []) {
        $data = [
            'user_id' => $user_id,
            'item_id' => $goods_id,
            'amount' => 1,
            'created_date' => $date,
            'updated_date' => $date,
        ];
        // insertのSQL
        $sql = 'INSERT INTO cart_table (user_id, item_id, amount, created_date, updated_date) VALUES(\'' . implode('\',\'', $data) . '\')';
        // クエリ実行
        return insert_db($link, $sql);
    } else {
        foreach ($data as $value) {
            $amount = $value['amount'];
        }
        $amount = $amount + 1;
        $sql = 'UPDATE cart_table SET amount = ' . $amount . ', updated_date = "' . $date . '" WHERE user_id = ' . $user_id . ' AND item_id =' . $goods_id . '';
        if (mysqli_query($link, $sql) === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

function insert_db($link, $sql)
{
    // クエリを実行する
    if (mysqli_query($link, $sql) === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function upload_file($key)
{
    $upload = UPLOAD_IMAGE . basename($_FILES[$key]['name']);
    if (move_uploaded_file($_FILES[$key]['tmp_name'], $upload) === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function update_goods_stock_table($goods_id, $stock)
{
    // DB接続
    $link = get_db_connect();
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    $sql = 'UPDATE goods_stock_table SET stock = ' . $stock . ', updated_date = "' . $date . '" WHERE goods_id =' . $goods_id . '';
    if (mysqli_query($link, $sql) === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function change_status($goods_id, $status)
{
    // DB接続
    $link = get_db_connect();
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    if ($status === '0') {
        $sql = 'UPDATE goods_information_table SET status = 1, updated_date = "' . $date . '" WHERE id =' . $goods_id . '';
        if (mysqli_query($link, $sql) === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    } else if ($status === '1') {
        $sql = 'UPDATE goods_information_table SET status = 0, updated_date = "' . $date . '" WHERE id = ' . $goods_id . '';
        //var_dump($sql);
        if (mysqli_query($link, $sql) === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    } else {
        return 0;
    }
}

function update_cart_amount($link, $goods_id, $amount)
{
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    $sql = 'UPDATE cart_table SET amount = ' . $amount . ', updated_date = "' . $date . '" WHERE item_id =' . $goods_id . '';
    if (mysqli_query($link, $sql) === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function transaction_delete($goods_id)
{
    // DB接続
    $link = get_db_connect();
    // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
    mysqli_autocommit($link, false);
    if (delete_goods_table($link, $goods_id) === TRUE) {
        if (delete_goods_stock_table($link, $goods_id) === TRUE) {
            //処理確定
            return mysqli_commit($link);
        } else {
            mysqli_rollback($link);
            return FALSE;
        }
    } else {
        mysqli_rollback($link);
        return 0;
    }
}

function delete_goods_table($link, $goods_id)
{
    $sql = 'DELETE FROM goods_information_table WHERE id = ' . $goods_id . '';
    // クエリ実行
    return insert_db($link, $sql);
}

function delete_goods_stock_table($link, $goods_id)
{
    $sql = 'DELETE FROM goods_stock_table WHERE id = ' . $goods_id . '';
    // クエリ実行
    return insert_db($link, $sql);
}

function delete_cart_table($link, $goods_id)
{
    $sql = 'DELETE FROM cart_table WHERE item_id = ' . $goods_id . '';
    // クエリ実行
    return insert_db($link, $sql);
}

function delete_cart_all($link, $user_id)
{
    $sql = 'DELETE FROM cart_table WHERE user_id = ' . $user_id . '';
    // クエリ実行
    return insert_db($link, $sql);
}

function get_goods_table_list($link)
{
    // SQL生成
    $sql = 'SELECT git.img, git.name, git.price, gst.stock, git.status, git.id FROM goods_information_table AS git join goods_stock_table AS gst ON git.id = gst.goods_id';
    // クエリ実行
    return get_as_array($link, $sql);
}

function get_user_name($link, $user_name)
{
    $sql = 'SELECT user_name FROM user2_table WHERE user_name = "' . $user_name . '"';
    return get_as_array($link, $sql);
}

function get_user_id($link, $user_name, $password)
{
    $sql = 'SELECT id FROM user2_table WHERE user_name = "' . $user_name . '" AND password = "' . $password . '"';
    return get_as_array($link, $sql);
}

function get_user_id2($link, $user_name)
{
    $sql = 'SELECT id FROM user2_table WHERE user_name = "' . $user_name . '"';
    return get_as_array($link, $sql);
}

function get_user_information($link)
{
    $sql = 'SELECT user_name, created_date FROM user2_table ';
    return get_as_array($link, $sql);
}

function get_cart_amount($link, $user_id, $goods_id)
{
    $sql = 'SELECT amount FROM cart_table WHERE user_id = ' . $user_id . ' AND item_id = ' . $goods_id . '';
    return get_as_array($link, $sql);
}

function get_cart_data($link, $user_id)
{
    $sql = 'SELECT ct.amount, git.id, git.img, git.name, git.price FROM cart_table AS ct join goods_information_table AS git ON ct.item_id = git.id WHERE user_id = ' . $user_id . '';
    return get_as_array($link, $sql);
}

function get_goods_stock($link, $goods_id)
{
    $sql = 'SELECT stock FROM goods_stock_table WHERE goods_id = ' . $goods_id . '';
    return get_as_array($link, $sql);
}

function get_as_array($link, $sql)
{
    // 返却用配列
    $data = [];
    // クエリを実行する
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            // １件ずつ取り出す
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        // 結果セットを開放
        mysqli_free_result($result);
    }
    return $data;
}

/**
 * DBハンドルを取得
 * @return obj $link DBハンドル
 */
function get_db_connect()
{

    // コネクション取得
    if (!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)) {
        die('error: ' . mysqli_connect_error());
    }

    // 文字コードセット
    mysqli_set_charset($link, DB_CHARACTER_SET);

    return $link;
}

/**
 * DBとのコネクション切断
 * @param obj $link DBハンドル
 */
function close_db_connect($link)
{
    // 接続を閉じる
    mysqli_close($link);
}

/**
 * リクエストメソッドを取得
 * @return str GET/POST/PUTなど
 */
function get_request_method()
{
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * POSTデータを取得
 * @param str $key 配列キー
 * @return str POST値
 */
function get_post_data($key)
{
    $str = '';
    if (isset($_POST[$key]) === TRUE) {
        $str = $_POST[$key];
    }
    return $str;
}

function get_files_data($key)
{
    $str = '';
    if (isset($_FILES[$key]) === TRUE) {
        $str = $_FILES[$key]['name'];
    }
    return $str;
}
