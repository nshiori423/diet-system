<?php
session_start();
require_once 'db.php';
require_once 'redirect.php';

$user_id = $_SESSION['userId'];

//user_info 取得 
$sql_user = "SELECT * FROM user_info WHERE id = ?";
$stmt = $pdo->prepare($sql_user);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//今日の記録取得 
$sql_today = "
  SELECT * 
  FROM user_data 
  WHERE id = ?
  ORDER BY record_date DESC
  LIMIT 1
";
$stmt = $pdo->prepare($sql_today); //SQL文を準備する
$stmt->execute([$user_id]); //SQLを実行する
$today = $stmt->fetch(PDO::FETCH_ASSOC); //結果から1行だけ取り出すPHPで使える形

// 変数 
$name          = $user['name'];
$id            = $user['id'];
$height        = $user['height'];
$age           = $user['age'];
$gender        = $user['gender']; // 0 or 1
$activity      = $user['activity'];
$target_weight = $user['target_weight'];
////>■
//開始日と登録日からダイエット期間を計算
$registration_weight = $user['weight'];
$target_date = $user['target_date'];
$registration_date = $user['registration_date'];
//echo "開始日 " . $registration_date . '<br>';
//echo "目標日 " . $target_date . '<br>';
$start_date = new DateTime($registration_date);
$end_date   = new DateTime($target_date);
$interval = $start_date->diff($end_date);
$interval_days = $interval->days + 1;
//echo "日数 " . $diet_days . '<br>';
////<■

// 今日の体重（未入力時は user_info の体重） 
$today_weight = $today ? $today['today_weight'] : $user['weight'];

//基礎代謝
if ($gender == 0) { // 男性
  $bmr = 66 + (13.7 * $today_weight) + (5.0 * $height) - (6.8 * $age);
} else { // 女性
  $bmr = 655 + (9.6 * $today_weight) + (1.8 * $height) - (4.7 * $age);
}

$bmr = round($bmr);

// 活動量を表示用文字列に変換
if ($activity == 1.5) {
  $activity_text = '少ない';
} elseif ($activity == 1.75) {
  $activity_text = '普通';
} elseif ($activity == 2) {
  $activity_text = '多い';
} else {
  $activity_text = '未設定';
}

//進捗の計算
$diff_weight = round($today_weight - $target_weight, 1);

// 1〜13の中からランダムに1つ選ぶ
$num = rand(1, 13);
// column + 数字 + .png
$imgPath = "img/columnimg/column" . $num . ".png";

// ===== グラフ用データ取得 =====
$sql_graph = "
  SELECT 
    record_date,
    today_weight,
    get_kcal,
    burn_kcal,
    sleep_time
  FROM user_data
  WHERE id = ?
  ORDER BY record_date ASC
";

$stmt = $pdo->prepare($sql_graph);
$stmt->execute([$user_id]);
$graphData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JS用にJSON変換
$graph_json = json_encode($graphData, JSON_UNESCAPED_UNICODE);

////>■
//ダイエット日数,総接種カロリー,総消費カロリー
$sql_sum_kcal = "
  SELECT 
    count(id),
    sum(get_kcal),
    sum(burn_kcal)
  FROM user_data
  WHERE id = ?
";
$stmt = $pdo->prepare($sql_sum_kcal); //SQL文を準備する
$stmt->execute([$user_id]); //SQLを実行する
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
  //echo "日数: " . $row['count(id)'];
  $diet_days = $row['count(id)'];
  //echo "総摂取カロリー: " . $row['sum(get_kcal)'];
  $sum_get_kcal = $row['sum(get_kcal)'];
  //echo "総消費カロリー: " . $row['sum(burn_kcal)'];
  $sum_burn_kcal = $row['sum(burn_kcal)'];
}
////本日(最終)の摂取カロリー,消費カロリー
$sql_latest_kcal = "
  SELECT 
    get_kcal,
    burn_kcal
  FROM user_data
  WHERE id = ?
  ORDER BY record_date DESC LIMIT 1;
";
$stmt = $pdo->prepare($sql_latest_kcal); //SQL文を準備する
$stmt->execute([$user_id]); //SQLを実行する
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
  ////echo "摂取カロリー: " . $row['get_kcal'];
  $latest_get_kcal = $row['get_kcal'];
  ////echo "消費カロリー: " . $row['burn_kcal'];
  $latest_burn_kcal = $row['burn_kcal'];
}

