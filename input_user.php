<?php
session_start();
require "db.php";

/*
  新規登録 完了ページ（処理＋表示）
*/

// すでにログインしている人は一旦ログアウト
if (isset($_SESSION['userId'])) {
    $_SESSION['userId'] = null;
}

// 直アクセス対策（POSTが無い場合）
if (empty($_POST['name'])) {
    header('Location: touroku.php');
    exit();
}

// パスワード要件チェック
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,30}$/', $_POST['password'])) {
    $_SESSION['error'] = true;
    header('Location: touroku.php');
    exit();
}

// 登録処理
$sql = $pdo->prepare('insert into user_info values(null,?,?,?,?,?,?,?,?,?,?)');

$result = $sql->execute([
    $_POST['name'],
    $_POST['password'],
    $_POST['gender'],
    $_POST['weight'],
    $_POST['height'],
    $_POST['targetWeight'],
    $_POST['activity'],
    $_POST['age'],
    $_POST['date'],
    date('Y-m-d')
]);

if ($result) {
    $userId = $pdo->lastInsertId();
    $_SESSION['userId'] = $userId;
}

$_SESSION['error'] = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php if ($result): ?>

<h2 class="complete-title">下記の内容で登録が完了しました！</h2>

<div class="registration-result">
<table>

<tr>
    <th>ユーザーID</th>
    <td>
        <span class="id-highlight"><?= sprintf('%04d', $userId) ?></span><br>
        <div class="id-note">※次回以降のログインで利用します。</div>
    </td>
</tr>

<tr>
    <th>名前</th>
    <td><?= htmlspecialchars($_POST['name']) ?> 様</td>
</tr>

<tr>
    <th>パスワード</th>
    <td>（セキュリティのため非表示）</td>
</tr>

<tr>
    <th>性別</th>
    <td><?= $_POST['gender'] == 0 ? '男性' : '女性' ?></td>
</tr>

<tr>
    <th>体重</th>
    <td><?= htmlspecialchars($_POST['weight']) ?> kg</td>
</tr>

<tr>
    <th>身長</th>
    <td><?= htmlspecialchars($_POST['height']) ?> cm</td>
</tr>

<tr>
    <th>目標体重</th>
    <td><?= htmlspecialchars($_POST['targetWeight']) ?> kg</td>
</tr>

<tr>
    <th>活動量</th>
    <td>
        <?php
        if ($_POST['activity'] == 1.5) {
            echo '少ない';
        } elseif ($_POST['activity'] == 1.75) {
            echo '普通';
        } elseif ($_POST['activity'] == 2) {
            echo '多い';
        }
        ?>
    </td>
</tr>

<tr>
    <th>年齢</th>
    <td><?= htmlspecialchars($_POST['age']) ?> 歳</td>
</tr>

<tr>
    <th>目標期間</th>
    <td><?= htmlspecialchars($_POST['date']) ?> まで</td>
</tr>

</table>

<div class="link-area">
    <a href="mypage.php" class="btn-next">マイページへ進む</a>
</div>

</div>

<?php else: ?>

<div class="error-message">
登録に失敗しました。<br>
<a href="touroku.php">登録画面へ戻る</a>
</div>

<?php endif; ?>

</body>
</html>
