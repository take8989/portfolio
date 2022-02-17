<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ショッピングカートページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>

<body>
  <header>
    <div class="header-box">
      <a href="./top.php">
        <img class="logo" src="./images/logo.png" alt="CodeCamp SHOP">
      </a>
      <a class="nemu" href="logout.php">ログアウト</a>
      <a href="./cart.php" class="cart"></a>
      <p class="nemu">ユーザー名：<?php print $user_name; ?></p>
    </div>
  </header>
  <div class="content">
    <h1 class="title">ショッピングカート</h1>
    <?php if ($cart_data === []) {
    ?><p>商品はありません。</p>
      <?php }
    if ($message !== '') {
      foreach ($message as $value) { ?>
        <p class='success-msg'><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php }
    }
    if ($err_msg !== '') {
      foreach ($err_msg as $value) { ?>
        <p class='err-msg'><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php }
    } ?>
    <div class="cart-list-title">
      <span class="cart-list-price">価格</span>
      <span class="cart-list-num">数量</span>
    </div>
    <ul class="cart-list">
      <?php foreach ($cart_data as $value) { ?>
        <li>
          <div class="cart-item">
            <img class="cart-item-img" src="images/<?php print htmlspecialchars($value['img'], ENT_QUOTES, 'UTF-8'); ?>">
            <span class="cart-item-name"><?php print htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></span>
            <form class="cart-item-del" action="./cart.php" method="post">
              <input type="submit" value="削除">
              <input type="hidden" name="item_id" value="<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="delete" value="delete_cart">
            </form>
            <span class="cart-item-price">¥<?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); ?></span>
            <form class="form_select_amount" id="form_select_amount<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8'); ?>" action="./cart.php" method="post">
              <input type="text" class="cart-item-num2" min="0" name="select_amount" value="<?php print htmlspecialchars($value['amount'], ENT_QUOTES, 'UTF-8'); ?>">個&nbsp;<input type="submit" value="変更する">
              <input type="hidden" name="item_id" value="<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="sql_kind" value="change_cart">
            </form>
          </div>
        </li>
      <?php } ?>
    </ul>
    <div class="buy-sum-box">
      <span class="buy-sum-title">合計</span>
      <span class="buy-sum-price">¥<?php foreach ($cart_data as $value) {
                                      $sum_price1 = $value['price'] * $value['amount'];
                                      $sum_price2 = $sum_price2 + $sum_price1;
                                    }
                                    print $sum_price2 ?>
      </span>
    </div>
    <div>
      <form action="./finish.php" method="post">
        <input class="buy-btn" type="submit" value="購入する">
      </form>
    </div>
  </div>
</body>

</html>