<!-- ログインしていないユーザーをログイン画面（index.php）に遷移させる -->
<?php
if (empty($_SESSION['userId'])) {
  header('Location: index.php');
  exit();
}