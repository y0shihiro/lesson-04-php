<?php
require('./dbconnect.php');
require('./function.php');

session_start();

if (isset($_SESSION['id']) && $_SESSION['time'] + 60 * 60 > time()) {
  //ログインしている
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  //ログインしていない
  header('Location: login.php');
  exit();
}

//投稿を記録する
if (!empty($_POST)) {
  if ($_POST['message'] != '') {
    if ($_POST['reply_post_id'] == '') {
      $_POST['reply_post_id'] = NULL;
    }
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));

    //リロードによる投稿の重複を防ぐ処理
    header('Location: index.php');
    exit();
  }
}

//投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
$page = max($page, 1);

//最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p.*, COUNT(l.post_id) AS like_cnt, l.member_id AS likes_member_id FROM members m, posts p LEFT JOIN likes l ON p.id=l.post_id WHERE m.id=p.member_id GROUP BY l.post_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

//返信の場合
if (isset($_REQUEST['res'])) {
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
  $response->execute(array($_REQUEST['res']));
  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'];
}

//いいねボタン
if (isset($_REQUEST['like'])) {

  //いいねを押したメッセージの投稿者を調べる
  $contributor = $db->prepare('SELECT member_id FROM posts WHERE id=?');
  $contributor->execute(array($_REQUEST['like']));
  $pressed_message = $contributor->fetch();

  //いいねを押した人とメッセージ投稿者が同一人物でないか確認
  if ($_SESSION['id'] != $pressed_message['member_id']) {

    //過去にいいね済みであるか確認
    $pressed = $db->prepare('SELECT COUNT(*) AS cnt FROM likes WHERE post_id=? AND member_id=?');
    $pressed->execute(array(
      $_REQUEST['like'],
      $_SESSION['id']
    ));
    $my_like_cnt = $pressed->fetch();

    //いいねのデータを挿入or削除
    if ($my_like_cnt['cnt'] < 1) {
      $press = $db->prepare('INSERT INTO likes SET post_id=?, member_id=?, created=NOW()');
      $press->execute(array(
        $_REQUEST['like'],
        $_SESSION['id']
      ));
      header("Location: index.php?page={$page}");
      exit();
    } else {
      $cancel = $db->prepare('DELETE FROM likes WHERE post_id=? AND member_id=?');
      $cancel->execute(array(
        $_REQUEST['like'],
        $_SESSION['id']
      ));
      header("Location: index.php?page={$page}");
      exit();
    }
  }
}

//ログインしている人がいいねしたメッセージをすべて取得
$like = $db->prepare('SELECT post_id FROM likes WHERE member_id=?');
$like->execute(array($_SESSION['id']));
while ($like_record = $like->fetch()) {
  $my_like[] = $like_record;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="./style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
      <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
      <form action="" method="post">
        <dl>
          <dt><label for="message"><?php echo h($member['name']); ?>さん、メッセージをどうぞ</label></dt>
          <dd>
            <textarea name="message" id="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
            <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>">
          </dd>
          <div>
            <input type="submit" value="投稿する">
          </div>
        </dl>
      </form>

      <?php foreach ($posts as $post) : ?>
        <div class="msg">
          <img src="../../../member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">

          <p>
            <?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]
          </p>

          <p class="day">
            <a class="created" href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>

            <!-- いいね表示部分 -->
            <?php
              $my_like_cnt = 0;
              if (!empty($my_like)) {
                foreach ($my_like as $like_post) {
                  foreach ($like_post as $like_post_id) {
                    if ($like_post_id == $post['id']) {
                      $my_like_cnt = 1;
                    }
                  }
                }
              }
              ?>
            <?php if ($my_like_cnt < 1) : ?>
              <a class="heart" href="index.php?like=<?php echo h($post['id']); ?>&page=<?php echo h($page); ?>">&#9825;</a>
            <?php else : ?>
              <a class="heart__red" href="index.php?like=<?php echo h($post['id']); ?>&page=<?php echo h($page); ?>">&#9829;</a>
            <?php endif; ?>
            <span><?php echo h($post['like_cnt']); ?></span>

            <?php if ($post['reply_post_id'] > 0) : ?>
              <a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
            <?php endif; ?>
            
            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #f33">削除</a>]
            <?php endif; ?>
          </p>
          
        </div>
      <?php endforeach; ?>

      <ul class="paging">
        <?php
        if ($page > 1) {
          ?>
          <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
        <?php
        } else {
          ?>
          <li>前のページへ</li>
        <?php
        }
        ?>
        <?php
        if ($page < $maxPage) {
          ?>
          <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
        <?php
        } else {
          ?>
          <li>次のページへ</li>
        <?php
        }
        ?>
      </ul>
    </div>
  </div>
</body>

</html>