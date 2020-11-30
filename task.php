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

$id = uniqid(); //ユニークなIDを自動生成
$title=""; //タイトルの変数
$text=""; //テキストの変数

$DATA = []; //一回分の投稿の情報を入れる
$BOARD = []; //全ての投稿の情報を入れる

$ERROR=array();//エラーを確認するための配列

$query =  "SELECT * FROM `data_table`";
if($success){
    $result =  mysqli_query($link,$query);
    while($row = mysqli_fetch_array($result)){
        $BOARD[]= [$row["id"],$row["title"],$row["article"]];
    }

  }
if ($_SERVER["REQUEST_METHOD"] === "POST"){

//文字数制限
if(mb_strlen($_POST["title"])>30){
  $ERROR[]="タイトルは30文字以内で入力してください";
}
//タイトル未入力
else if(empty($_POST["title"])){
 $ERROR[]="タイトルを入力してください";}
//記事未入力
else if(empty($_POST["text"])){
 $ERROR[]="記事を入力してください";}


  //リクエストパラメータが空でなければ
else if(!empty($_POST["text"]) && !empty($_POST["title"])){
   //投稿ボタンが押された時
   //$title・$textに送信されたテキストを代入
 $title=$_POST['title'];
 $text=$_POST['text'];
  //この後に保存の処理をする
  //新規データ
  $DATA=[$id,$title,$text];
  $BOARD[] = $DATA;


  $insert_query = "INSERT INTO `data_table`(`id`,`title`,`article`) VALUES ('{$id}','{$title}','{$text}')";
  mysqli_query($link,$insert_query);

  header('Location: ' . $_SERVER['SCRIPT_NAME']);
  exit;
}
}

?>
<!DOCTYPE html>
 <html>

 <head>
  <meta charset='utf-8'>
  <title>larabelnews<</title>
  <!--<link rel="stylesheet" href="stylesheet.css">-->
 </head>

 <body>


 <!--確認ダイアログを表示するための関数-->
 <script>
  function dialog(){
    let popup =confirm("入力に間違いはないですか？")

    return popup;
    }　
 </script>

  <h1>larabalnews</h1>
     <!--エラーメッセージの表示-->
     <ul>
      <?php foreach($ERROR as $erro_message): ?>
      <li><?php echo $erro_message;?></li>
      <?php endforeach;?>  
      </ul>
      
  <!--投稿-->
   <form id="push"  method="POST" name="lalavel news"  onsubmit="return dialog()"> 
      <div>
         <p>タイトル</p>
          <input type="text" name="title" >
      </div>
      <div>
          <p>記事</p>
          <textarea row="10"cols="60"name="text" value="text" ></textarea>
      </div>
      <div>
          <input type="submit" name="push" value="投稿"　>
      </div>
   </form>

  <!--コメント-->
  <hr>
  <div>
     <!--foreachで投稿を繰り返し表示させていく-->
      <?php foreach ($BOARD as $ARTICLE)  : ?>
  </div>
  <p>
      <?php echo $ARTICLE[1];?>
  </p>
  <p>
      <?php echo $ARTICLE[2];?>
  </p>
  <!--記事全文・コメントへのリンク貼り付け--><!-- id=$ARTICLE[0]でurlにidを付随-->
  <p>
     <a href="http://localhost/lalabel-news-develop2/meisai.php/?id=<?php echo $ARTICLE[0]?>">記全文・コメント</a>
      </p>
  <hr>
  <div>
    <?php endforeach; ?>
  </div>

 </body>

</html>
