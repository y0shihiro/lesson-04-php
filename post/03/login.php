<?php
require('./function.php');
require('./dbconnect.php');

session_start();

if ($_COOKIE['email'] != '') {
  $_POST['email'] = $_COOKIE['email'];
  $_POST['password'] = $_COOKIE['password'];
  $_POST['save'] = 'on';
}

//ログインの処理
if (!empty($_POST)) {
  if ($_POST['email'] != '' && $_POST['password'] != '') {
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    //ログイン成功
    if ($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

        //ログイン情報を記録する
        if ($_POST['save'] == 'on') {
          setcookie('email', $_POST['email'], time()+60);
          setcookie('password', $_POST['password']. time()+60);
        }

      header('Location: index.php');
      exit();
    } else {
      $error['login'] = 'failed';
    }
  } else {
    $error['login'] = 'blank';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ログインする</h1>
    </div>
    <div id="content">
      <div id="lead">
        <p>メールアドレスとパスワードを入力してログインしてください</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/">入会手続をする</a></p>
      </div>
      <form action="" method="post">
        <dl>
          <dt>メールアドレス</dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($_POST['email']) ?>">
            <?php if ($error['login'] == 'blank') : ?>
              <p class="error">* メールアドレスとパスワードをご記入ください</p>
            <?php endif; ?>
            <?php if ($error['login'] == 'failed') : ?>
              <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
            <?php endif; ?>
          </dd>
          <dt>パスワード</dt>
          <dd>
            <input type="password" name="password" size="35" maxlength="255" value="<?php echo h($_POST['password']); ?>">
          </dd>
          <dt>ログイン情報の記録</dt>
          <dd>
            <input type="checkbox" id="save" name="save" value="on"><label for="save">次回からは自動的にログインする</label>
          </dd>
        </dl>
        <div><input type="submit" value="ログインする"></div>
      </form>
    </div>

  </div>
</body>

</html>