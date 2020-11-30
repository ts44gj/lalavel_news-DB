<?php
//変数を用意 PDO
$dsn = "mysql:host=localhost;dbname=laravel_news;charset=utf8"; //DBの場所、名前
$user = "ts44gj"; //DBのユーザ名
$pass = "ts44gj"; //DBのパスワード

//文字化け防止
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'");

//PHPのエラーを表示する
error_reporting(E_ALL &~E_NOTICE);

//DB接続　setAttributeからエラー表示
try {
    $dbh = new PDO ($dsn,$user,$pass,[
    PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e){
    echo $e->getMessage();
    exit;
}

$title=""; //タイトルの変数
$text=""; //テキストの変数
$ERROR=array();//エラーを確認するための配列

//$FILE = "article.txt"; //保存ファイル名//DBの時これはいらないはず
$sql = "SELECT　* FROM　  data_table"; //data_tableより表示
$sth = $pdo -> query($sql);
$aryItem = $sth -> fetchAll(PDO::FETCH_ASSOC);
$FILE = $aryItem;

$id = uniqid(); //ユニークなIDを自動生成
$DATA = []; //一回分の投稿の情報を入れる
$BOARD = []; //全ての投稿の情報を入れる


// $FILEというファイルが存在する時
if (file_exists($FILE)){
  //ファイルを読み込む
$BOARD = json_decode(file_get_contents($FILE));

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
 $title=$_POST["title"];
 $text=$_POST["text"];
  //この後に保存の処理をする
  //新規データ
  $DATA=[$id,$title,$text];
  $BOARD[] = $DATA;
  
  //全体配列をファイルに保存する
  file_put_contents($FILE, json_encode($BOARD)); 


  //sqlでの挿入
  $sql = "INSERT into data_table(id,title,article) VALUES (:id,:title,:article)";
  try {
    $stmt=$dbh->prepare($sql);
    $stmt->bindValue(":id",$id,PDO::PARAM_STR);
    $stmt->bindValue(":title",$_POST["title"],PDO::PARAM_STR);
    $stmt->bindValue(":article",$_POST["text"],PDO::PARAM_STR);
    $check=$stmt->execute();
  if($check){
  print "成功！";
  }else{
  print "失敗！";
  };
  } catch (PDOException $e){
    echo $e->getMessage();
    exit;
  }
  
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
          <input type="submit" name="push" value="投稿"　onclick="">
      </div>
   </form>

  <!--コメント-->
  <hr>
  <div>
     <!--foreachで投稿を繰り返し表示させていく-->
      <?php foreach ((array)$BOARD as $ARTICLE)  : ?>
  </div>
  <p>
      <?php echo $ARTICLE[1];?>
  </p>
  <p>
      <?php echo $ARTICLE[2];?>
  </p>
  <!--記事全文・コメントへのリンク貼り付け--><!-- id=$ARTICLE[0]でurlにidを付随-->
  <p>
     <a href="http://localhost/meisai.php/?id=<?php echo $ARTICLE[0]?>">記全文・コメント</a>
      </p>
  <hr>
  <div>
    <?php endforeach; ?>
  </div>

 </body>

</html>
