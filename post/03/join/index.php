<?php
require('../function.php');
require('../dbconnect.php');
session_start();

//エラー項目の確認
if (!empty($_POST)) {
  if ($_POST['name'] == '') {
    $error['name'] = 'blank';
  }
  if ($_POST['email'] == '') {
    $error['email'] = 'blank';
  }
  if (strlen($_POST['password'] < 4)) {
    $error['password'] = 'length';
  }
  if ($_POST['password'] == '') {
    $error['password'] = 'blank';
  }
  $fileName = $_FILES['image']['name'];
  if (!empty($fileName)) {
    $ext = substr($fileName, -3);
    if ($ext != 'jpg' && $ext != 'gif') {
      $error['image'] = 'type';
    }
  }

  //重複アカウントのチェック
  if (empty($error)) {
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
    $member->execute(array($_POST['email']));
    $record = $member->fetch();
    if ($record['cnt'] > 0) {
      $error['email'] = 'duplicate';
    }
  }

  //画像をアップロードする
  if (empty($error)) {
    $image = date('YmdHis') . $fileName;
    move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);

    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    header('Location: check.php');
    exit();
  }
}

//書き直し
if ($_REQUEST['action'] == 'rewrite') {
  $_POST = $_SESSION['join'];
  $error['rewrite'] = 'true';
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>会員登録</h1>
    </div>
    <div id="content">
      <p>次のフォームに必要事項をご記入ください</p>
      <form action="" method="post" enctype="multipart/form-data">
        <dl>
          <dt><label for="name">ニックネーム<span class="required">必須</span></label></dt>
          <dd>
            <input type="text" name="name" id="name" size="35" maxlength="255" value="<?php h($_POST['name']); ?>">
            <?php if ($error['name'] == 'blank') : ?>
              <p class="error">* ニックネームを入力してください</p>
            <?php endif; ?>
          </dd>
          <dt><label for="email">メールアドレス<span class="required">必須</span></label></dt>
          <dd>
            <input type="text" name="email" id="email" size="35" maxlength="255" value="<?php h($_POST['email']); ?>">
            <?php if ($error['email'] == 'blank') : ?>
              <p class="error">* メールアドレスを入力してください</p>
            <?php endif; ?>
            <?php if ($error['email'] == 'duplicate') : ?>
              <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
            <?php endif; ?>
          </dd>
          <dt><label for="password">パスワード<span class="required">必須</span></label></dt>
          <dd>
            <input type="password" name="password" id="password" size="10" maxlength="20" value="<?php h($_POST['password']); ?>">
            <?php if ($error['password'] == 'blank') : ?>
              <p class="error">* パスワードを入力してください</p>
            <?PHP endif; ?>
            <?php if ($error['password'] == 'length') : ?>
              <p class="error">* パスワードは４文字以上で入力してください</p>
            <?php endif; ?>
          </dd>
          <dt><label for="image">写真など</label></dt>
          <dd>
            <input type="file" name="image" id="image" size="35">
            <?php if ($error['image'] == 'type') : ?>
              <p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
              <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
            <?php endif; ?>
          </dd>
        </dl>
        <div><input type="submit" value="入力内容を確認する"></div>
      </form>
    </div>

  </div>
</body>

</html>