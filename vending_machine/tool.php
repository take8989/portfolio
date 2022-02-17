<?php
$host = '********'; // データベースのホスト名又はIPアドレス
$user = '********';  // MySQLのユーザ名
$passwd   = '********';    // MySQLのパスワード
$dbname   = '********';    // データベース名
$link = mysqli_connect($host, $user, $passwd, $dbname);

$uploaddir = './images/';
$drink_data = [];
$drink_id = '';
if (isset($_POST['drink_id']) === TRUE) {
    $drink_id = $_POST['drink_id'];
}
$drink_name = '';
if (isset($_POST['new_name']) === TRUE) {
    $drink_name = $_POST['new_name'];
}
$price = '';
if (isset($_POST['new_price']) === TRUE) {
    $price = $_POST['new_price'];
}
$public_status = '';
if (isset($_POST['new_status']) === TRUE) {
    $public_status = $_POST['new_status'];
}
$drink_image = '';
if (isset($_FILES['new_img']) === TRUE) {
    $drink_image = $_FILES['new_img']['name'];
}
var_dump($drink_image);
$quantity = '';
if (isset($_POST['new_stock']) === TRUE) {
    $quantity = $_POST['new_stock'];
}
$message = [];
$err_msg = [];
if (isset($_POST['new_name']) === TRUE) {
    if ($drink_name !== '' && preg_match('/^[0-9]+$/', $price) === 1 && preg_match('/^[0-9]+$/', $quantity) === 1 && preg_match('/\.png$|\.jpg$/', $drink_image) === 1 && ($public_status === '0' || $public_status === '1')) {
        mysqli_set_charset($link, 'utf8');
        // 現在時刻を取得
        $date = date('Y-m-d H:i:s');
        // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
        mysqli_autocommit($link, false);
        //ファイル名を取得
        $drink_image = basename($_FILES['new_img']['name']);
        // 挿入情報をまとめる
        $data = [
            'drink_name' => $drink_name,
            'price' => $price,
            'created_date' => $date,
            'update_date' => $date,
            'public_status' => $public_status,
            'drink_image' => $drink_image
        ];
        // insertのSQL
        $sql = 'INSERT INTO drink_table (drink_name, price, created_date, update_date, public_status, drink_image) VALUES(\'' . implode('\',\'', $data) . '\')';
        if (mysqli_query($link, $sql) === TRUE) {


            // A_Iを取得
            $drink_id = mysqli_insert_id($link);
            /**
             * 発注詳細情報を挿入
             */
            // 挿入情報をまとめる
            $data = [
                'drink_id' => $drink_id,
                'quantity' => $quantity,
                'created_date' => $date,
                'update_date' => $date
            ];
            // 注文詳細情報をinsertする
            $sql = 'INSERT INTO drink_inventory_table(drink_id, quantity, created_date, update_date) VALUES(\'' . implode('\',\'', $data) . '\')';
            // insertを実行する
            //ファイルをアップロード
            $upload = $uploaddir . basename($_FILES['new_img']['name']);
            move_uploaded_file($_FILES['new_img']['tmp_name'], $upload);
            $message[] = '追加成功';

            if (mysqli_query($link, $sql) !== TRUE) {
                $err_msg[] = 'drink_inventory_table: insertエラー:' . $sql;
            }
        } else {
            $err_msg[] = 'drink_table: insertエラー:' . $sql;
        }
        // トランザクション成否判定
        if (count($err_msg) === 0) {
            // 処理確定
            mysqli_commit($link);
        } else {
            // 処理取消
            mysqli_rollback($link);
        }
    } else {
        if ($drink_name === '') {
            $err_msg[] = '名前を入力してください。';
        }
        if ($price === '') {
            $err_msg[] = '値段を入力してください。';
        } else if (preg_match('/^[0-9]+$/', $price) !== 1) {
            $err_msg[] = '値段は半角数字を入力してください';
        }
        if ($quantity === '') {
            $err_msg[] = '個数を入力してください。';
        } else if (preg_match('/^[0-9]+$/', $quantity) !== 1) {
            $err_msg[] = '個数は半角数字を入力してください';
        }
        if ($drink_image === '') {
            $err_msg[] = 'ファイルを選択してください';
        } else if (preg_match('/\.png$|\.jpg$/', $drink_image) !== 1) {
            $err_msg[] = 'ファイルが正しくありません';
        }
        if ($public_status !== '0' && $public_status !== '1') {
            $err_msg[] = '公開ステータスが正しくありません';
        }
    }
}

if (isset($_POST['update_stock']) === TRUE) {
    if (preg_match('/^[0-9]+$/', $_POST['update_stock']) === 1) {
        mysqli_set_charset($link, 'utf8');
        $quantity = $_POST['update_stock'];
        // 現在時刻を取得
        $date = date('Y-m-d H:i:s');
        $sql = 'UPDATE drink_inventory_table SET quantity = ' . $quantity . ', update_date = "' . $date . '" WHERE drink_id =' . $drink_id . '';
        if (mysqli_query($link, $sql) === TRUE) {
            $message[] = '更新成功';
        } else {
            $err_msg[] = '更新失敗';
        }
    } else {
        $err_msg[] = '個数は半角数字を入力してください';
    }
}

