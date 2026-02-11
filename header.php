<?php
// 現在実行されているファイル名を取得（例：mypage1217.php）
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="header-nav">
  <ul>
    <li><a href="mypage.php" class="<?= $current_page == 'mypage.php' ? 'active' : '' ?>">マイページ</a></li>
    <li><a href="kiroku.php" class="<?= $current_page == 'kiroku.php' ? 'active' : '' ?>">記録する</a></li>
    <li><a href="kakunin.php" class="<?= $current_page == 'kakunin.php' ? 'active' : '' ?>">詳細な記録</a></li>
    <li><a href="hensyu.php" class="<?= $current_page == 'hensyu.php' ? 'active' : '' ?>">ユーザー情報編集</a></li>
    <li>
      <form id="form_mypage_logout" action="index.php" method="post" class="nav-logout">
        <button type="submit" name="logout" class="nav-link" value="ログアウト">ログアウト</button>
      </form>
    </li>
  </ul>
</nav>