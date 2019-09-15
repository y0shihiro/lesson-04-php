<?php require('dbconnect.php'); ?>
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
    if (!empty($_GET['page']) && is_numeric($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $start = 3 * ($page - 1);
    $memos = $db->prepare('SELECT * FROM memos ORDER BY id DESC LIMIT ?,3');
    $memos->bindParam(1, $start, PDO::PARAM_INT);
    $memos->execute();
    ?>
    <article>
      <?php while ($memo = $memos->fetch()) : ?>
        <p>
          <a href="memo.php?id=<?php print($memo['id']); ?>">
            <?php print(mb_substr($memo['memo'], 0, 50)); ?>
            <?php print((mb_strlen($memo['memo']) > 50 ? '...' : '')); ?>
          </a>
        </p>
        <time><?php print($memo['created_at']); ?></time>
        <hr>
      <?php endwhile; ?>

      <?php if ($page >= 2) : ?>
        <a href="index.php?page=<?php print($page - 1); ?>"><?php print($page - 1); ?>ページ目へ</a>
      <?php endif; ?>
      |
      <?php
      $counts = $db->query('SELECT COUNT(*) AS cnt FROM memos');
      $count = $counts->fetch();
      $max_page = ceil($count['cnt'] / 5) + 1;
      if ($page < $max_page):
      ?>
        <a href="index.php?page=<?php print($page + 1); ?>"><?php print($page + 1); ?>ページ目へ</a>
      <?php endif; ?>
    </article>
  </main>
</body>

</html>