<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        jQuery(function($) {
            //変数宣言
            let $view = $('#view');
            let $text_id = $('input[name="text_id"]');
            let $text_password = $('input[name="text_password"]');

            //フォームを送信する際の処理 
            $('#form_login_check').submit(function(event) {
                event.preventDefault();
                $.post('login_check.php', {
                    text_id: $text_id.val(),
                    text_password: $text_password.val()
                }, function(data) {
                    $text_id.val(''); //ID欄の中を消去
                    $text_password.val(''); //パスワード欄の中を消去
                    if (data === 'GoMyPage') {
                        // ログイン成功ならマイページへ
                        window.location.href = 'mypage.php';
                    } else if (data === 'GoAdPage') {
                        // 管理者ページへ
                        window.location.href = 'admin.php';
                    } else {
                        // 失敗ならエラーメッセージを画面に表示
                        $view.html('<p class="error">' + data + '</p>');
                    }
                });
            });

        });
    </script>

    <title>Document</title>

</head>

<body class="login-page">


    <div class="profile-box login-box">

        <h3 style="text-align: center;">マイページへログイン</h3>

        <form id="form_login_check">
            <p>ID：<input type="text" name="text_id" value="" size="10" pattern="^[0-9A-Za-z]+$" placeholder="半角で入力してください" /></p>

            <p>パスワード：<input type="password" name="text_password" value="" size="10" /></p>

            <button type="submit" name="login" class="btn-next">ログイン</button>
        </form>


        <div id="view" class="error-area"></div>

        <hr style="border: 0; border-top: 1px solid rgba(73, 48, 36, 0.1); margin: 20px 0;">

        <form id="form_new_acount" action="touroku.php" method="post">
            <button type="submit" name="new_acount" class="btn-next">新規ユーザ登録</button>
        </form>
    </div>
    

    <?php

    //ログアウトで
    if (!empty($_POST['logout'])) {
        session_start();
        //echo $_SESSION['userId'] . '<br>';
        session_destroy();
        //echo $_SESSION['userId'] . '<br>';
    }
    ?>
</body>

</html>