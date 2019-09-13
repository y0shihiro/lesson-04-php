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
// try {
//   $db = new PDO('mysql:dbname=mydb;host=localhost;charset=utf8', 'root', 'root');
// } catch (PODException $e) {
//   echo 'DB接続エラー: ' . $e->getMessage();
// }
if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
  $page = $_REQUEST['page'];
} else {
  $page = 1;
}
$start = 5 * ($page - 1);
$memos = $db->prepare('SELECT * FROM memos ORDER BY id LIMIT ?, 5');
$memos->bindParam(1, $start, PDO::PARAM_INT);
$memos->execute();
?>
<article>
<?php while ($memo = $memos->fetch()): ?>
  <p>
    <a href="memo.php?id=<?php print($memo['id']); ?>">
    <?php print(mb_substr($memo['memo'], 0, 50)); ?>
    <?php print((mb_strlen($memo['memo']) > 50 ? '...' : '')); ?>
    </a>
  </p>
  <time><?php print($memo['created_at']); ?></time>
  <hr>
<?php endwhile; ?>

<?php if ($page >= 2): ?>
  <a href="index.php?page=<?php print($page-1); ?>"><?php print($page-1); ?>ページ目へ</a>
<?php endif; ?>
|
<?php
$counts = $db->query('SELECT COUNT(*) as cnt FROM memos');
$count = $counts->fetch();
$max_page = ceil($count['cnt'] / 5) + 1;
if ($page < $max_page):
?>
<a href="index.php?page=<?php print($page+1); ?>"><?php print($page+1); ?>ページ目へ</a>
<?php endif; ?>
</article>

<!-- // $count = $db->exec('INSERT INTO my_items SET maker_id=1, item_name="もも", price=210, keyword="缶詰, ピンク, 甘い", sales=0, created="2018-01-23", modified="2018-01-23"');
// echo $count . '件のデータを挿入しました';

// $count = $db->exec('UPDATE my_items SET item_name="白桃" WHERE id=5');
// echo $count . '件変更しました';

// $count = $db->exec('DELETE FROM my_items WHERE id=5');
// echo $count . '件削除しました';

// $records = $db->query('SELECT * FROM my_items');
// while ($record = $records->fetch()) {
//   print($record['item_name'] . "\n");
// }

// $records = $db->query('SELECT COUNT(*) AS record_count FROM my_items');
// $record = $records->fetch();
// print('件数は、' . $record['record_count'] . '件です');
?> -->
</main>
</body>    
</html>