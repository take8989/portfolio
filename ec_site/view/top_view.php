<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>商品一覧ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="./top.php">
        <img class="logo" src="./images/logo.png" alt="CodeSHOP">
      </a>
      <a class="nemu" href="logout.php">ログアウト</a>
      <a href="./cart.php" class="cart"></a>
      <p class="nemu">ユーザー名：<?php print $user_name; ?></p>
    </div>
  </header>
  <div class="content">
    <?php
    if($message !== '') { foreach ($message as $value) { ?>
        <p class='success-msg'><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }
    if ($err_msg !== '') { foreach ($err_msg as $value) { ?>
        <p class='err-msg' ><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }?>

    <ul class="item-list">
      <?php foreach ($goods_data as $value) {
        if ($value['status'] === '1') {
      ?>
      <li>
        <div class="item">
          <form action="./top.php" method="post">
            <img class="item-img" src="images/<?php print htmlspecialchars($value['img'], ENT_QUOTES, 'UTF-8');?>" >
            <div class="item-info">
              <span class="item-name"><?php print htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></span>
              <span class="item-price">￥<?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8');?></span>
            </div>
            <?php if ($value['stock'] === '0') { ?><span class="red">売り切れ</span>
            <?php } else { ?><input class="cart-btn" type="submit" value="カートに入れる">
            <?php }?>
            <input type="hidden" name="item_id" value="<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>">
            <input type="hidden" name="sql_kind" value="insert_cart">
          </form>
        </div>
      </li>
      <?php }
      } ?>
    </ul>
  </div>
</body>
</html>