if (isset($_POST['change_status']) === TRUE) {
    mysqli_set_charset($link, 'utf8');
    // 現在時刻を取得
    $date = date('Y-m-d H:i:s');
    if ($_POST['change_status'] === '0') {
        $sql = 'UPDATE drink_table SET public_status = 1, update_date = "' . $date . '" WHERE drink_id =' . $drink_id . '';
        if (mysqli_query($link, $sql) === TRUE) {
            $message[] = '更新成功';
        } else {
            $err_msg[] = '更新失敗';
        }
    }
    if ($_POST['change_status'] === '1') {
        $sql = 'UPDATE drink_table SET public_status = 0, update_date = "' . $date . '" WHERE drink_id =' . $drink_id . '';
        //var_dump($sql);
        if (mysqli_query($link, $sql) === TRUE) {
            $message[] = '更新成功';
        } else {
            $err_msg[] = '更新失敗';
        }
    }
}

if ($link) {
    // 文字化け防止
    mysqli_set_charset($link, 'utf8');
    $query = 'SELECT dt.drink_image, dt.drink_name, dt.price, dit.quantity, dt.public_status, dt.drink_id FROM drink_table AS dt join drink_inventory_table AS dit ON dt.drink_id = dit.drink_id';
    // クエリを実行します
    $result = mysqli_query($link, $query);
    // 1行ずつ結果を配列で取得します
    while ($row = mysqli_fetch_array($result)) {
        $drink_data[] = $row;
    }
    // 結果セットを開放します
    mysqli_free_result($result);
    // 接続を閉じます
    mysqli_close($link);
    // 接続失敗した場合
} else {
    $err_msg[] = 'DB接続失敗';
}
//var_dump($err_msg); 
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>自動販売機</title>
    <style>
        section {
            margin-bottom: 20px;
            border-top: solid 1px;
        }

        table {
            width: 660px;
            border-collapse: collapse;
        }

        table,
        tr,
        th,
        td {
            border: solid 1px;
            padding: 10px;
            text-align: center;
        }

        caption {
            text-align: left;
        }

        .text_align_right {
            text-align: right;
        }

        .drink_name_width {
            width: 100px;
        }

        .input_text_width {
            width: 60px;
        }

        .status_false {
            background-color: #A9A9A9;
        }
    </style>
</head>

<body>
    <?php
    if ($message !== '') {
        foreach ($message as $value) { ?>
            <p><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php }
    }
    if ($err_msg !== '') {
        foreach ($err_msg as $value) { ?>
            <p><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php }
    } ?>
    <h1>自動販売機管理ツール</h1>
    <section>
        <h2>新規商品追加</h2>
        <form action="tool.php" method="post" enctype="multipart/form-data">
            <div><label>名前: <input type="text" name="new_name" value=""></label></div>
            <div><label>値段: <input type="text" name="new_price" value=""></label></div>
            <div><label>個数: <input type="text" name="new_stock" value=""></label></div>
            <div><input type="file" name="new_img"></div>
            <div>
                <select name="new_status">
                    <option value="0">非公開</option>
                    <option value="1">公開</option>
                </select>
            </div>
            <input type="hidden" name="sql_kind" value="insert">
            <div><input type="submit" value="■□■□■商品追加■□■□■"></div>
        </form>

    </section>
    <section>
        <h2>商品情報変更</h2>
        <table>
            <caption>商品一覧</caption>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>ステータス</th>
            </tr>
            <?php
            foreach ($drink_data as $value) { ?><tr <?php if ($value['public_status'] === '0') {
                                                        print 'class = "status_false"';
                                                    } ?>>
                    <form method="POST">
                        <td><img src="images/<?php print htmlspecialchars($value['drink_image'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <td class="drink_name_width"><?php print htmlspecialchars($value['drink_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text_align_right"><?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); ?>円</td>
                        <td>
                            <input type="text" class="input_text_width text_align_right" name="update_stock" value=<?php print htmlspecialchars($value['quantity'], ENT_QUOTES, 'UTF-8'); ?>>個&nbsp;&nbsp;
                            <input type="submit" value="変更">
                            <input type="hidden" name="drink_id" value=<?php print htmlspecialchars($value['drink_id'], ENT_QUOTES, 'UTF-8'); ?>>
                        </td>
                    </form>
                    <form method="post">
                        <td>
                            <input type="submit" value=<?php
                                                        if ($value['public_status'] === '0') {
                                                            print "非公開&nbsp→&nbsp公開";
                                                        } else if ($value['public_status'] === '1') {
                                                            print "公開&nbsp→&nbsp非公開";
                                                        }
                                                        ?>>
                            <input type="hidden" name="change_status" value=<?php print htmlspecialchars($value['public_status'], ENT_QUOTES, 'UTF-8'); ?>>
                            <input type="hidden" name="drink_id" value=<?php print htmlspecialchars($value['drink_id'], ENT_QUOTES, 'UTF-8'); ?>>
                        </td>
                    </form>

                </tr>
            <?php } ?>
        </table>
</body>
</htlm>