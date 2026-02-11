<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者画面</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #c2fac4;
            color: #493024;
        }

        tr {
            background: #f9f9f9;
        }

        div {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* 横方向の中央 */
            justify-content: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <?php
    // 現在実行されているファイル名を取得（例：mypage1217.php）
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="header-nav">
        <ul>
            <li><a href="admin.php" class="<?= $current_page == 'admin.php' ? 'active' : '' ?>">登録ユーザーリスト</a></li>
            <li>
                <form id="form_mypage_logout" action="index.php" method="post" class="nav-logout">
                    <button type="submit" name="logout" class="nav-link" value="ログアウト">ログアウト</button>
                </form>
            </li>
        </ul>
    </nav>

    <?php
    session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: index.php');
        exit();
    }
    require "db.php";
    $sql = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_info');
    $sql->execute();
    $count = $sql->fetch(PDO::FETCH_ASSOC);
    ?>

    <div>
        <h1>登録ユーザーリスト</h1>
        <h2>現在の登録者数：<?= $count['cnt'] ?>人</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>操作</th>
            </tr>

            <?php
            $sql = $pdo->prepare('select id,name from user_info');
            $sql->execute();
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $row) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td><a class="btn-next" href="delete.php?delete_id=' . $row['id'] . '&delete_name=' . $row['name'] . '">削除</a></td>';
                echo '</tr>';
            }
            ?>

        </table>
    </div>

</body>

</html>