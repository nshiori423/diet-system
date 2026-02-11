<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    h2 {
        text-align: center;
    }

    h3 {
        text-align: center;
    }
</style>

<body>
    <?php
     session_start();
    include 'header.php';
    require "db.php";

    $sql = $pdo->prepare('select password from user_info where id = ?');
    $sql->execute([$_SESSION['userId']]);
    $data = $sql->fetch(PDO::FETCH_ASSOC);
    if (!$data) {
    header('Location: index.php');
    exit();
}

    //現在のパスワードが正しいか確認
if (!empty($_POST['check_pass'])) {
    if (!password_verify($_POST['check_pass'], $data['password'])) {


            $_SESSION['pass_error'] = "true";
            header('Location: hensyu.php'); //修正必須
            exit();
        }
    }

    //変更するパスワードが要件を満たしているか確認
    if (isset($_POST['password']) && !empty($_POST['password']) && !(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/', $_POST['password']))) {
        $_SESSION['error'] = "true";
        header('Location: hensyu.php');  //修正必須
        exit();
    }

    if (!empty($_POST['password']) && password_verify($_POST['password'], $data['password'])) {
        $_SESSION['same_pass'] = "true";
         header('Location: hensyu.php'); //修正必須
        exit();
    }

    //名前の更新
    if (isset($_POST['name'])) {
        $sql = $pdo->prepare('update user_info set name=? where id = ?');
        $sql->execute(
            [
                $_POST['name'],
                $_SESSION['userId']
            ]
        );
    }

    //パスワードの更新
    if (isset($_POST['password']) && !(empty($_POST['password']))) {
     $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
     $sql = $pdo->prepare('update user_info set password=? where id = ?');
     $sql->execute(
        [
        $hash,
        $_SESSION['userId']
    ]
);

    }

    //体重の更新
    if (isset($_POST['weight'])) {
        $sql = $pdo->prepare('update user_info set target_weight=? where id = ?');
        $sql->execute(
            [
                $_POST['weight'],
                $_SESSION['userId']
            ]
        );
    }

    echo '<h2>更新が完了しました！</h2><br>';
    ?>
    <section class="menu" style="text-align:center; margin-top:30px;">
        <a href="mypage.php" class="btn-next">マイページへ戻る</a>
    </section>

</body>