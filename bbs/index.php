<?php

$filename = 'bbs.txt';
$user_name = '';
$user_comment = '';
$error_name = '';
$error_comment = '';
$log = date('-Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['user_name']) === TRUE && isset($_POST['user_comment']) === TRUE) {

        $user_name = $_POST['user_name'];
        $user_comment = $_POST['user_comment'];

        if ($user_name === '') {
            $error_name = '名前を入力してください';
        }

        if ($user_comment === '') {
            $error_comment = 'ひとことを入力してください';
        }

        if (mb_strlen($user_name) > 20) {
            $error_name = '名前は20文字以内で入力してください';
        }

        if (mb_strlen($user_comment) > 100) {
            $error_comment = 'ひとことは100文字以内で入力してください';
        }

        if ($error_name === '' && $error_comment === '') {
            if (($fp = fopen($filename, 'a')) !== FALSE) {
                if (fwrite($fp, $user_name . ':' . ' ' . $user_comment . ' ' . $log . "\n") === FALSE) {
                    print 'ファイル書き込み失敗:  ' . $filename;
                }
                fclose($fp);
            }
        }
    }
}

$data = [];

if (is_readable($filename) === TRUE) {
    if (($fp = fopen($filename, 'r')) !== FALSE) {
        while (($tmp = fgets($fp)) !== FALSE) {
            $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
        }
        fclose($fp);
    }
} else {
    $data[] = 'ファイルがありません';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ひとこと掲示板</title>
</head>

<body>
    <h1>ひとこと掲示板</h1>
    <ul>
        <?php if ($error_name !== '') { ?>
            <li><?php print $error_name; ?></li>
        <?php } ?>
        <?php if ($error_comment !== '') { ?>
            <li><?php print $error_comment; ?></li>
        <?php } ?>
    </ul>
    <form action="index.php" method="post">
        名前：<input type="text" name="user_name">
        ひとこと：<input type="text" name="user_comment" size="60">
        <input type="submit" name="submit" value="送信">
    </form>
    <ul>
        <?php foreach (array_reverse($data) as $read) { ?>
            <li><?php print $read; ?></li>
        <?php } ?>
    </ul>
</body>

</html>