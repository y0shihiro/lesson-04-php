<?php
require('./dbconnect.php');
?>

<!doctype html>
<html lang="ja">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <title>よくわかるPHPの教科書</title>
  </head>
  <body>
    <header>
      <h1 class="font-weight-normal">よくわかるPHPの教科書</h1>    
    </header>
    
    <main>
      <h2>Practice</h2>
      <?php
      $statement = $db->prepare('INSERT INTO memos SET memo=?, created=NOW()');
      $statement->execute(array($_POST['memo']));
      echo 'メッセージが登録されました';
      ?>
    </main>
</body>    
</html>