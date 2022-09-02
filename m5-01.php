<!DOCTYPE　html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>m5-01</title>
</head>
<body>


    
<?php 

//DB接続
    $dsn = 'mysql:dbname=**********;host=localhost';
    $user = '*********';
    $password = '**********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
//テーブルを作成する
    $sql = "CREATE TABLE IF NOT EXISTS 800table"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TIMESTAMP,"
    . "password1 char(32)"
    .");";
    $stmt = $pdo->query($sql);
 
    
//名前・コメント・パスワードがからじゃなかったら
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password1"])){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $password1 = $_POST["password1"];
    $date = date("Y/m/d H:i:s");
    //編集ボタンが押されていなかったらの条件分岐
    if(empty($_POST["edit_post"])){//edit_postがない場合は新規投稿
        $sql = $pdo->prepare("INSERT INTO 800table (name, comment, date, password1) VALUES (:name, :comment, :date, :password1)");
        $sql ->bindParam(':name', $name, PDO::PARAM_STR);
        $sql ->bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql ->bindParam(':date', $date, PDO::PARAM_STR);
        $sql ->bindParam(':password1', $password1, PDO::PARAM_STR);
        $sql ->execute();
    } else {
        //編集が行われた際（edit_postが入ってきたとき）に、更新する
        $edit_post = $_POST["edit_post"];
        $sql = 'UPDATE 800table SET name=:name, comment=:comment, password1=:password1 where id=:edit_post';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':password1', $password1, PDO::PARAM_STR);
        $stmt->bindParam(':edit_post', $edit_post, PDO::PARAM_INT);
        $stmt->execute();
    }
} 
elseif(isset($_POST["edit_id"])){
        $edit_id = $_POST["edit_id"];
        $password_edit = $_POST["password_edit"];
        $sql = 'SELECT * FROM 800table where id=:edit_id and password1=:password_edit';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
        $stmt->bindParam(':password_edit', $password_edit, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach($results as $row){
            $edit_name = $row["name"];
            $edit_comment = $row["comment"];
            $edit_password = $row["password1"];
        }
} 



elseif(!empty($_POST["delete_id"])){
    $delete_id = $_POST["delete_id"];
    $password_delete = $_POST["password_delete"];
    $sql = 'delete from 800table where id=:delete_id and password1=:password_delete';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
    $stmt->bindParam(':password_delete', $password_delete, PDO::PARAM_STR);
    $stmt->execute();
}
//表示機能
$sql = 'SELECT * FROM 800table';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	echo $row['id'].",";
	echo $row['name'].",";
	echo $row['comment'].",";
	echo $row['date'].'<br>';
}
echo "<hr>";

?>

<form method="POST" action="#">
    <h2>入力フォーム</h2>
    名前：
    <dd><input type="text" name="name"  value="<?php if(isset($edit_name)){echo $edit_name;}?>"></dd>
    コメント：
    <dd><input type="text" name="comment" value="<?php if(isset($edit_comment)){echo $edit_comment;}?>"></dd>
    パスワード：
    <dd><input type="text" name="password1" value="<?php if(isset($edit_password)){echo $edit_password;}?>"></dd>
    <dd><input type="hidden" name="edit_post" value="<?php if(isset($edit_id)){echo $edit_id;}?>"></dd>
    <dd><input type="submit" name="submit" value="送信"></dd>
</form>

<form method="POST" action="#">
    <h2>削除フォーム</h2>
    削除対象番号：
    <dd><input type="number" name="delete_id" ></dd>
    パスワード：
    <dd><input type="text" name="password_delete"></dd>
    <dd><input type="submit" name="delete_submit" value="削除"></dd>
</form>

<form method="POST" action="#">
    <h2>編集フォーム</h2>
    編集対象番号：
    <dd><input type="number" name="edit_id" ></dd>
    パスワード：
    <dd><input type="text" name="password_edit" ></dd>
    <dd><input type="submit" name="submit_edit" value="編集"></dd>
</form>


    
</body>
</html>