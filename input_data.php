<?php
include 'header.php';
session_start();
require "db.php";
require_once 'redirect.php';

$today = date("Y-m-d");

$sql = $pdo->prepare(
  'select count(*) from user_data where record_date = ? and id = ?'
);
$sql->execute([$_POST['date'], $_SESSION['userId']]);
$count = $sql->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>記録完了</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .btn-next {
  display: inline-block;
  background: #ff8cbf;      
  color: white !important;
  text-decoration: none;
  padding: 12px 40px;
  border-radius: 30px;
  font-weight: bold;
  transition: 0.3s ease;
  margin: 3px;
}
  </style>
</head>

<body>

  <?php if ($count > 0): ?>

    <?php
    //データ更新用のSQL
    $sql = $pdo->prepare(
      'update user_data 
         set record_date=?, today_weight=?, get_kcal=?, burn_kcal=?, sleep_time=?
         where id = "' . $_SESSION['userId'] . '" 
         and record_date ="' . $_POST['date'] . '"'
    );

    $result = $sql->execute([
      $_POST['date'],
      $_POST['weight'],
      $_POST['get_kcal'],
      $_POST['burn_kcal'],
      $_POST['sleep']
    ]);
    ?>

    <!-- 既存データの更新の場合 -->
    <?php if ($result): ?>

      <h2 class="complete-title">更新が完了しました！</h2>

      <?php
      // 更新日が今日の場合は、ユーザー情報の体重を上書きする
      if ($today == $_POST['date']) {
        $sql = $pdo->prepare('update user_info set weight = ? where id = ?');
        $sql->execute([$_POST['weight'], $_SESSION['userId']]);
      }
      ?>

      <section class="registration-result">
        <table>
          <tr>
            <th>日付</th>
            <td><?= $_POST['date'] ?></td>
          </tr>
          <tr>
            <th>今日の体重</th>
            <td><?= $_POST['weight'] ?> kg</td>
          </tr>
          <tr>
            <th>摂取カロリー</th>
            <td><?= $_POST['get_kcal'] ?> kcal</td>
          </tr>
          <tr>
            <th>消費カロリー</th>
            <td><?= $_POST['burn_kcal'] ?> kcal</td>
          </tr>
          <tr>
            <th>睡眠時間</th>
            <td><?= $_POST['sleep'] ?> 時間</td>
          </tr>
        </table>

        <div class="link-area">
          <a href="mypage.php" class="btn-next">マイページへ戻る</a><br>
          <a href="kiroku.php" class="btn-next">続けて入力する</a>
        </div>
      </section>

    <?php else: ?>

      <p class="error-message">更新に失敗しました。</p>
      <div class="link-area">
        <a href="kiroku.php" class="btn-next">登録画面へ戻る</a>
      </div>

    <?php endif; ?>


  <?php else: ?>
    <!-- 新規データの登録の場合 -->
    <?php
    $sql = $pdo->prepare(
      'insert into user_data values(' . $_SESSION['userId'] . ',?,?,?,?,?)'
    );
    $result = $sql->execute([
      $_POST['date'],
      $_POST['weight'],
      $_POST['get_kcal'],
      $_POST['burn_kcal'],
      $_POST['sleep']
    ]);
    ?>

    <?php if ($result): ?>

      <h2 class="complete-title">登録が完了しました！</h2>

      <!-- データ登録日が今日の場合は、ユーザー情報の体重を上書きする -->
      <?php
      if ($today == $_POST['date']) {
        $sql = $pdo->prepare('update user_info set weight = ? where id = ?');
        $sql->execute([$_POST['weight'], $_SESSION['userId']]);
      }
      ?>

      <section class="registration-result">
        <table>
          <tr>
            <th>日付</th>
            <td><?= $_POST['date'] ?></td>
          </tr>
          <tr>
            <th>今日の体重</th>
            <td><?= $_POST['weight'] ?> kg</td>
          </tr>
          <tr>
            <th>摂取カロリー</th>
            <td><?= $_POST['get_kcal'] ?> kcal</td>
          </tr>
          <tr>
            <th>消費カロリー</th>
            <td><?= $_POST['burn_kcal'] ?> kcal</td>
          </tr>
          <tr>
            <th>睡眠時間</th>
            <td><?= $_POST['sleep'] ?> 時間</td>
          </tr>
        </table>

        <div class="link-area">
          <a href="mypage.php" class="btn-next">マイページへ戻る</a><br>
          <a href="kiroku.php" class="btn-next">続けて入力する</a>
        </div>
      </section>

    <?php else: ?>

      <p class="error-message">登録に失敗しました。</p>
      <div class="link-area">
        <a href="kiroku.php" class="btn-next">登録画面へ戻る</a>
      </div>

    <?php endif; ?>

  <?php endif; ?>

</body>

</html>