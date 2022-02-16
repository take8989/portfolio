<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザ登録ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="./top.php">
        <img class="logo" src="./images/logo.png" alt="CodeSHOP">
      </a>
      <a href="./cart.php" class="cart"></a>
    </div>
  </header>
  <div class="content">
    <div class="register">
      <?php
    if($message !== '') { foreach ($message as $value) { ?>
        <p class='success-msg'><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }
    if ($err_msg !== '') { foreach ($err_msg as $value) { ?>
        <p class='err-msg' ><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }?>
      <form method="post" action="./register.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード">
        <div><input type="submit" value="ユーザーを新規作成する">
      </form>
      <div class="login-link"><a href="./login.php">ログインページに移動する</a></div>
    </div>
  </div>
</body>
</html>