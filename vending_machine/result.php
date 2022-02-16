<?php
$host = 'mysql34.conoha.ne.jp'; // データベースのホスト名又はIPアドレス
$user = 'bcdhm_tennoji0003';  // MySQLのユーザ名
$passwd   = '*tF2xWBV';    // MySQLのパスワード
$dbname   = 'bcdhm_tennoji0003';    // データベース名
$link = mysqli_connect($host, $user, $passwd, $dbname);
$money = '';
$message = [];
$err_msg = [];
$drink_data = [];
if (isset($_POST['money']) === TRUE) {
    $money = $_POST['money'];
}
$drink_id = '';
if (isset($_POST['drink_id']) === TRUE) {
    $drink_id = $_POST['drink_id'];
}
if($link) {
    if (preg_match('/^[0-9]+$/', $money) === 1 && $drink_id !== '') {
        $date = date('Y-m-d H:i:s');
        mysqli_set_charset($link, 'utf8');
        // トランザクション成否判定
        mysqli_autocommit($link, false);
        $query = 'SELECT dt.drink_image, dt.drink_name, dt.price, dit.quantity, dt.public_status FROM drink_table AS dt join drink_inventory_table AS dit ON dt.drink_id = dit.drink_id WHERE dt.drink_id = '.$drink_id.'';
        $result = mysqli_query($link, $query);
        while ($row = mysqli_fetch_array($result)) {
            $drink_data[] = $row;
        }
   
   foreach ($drink_data as $value) {
       $drink_image = $value['drink_image'];
       $drink_name = $value['drink_name'];
       $price = $value['price'];
       $quantity = $value['quantity'];
       $public_status = $value['public_status'];
   }
   if($money >= $price && $quantity >= 1 && $public_status ==='1') {
       $money = $money - $price;
       $quantity = $quantity - 1;
       //drink_inventory_tableに個数と更新日のupdateを行う
       $sql = 'UPDATE drink_inventory_table SET quantity = '.$quantity.', update_date = "'.$date.'" WHERE drink_id ='.$drink_id.'';
       if (mysqli_query($link, $sql) === TRUE) {
            //drink_history_tableに購入日を追加する。
            $sql2 = 'INSERT INTO drink_history_table (drink_id, purchase_date) VALUES('.$drink_id.',"'.$date.'")';
            if (mysqli_query($link, $sql2) === TRUE) {
                $message[] = 'がしゃん！【'.$drink_name.'】が買えました！';
                $message[] = 'おつりは【'.$money.'円】です';
            } else {
                $err_msg[] = 'error $sql2:購入失敗';
            }
       } else {
           var_dump(mysqli_query($link, $sql));
           $err_msg[] = 'error $sql:購入失敗';
       }
   } else if ($quantity === '0') {
       $err_msg[] = '売り切れです！';
   } else if ($public_status !== '1') {
       $err_msg[] = '現在販売していません！';
   } else {
       $err_msg[] = 'お金がたりません！';
   }
   // トランザクション成否判定
    if (count($err_msg) === 0) {
           // 処理確定
           mysqli_commit($link);
       } else {
           // 処理取消
           mysqli_rollback($link);
       }
    // 結果セットを開放します
   mysqli_free_result($result);
   // 接続を閉じます
   mysqli_close($link);
        
    } else {
        if ($money === '') {
            $err_msg[] = 'お金を投入してください';
        } else if (preg_match('/^[0-9]+$/', $money) !== 1) {
            $err_msg[] = 'お金は半角数字を入力してください';
        }
        if ($drink_id === '') {
            $err_msg[] = '商品を選択してください';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>自動販売機結果</title>
</head>
<body>
    <h1>自動販売機結果</h1>
<?php if($message !== []) {?>
    <img src = "images/<?php print htmlspecialchars($drink_image, ENT_QUOTES, 'UTF-8');?>">
    <?php foreach ($message as $value) { ?>
    <p><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
        <?php }
    } ?>
<?php if ($err_msg !== '') { foreach ($err_msg as $value) { ?>
    <p><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
        <?php }
    }?>
    <footer><a href="index.php">戻る</a></footer>
</body>
</html>