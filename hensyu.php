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

    <style>
        h2 {
            color: red;
            text-align: center;
        }
        p{
            font-size: small;
        }
    </style>

</head>

<body>
    <?php include 'header.php'; ?>
    <?php
    session_start();
    require_once 'redirect.php';
    require "db.php";

    ?>
    <h1>ユーザー情報編集</h1>

    <?php
    //パスワード確認
    if (isset($_SESSION['pass_error'])) {
        echo '<h2>現在のパスワードが間違っています。</h2>';
        $_SESSION['pass_error'] = null;
    }

    if (isset($_SESSION['error'])) {
        echo '<h2>変更されるパスワードが要件を満たしていません。</h2>';
        $_SESSION['error'] = null;
    }

    if (isset($_SESSION['same_pass'])) {
        echo '<h2>パスワードが変更されていません。</h2>';
        $_SESSION['same_pass'] = null;
    }

    $sql = $pdo->query('select * from user_info where id = ' . $_SESSION['userId'] . '');
    $row = $sql->fetch(PDO::FETCH_ASSOC);

    ?>

    <div class="profile-box" style="max-width: 500px; margin: 0 auto;">

        <form action="update_user.php" method="POST">

            <label>
                名前
                <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
            </label>

            <label>
                目標体重（kg）
                <input type="number" name="weight" step="0.1" value="<?= htmlspecialchars($row['target_weight']) ?>">
            </label>

            <label>
                パスワード（変更する場合のみ）
                <input type="password" name="password" pattern="^[a-zA-Z0-9]+$" placeholder="アルファベットと数字を組み合わせて、8～30文字で作成してください">
            </label>

            <label>
                現在のパスワード（入力必須）
                <input type="password" name="check_pass" pattern="^[a-zA-Z0-9]+$" required>
            </label>

            <div class="link-area">
                <input type="submit" value="更新する" class="btn-next" style="border:none; cursor:pointer;">
            </div>

        </form>
    </div>

</body>

</html>