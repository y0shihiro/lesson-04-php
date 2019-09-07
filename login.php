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
      <div class="lead">
        <p>メールアドレスとパスワードを記入してログインしてください。</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join">入会手続きをする</a></p>
      </div>
      <form action="" method="post">
        <dl>
          <dt>メールアドレス</dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255">
          </dd>
          <dt>パスワード</dt>
          <dd>
            <input type="password" name="password" size="35" maxlength="255">
          </dd>
          <dt>ログイン情報の記録</dt>
          <dd>
            <input id="save" type="checkbox" name="save" value="on"><label for="save">次回からは自動的にログインする</label>
          </dd>
        </dl>
        <div><input type="submit" value="ログインする"></div>
      </form>
    </div>
  </div>
</body>

</html>