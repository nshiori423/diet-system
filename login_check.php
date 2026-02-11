<?php
session_start();

if(!empty($_POST['text_id']) && !empty($_POST['text_password'])){

    require "db.php";

   if($_POST['text_id'] === "admin" && $_POST['text_password'] === "ADMIN_PASSWORD"){

        $_SESSION['admin'] = 'admin';
        echo 'GoAdPage';
    }else{
        $str = 'select * from user_info where id=?';
        $sql = $pdo->prepare($str);
        $sql->execute([$_POST['text_id']]);
        $data = "";
        foreach($sql as $row){
            $data .= $row['id'] . ',' . $row['password'];
            $id = $row['id'];
            $password = $row['password'];
        }
        if(empty($id)){
            //IDが存在しない
            echo 'ご指定のIDは存在しません。' . '<br>';
        }else{
            if($password == $_POST['text_password']){
                //IDとパスワードが一致
                echo 'GoMyPage';
                
            }else{
                //パスワードが一致しません。
                echo 'パスワードが一致しません。' . '<br>';
            }
        }
    }
}else{
    echo 'IDまたはパスワードが入力されていません。' . '<br>';
}

$userId = $_POST['text_id'];
$_SESSION['userId']= $userId;