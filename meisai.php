<?php 
$user = 'ts44gj';
$password = 'ts44gj';
$db = 'laravel_news'; 
$host = 'localhost';
$port = 3306;
$link = mysqli_init();
$success = mysqli_real_connect(
  $link,
  $host,
  $user,
  $password,
  $db,
  $port
);

$uniqueId = uniqid(); //ユニークなIDを自動生成

$id = $_GET['id'];
$page_data = [];

$comment_board = []; //全体配列
$text = '';
$DATA = []; //追加するデータ
$COMMENT_BOARD = []; //表示する配列

$error_message = [];

$query = "SELECT * FROM `data_table` WHERE `id` = '${id}'";
if ($success) {
  $result = mysqli_query($link, $query);
  while ($row = mysqli_fetch_array($result)) {
    $page_data = [$row['id'], $row['title'], $row['article']];
  }
}

$commnetQuery = "SELECT * FROM `comment_table` WHERE `article_id` = '${id}'";
if ($success) {
  $result = mysqli_query($link, $commnetQuery);
  while ($row = mysqli_fetch_array($result)) {
    $COMMENT_BOARD[] = [$row['id'], $row['article_id'], $row['comment_text']];
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$_POSTはHTTPリクエストで渡された値を取得する
  //リクエストパラメーターが空でなければ
  if (!empty($_POST['txt'])) {
    //投稿ボタンが押された場合
    if (mb_strlen($_POST['txt']) > 50) {
      $error_message[] = "コメント数は50文字以内でお願いします。";
    } else {

      //$textに送信されたテキストを代入
      $text = $_POST["txt"];

      
      $insert_query = "INSERT INTO `comment_table`(`id`, `article_id`, `comment_text`) VALUES ('{$uniqueId}', '{$id}', '{$text}')";
      mysqli_query($link, $insert_query);



  //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了
    exit;
    }
  } else if (isset($_POST['del'])) {
    //削除ボタンが押された場合

    $delete_query = "DELETE FROM `comment_table` WHERE `id` = '{$_POST['del']}'";
    mysqli_query($link, $delete_query);


    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了


    exit;
  } else if (empty($_POST['txt'])) {
    $error_message[] = "コメントは必須です。";
  }
}

?>
 

 <!DOCTYPE html>
<html lang="ja">

<head>
  <meta name="viewport" content="width=device-width, initial-scale= 1.0">
  <meta http-equiv="content-type" charset="utf-8">
  <link rel='stylesheet' href='./css/article.css' type="text/css">
  <title>Laravel news</title>
</head>

<body>
  <h1 class='title link'><a href='/'>Laravel News</a></h1>

  <section class="main">
    <div class='content'>
      <h2 class="subTitle"><?php echo $page_data[1]; ?></h2>
      <p class='article'><?php echo $page_data[2]; ?></p>
    </div>
    <!-- エラーメッセージ -->
    <ul>
      <?php foreach ($error_message as $error) : ?>
        <li>
          <?php echo $error ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class='commentContainer'>
      <!-- コメント表示部分 -->
      <form method="post" class="commentForm">
        <textarea name="txt" class="inputFlex commentInput"></textarea>
        <input type="submit" value="コメントを書く" name='<?php echo $id; ?>' class="commnetSubmitStyle">
      </form>
      <?php foreach ((array)$COMMENT_BOARD as $DATA) : ?>
        <div class="commentContent">
          <p>
            <?php echo $DATA[2] ?>
          </p>
          <div>
            <form method="post">
              <input type="hidden" name="del" value="<?php echo $DATA[0]; ?>">
              <input type="submit" value="コメントを消す" class="deleteComment">
            </form>
          </div>
        </div>

      <?php endforeach; ?>
    </div>
  </section>
</body>

</html>