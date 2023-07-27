<?php
//likeテーブルに送信されてきた内容を追加する
session_start();
include("functions.php");

$user_id = $_GET['user_id'];
$todo_id = $_GET['todo_id'];

$pdo = connect_to_db();

//likeされているかチェックする:count(*)関数で件数を取得
$sql = 'SELECT COUNT(*)FROM like_table WHERE user_id=:user_id AND todo_id=:todo_id' ;

//ユーザーが入力した値はセキュリティ強化の為バインド変数に置き換える
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}
//fetchColumn()関数で件数を取得
$like_count = $stmt->fetchColumn();
//データを確認する
// var_dump($like_count);
// exit();

//されていなければINSERTで挿入
if($like_count === 0){
    $sql = 'INSERT INTO like_table(id, user_id, todo_id, created_at) VALUES(NULL, :user_id, :todo_id, now())';
}else{
//されていればDELETE
    $sql = 'DELETE FROM like_table WHERE user_id=:user_id AND todo_id=:todo_id';
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header("Location:todo_read.php");
exit();
