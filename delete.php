<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
    h2 {
        color: red;
        background-color: white;
        text-align: center;
    }

    .center-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        /* 横方向の中央 */
        justify-content: center;
        margin-top: 50px;
    }
</style>
<?php
require "db.php";
if (!(isset($_GET['delete_id']))) {
    header('Location: admin.php');
    exit();
}
$sql = $pdo->prepare('delete from user_info where id =?');
$result = $sql->execute([$_GET['delete_id']]);

echo '<div class="center-box">';
if ($result) {
    echo '<h2>ユーザー『' . $_GET['delete_name'] . '』さんを削除しました。</h2>';
} else {
    echo '<h2>ユーザー『' . $_GET['delete_name'] . '』さんの削除に失敗しました。</h2>';
}
?>
<br>
<a href="admin.php" class="btn-next">戻る</a>
</div>