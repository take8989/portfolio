<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>商品管理ページ</title>
  <link type="text/css" rel="stylesheet" href="css/admin.css">
</head>
<body>
  <h1>CodeSHOP 管理ページ</h1>
  <div>
    <a class="nemu" href="logout.php">ログアウト</a>
    <a href="./admin_user.php">ユーザ管理ページ</a>
  </div>
  <?php
    if($message !== '') { foreach ($message as $value) { ?>
        <p class='success-msg'><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }
    if ($err_msg !== '') { foreach ($err_msg as $value) { ?>
        <p class='err-msg' ><?php print htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></p>
            <?php }
            }?>
  <section>
    <h2>商品の登録</h2>
    <form method="post" enctype="multipart/form-data">
      <div><label>商品名: <input type="text" name="new_name" value=""></label></div>
      <div><label>値　段: <input type="text" name="new_price" value=""></label></div>
      <div><label>個　数: <input type="text" name="new_stock" value=""></label></div>
      <div><label>商品画像:<input type="file" name="new_img"></label></div>
      <div><label>ステータス:
        <select name="new_status">
          <option value="0">非公開</option>
          <option value="1" selected>公開</option>
        </select>
        </label>
      </div>
      <input type="hidden" name="sql_kind" value="insert">
      <div><input type="submit" value="商品を登録する"></div>
    </form>
  </section>
  <section>
    <h2>商品情報の一覧・変更</h2>
    <table>
      <tr>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価　格</th>
        <th>在庫数</th>
        <th>ステータス</th>
        <th>操作</th>
      </tr>
      <?php
      foreach ($goods_data as $value) { ?><tr <?php if ($value['status'] === '0') {print 'class = "status_false"';}?>>
        <form method="post" >
          <td><img src = "images/<?php print htmlspecialchars($value['img'], ENT_QUOTES, 'UTF-8');?>"></td>
          <td class="name_width"><?php print htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text_align_right"><?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); ?>円</td>
          <td>
            <input type="text"  class="input_text_width text_align_right" name="update_stock" value=<?php print htmlspecialchars($value['stock'], ENT_QUOTES, 'UTF-8'); ?>>個&nbsp;&nbsp;
            <input type="submit" value="変更">
          </td>
          <input type="hidden" name="goods_id" value=<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>>
          <input type="hidden" name="sql_kind" value="update">
        </form>
        <form method="post">
          <td>
            <input type="submit" value=<?php
            if($value['status'] === '0') { print "非公開&nbsp→&nbsp公開";}
            else if ($value['status'] === '1') { print "公開&nbsp→&nbsp非公開";}
            ?>>
            <input type="hidden" name="change_status" value=<?php print htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8');?>>
            <input type="hidden" name="goods_id" value=<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>>
          </td>
        </form>
        <form method="post">
          <td><input type="submit" value="削除する"></td>
          <input type="hidden" name="goods_id" value=<?php print htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>>
          <input type="hidden" name="delete" value="delete">
        </form>
      </tr>
      <?php } ?>
    </table>
  </section>
</body>
</html>