////進捗率計算
//落としたい体重
$lose_weight = $registration_weight - $target_weight;
//echo "登録体重: " . $registration_weight . '<br>';
//echo "目標体重: " . $target_weight . '<br>';
//目標の総消費カロリー
$target_sum_kcal = $lose_weight * 7200;
////echo "目標の総消費カロリー: " . $target_sum_kcal . '<br>';
//1日当たりに消費すべきカロリー
if ($interval_days < 1) {
  $interval_days = 1;
}
$burn_kcal_per_day = (int)($target_sum_kcal / $interval_days);  //キャストして整数型に変更
////echo "ダイエット日数: " . $diet_days . '<br>';
//本日減らしたカロリー
if (!$row) {
  $today_burn_kcal = 0;
} else {
  $today_burn_kcal = $latest_burn_kcal + $bmr - $latest_get_kcal;
}

//本日までの総消費カロリー
////echo "本日までの総消費カロリー(基礎代謝含まない): " . $sum_burn_kcal . '<br>';
$sum_burn_kcal += $bmr * $diet_days - $sum_get_kcal;
////echo "基礎代謝 x ダイエット日数): " . $bmr * $diet_days . '<br>';
////echo "本日までの総接種カロリー: " . $sum_get_kcal . '<br>';
////全体進捗率[%]////
$progress_rate = round($sum_burn_kcal / $target_sum_kcal * 100, 2);
//echo "進捗率[%]: " . $progress_rate . '<br>';
//echo "本日までの総消費カロリー(基礎代謝含む): " . $sum_burn_kcal . '<br>';
//echo "目標の総消費カロリー: " . $target_sum_kcal . '<br>';
////1日の状態////
//echo "本日減らしたカロリー: " . $today_burn_kcal . '<br>';
//echo "本日の消費カロリー: " . $latest_burn_kcal . '<br>';
//echo "基礎代謝: " . $bmr . '<br>';
//echo "本日の摂取カロリー: " . $latest_get_kcal . '<br>';
//echo "目標の消費カロリー(1日当たり): " . $burn_kcal_per_day . '<br>';
////<■
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>マイページ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php include 'header.php'; ?>

  <h1>🔥SmartDiet Mypage🔥</h1>

  <!-- 過去のデータ未入力があった際に、お知らせを表示する -->
  <?php
  $sql_day = $pdo->prepare(
    'SELECT registration_date FROM user_info WHERE id = ?');
    $sql_day->execute([$_SESSION['userId']]);
    $row = $sql_day->fetch(PDO::FETCH_ASSOC);
    $date1 = strtotime(date('Y-m-d'));
    $date2 = strtotime($row['registration_date']);

  // 秒数を86400で割ると日数に変換
  // 86400 = (60 * 60 * 24)
