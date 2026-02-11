<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規ユーザー登録</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        h2 {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

<h1>新規ユーザー登録</h1>

<?php
session_start();

// パスワードが要件を満たしていない場合のエラーメッセージ
if (isset($_SESSION['error'])) {
    echo '<h2>パスワードが要件を満たしていません。</h2>';
    unset($_SESSION['error']);
}

//各情報の入力
echo '<div class="registration-result">';
echo '<form action="input_user.php" method="POST">';

echo '名前：<input type="text" name="name" placeholder="ポリテク太郎" required><br>';

echo 'パスワード：<input type="password" name="password" pattern="^[a-zA-Z0-9]{8,30}$" required><br>';
echo '<p style="font-size: 0.8em; color: #666; margin-top: -10px;">※アルファベットと数字を組み合わせて、8～30文字で作成してください。</p>';

echo '性別：<br>';
echo '<label><input type="radio" name="gender" value="0" required>男性</label>';
echo '<label style="margin-left:15px;"><input type="radio" name="gender" value="1" required>女性</label><br><br>';


echo '今の体重：<input type="number" step="0.1" name="weight" placeholder="60.0" required style="width:100px; display:inline-block; margin-right:5px;">kg<br>';
echo '身長：<input type="number" step="0.1" name="height" placeholder="170.0" required style="width:100px; display:inline-block; margin-right:5px;">cm<br>';
echo '目標体重：<input type="number" step="0.1" name="targetWeight" placeholder="55.0" required style="width:100px; display:inline-block; margin-right:5px;">kg<br><br>';

echo '活動量：<br>';
echo '<label><input type="radio" name="activity" value="1.5" required>少ない</label><br>';
echo '<label><input type="radio" name="activity" value="1.75" required>普通</label><br>';
echo '<label><input type="radio" name="activity" value="2.0" required>多い</label><br><br>';

echo '年齢：<input type="number" name="age" placeholder="30" required style="width:80px; display:inline-block; margin-right:5px;">歳<br>';
echo '目標期間：<input type="date" name="date" min="' . date('Y-m-d') . '" required style="width:auto; display:inline-block;">まで<br><br>';

echo '<div class="link-area">';
echo '<input type="submit" value="登録する" class="btn-next" style="border:none; cursor:pointer; width:100%;">';
echo '</div>';

echo '</form>';
echo '<div class="link-area">';
echo '<a href="index.php" class="btn-next" >ログイン画面に戻る</a>';
echo '</div>';

?>



</body>

</html>
