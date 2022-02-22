<?php
$host = '********'; // データベースのホスト名又はIPアドレス
$user = '********';  // MySQLのユーザ名
$passwd   = '********';    // MySQLのパスワード
$dbname   = '********';    // データベース名
$link = mysqli_connect($host, $user, $passwd, $dbname);
$drink_data = [];
$message = [];
$err_msg = [];
if ($link) {
    // 文字化け防止
    mysqli_set_charset($link, 'utf8');
    $query = 'SELECT dt.drink_image, dt.drink_name, dt.price, dit.quantity, dt.public_status, dt.drink_id FROM drink_table AS dt join drink_inventory_table AS dit ON dt.drink_id = dit.drink_id WHERE public_status = 1';
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
        #flex {
            width: 600px;
        }

        #flex .drink {
            width: 120px;
            height: 210px;
            text-align: center;
            margin: 10px;
            float: left;
        }

        #flex span {
            display: block;
            margin: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .img_size {
            height: 125px;
        }

        .red {
            color: #FF0000;
        }

        #submit {
            clear: both;
        }
    </style>
</head>

<body>
    <h1>自動販売機</h1>
    <form action="result.php" method="post">
        <div>金額<input type="text" name="money" value=""></div>
        <!--商品一覧-->
        <div id="flex">
            <?php foreach ($drink_data as $value) { ?>
                <div class="drink">
                    <span class="img_size"><img src="images/<?php print htmlspecialchars($value['drink_image'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                    <span><?php print htmlspecialchars($value['drink_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); ?>円</span>
                    <?php if ($value['quantity'] === '0') { ?>
                        <span class="red">売り切れ</span>
                    <?php } else { ?>
                        <input type="radio" name="drink_id" value=<?php print htmlspecialchars($value['drink_id'], ENT_QUOTES, 'UTF-8'); ?>>
                    <?php } ?>
                </div>
            <?php } ?>
            <div id="submit">
                <input type="submit" value="■□■□■ 購入 ■□■□■">
            </div>
    </form>
</body>

</html>