$sql_count = $pdo->prepare(
    'SELECT count(*) FROM user_data WHERE id = ?'
);
$sql_count->execute([$_SESSION['userId']]);
$row = $sql_count->fetch(PDO::FETCH_ASSOC);
$count = $row['count(*)'];


  if ($diff - 1 > $count):
  ?>
    <div class="notice-box">
      <a href="kakunin.php">🔔 データが入力されていない日があります✅</a>

    </div>
  <?php endif; ?>
  <!-- ここまで -->

  <div style="text-align: center;">
    <section class="profile-box" style="display:inline-block;">
      <div class="profile-header">
        <div class="left">

          <p><?= htmlspecialchars($name) ?> さん、ようこそ</p>
          <p>⚖️今の体重：<?= $today_weight ?> kg</p>
          <p>🏋️‍♂️活動量：「<?= htmlspecialchars($activity_text) ?>」</p>

        </div>

        <div class="right">
          <p>🆔：<?= $id ?></p>
          <p>目標体重：<?= $target_weight ?> kg📉</p>
          <p>基礎代謝：<?= $bmr ?> kcal🔥</p>
        </div>
      </div>

      <?php
      ////進捗率計算
      //落としたい体重
      //$lose_weight = ;
      //総消費カロリー
      //$sum_kcal = ;
      //1日当たりに消費すべきカロリー
      //$kcal_per_day = ;
      //

      //目標までの体重がマイナスになったら目標達成と表示
      if ($diff_weight <= 0) {
        $diff_weight = '🏆💪目標達成🎉✨';
      } else {
        $diff_weight .= 'kg';
      }
      ?>

      <div class="progress">
        <p>
          <?= htmlspecialchars($name) ?>さんが <?= $interval_days ?> 日で <?= $lose_weight ?> kg落とす為に<br>
          消費しなければいけないカロリー<span style="color: #fff; font-weight: bold; text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #ff0000ff, 0 0 20px #ff0000ff;  padding: 2px 10px; border-radius: 10px; margin: 0 5px;"><?= $target_sum_kcal ?></span>kcal

        </p>

        <hr style="border: 0; border-top: 1px dashed #493024; margin: 15px 0;">

        <p>1日当たり <span style="font-size: 1.1em; color: #d9534f; font-weight: bold;"><?= $burn_kcal_per_day ?></span> kcal消費しましょう💪</p>

        <hr style="border: 0; border-top: 1px dashed #493024; margin: 15px 0;">

        <div style="line-height: 1.6; margin-bottom: 20px;">
          本日の消費したカロリー：<span style="font-weight: bold;"><?= $today_burn_kcal ?></span> kcal<br>
          <?= htmlspecialchars($name) ?>さんの基礎代謝と合わせて、<span style="font-weight: bold; color: #2e7d32;"><?= $today_burn_kcal + $bmr ?></span> kcal消費しました🔥<br>
          総消費カロリー：<span style="font-weight: bold;"><?= $sum_burn_kcal ?></span> kcal
        </div>

        <div style="font-size: 1.2em; margin-bottom: 15px;">
          進捗率：<span style="font-size: 1.4em; color: #ff8cbf; font-weight: bold;"><?= $progress_rate ?> %</span>
          <br>
          残り<span style="font-size: 1.4em; color: #fff; font-weight: bold; text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #ff0000ff, 0 0 20px #ff0000ff;  padding: 2px 10px; border-radius: 10px; margin: 0 5px;"><?= $target_sum_kcal - $sum_burn_kcal ?></span> kcalの消費です💪
        </div>
        <p>
          目標体重まであと：
          <span class="remain"><?= $diff_weight ?></span>
          <span class="img-container">
            <img class="column-img" src="<?= $imgPath ?>" alt="ランダム画像">
          </span>
        </p>
      </div>

    </section>
  </div>

  <section class="graph">


    <div class="graph-row">
      <div class="graph-box">
        <canvas id="weightChart"></canvas>
      </div>

      <div class="graph-box">
        <canvas id="calorieChart"></canvas>
      </div>
      <div class="graph-box sleep-box">
        <canvas id="sleepChart"></canvas>
      </div>
    </div>

  </section>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const graphData = <?= $graph_json ?>;
    const bmr = <?= $bmr ?>;

    // 共通：日付
    const labels = graphData.map(d => d.record_date);
    const COLOR = {
      weight: '#ff8cbf', // メイン（体重・進捗）
      target: '#c2fac4', // 目標ライン
      intake: '#f39c12', // 摂取カロリー
      burn: '#2ecc71', // 消費カロリー
      sleep: '#6fa8dc', // 睡眠
      text: '#493024' // 文字色
    };

    // ===== 1. 体重グラフ =====
    new Chart(document.getElementById('weightChart'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
            label: '体重 (kg)',
            data: graphData.map(d => d.today_weight),
            borderColor: COLOR.weight,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 7,
            pointHoverBorderWidth: 3,
            pointHitRadius: 10,

            fill: false
          },
          {
            label: '目標体重',
            data: Array(labels.length).fill(<?= $target_weight ?>),
            borderColor: 'rgba(73, 48, 36, 0.8)',
            borderDash: [8, 6],
            borderWidth: 3,
            pointRadius: 0,
            order: 10,
            fill: false
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });


    // ===== 2. 摂取 / 消費カロリー =====
    new Chart(document.getElementById('calorieChart'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
            label: '摂取カロリー',
            data: graphData.map(d => d.get_kcal),
            borderColor: COLOR.intake,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 7,
            pointHoverBorderWidth: 3,
            pointHitRadius: 10,
            fill: false
          },
          {
            label: '消費カロリー',
            data: graphData.map(d => Number(d.burn_kcal) + bmr),
            borderColor: COLOR.burn,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 7,
            pointHoverBorderWidth: 3,
            pointHitRadius: 10,
            fill: false
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });

    // ===== 3. 睡眠時間 =====
    new Chart(document.getElementById('sleepChart'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: '睡眠時間 (時間)',
          data: graphData.map(d => d.sleep_time),
          borderColor: COLOR.sleep,
          borderWidth: 2,
          tension: 0.25,
          pointRadius: 3,
          pointHoverRadius: 7,
          pointHoverBorderWidth: 3,
          pointHitRadius: 10,
          fill: false
        }]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });
  </script>

</body>

</html>