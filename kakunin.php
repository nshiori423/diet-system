<?php
session_start();
require_once 'db.php';
require_once 'redirect.php';

$user_id = $_SESSION['userId'];

// --- 1. 目標体重を取得 (マイページと同じロジック) ---
$sql_user = "SELECT target_weight, weight FROM user_info WHERE id = ?";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

$target_weight = $user['target_weight'];

// --- 2. 今日の(最新の)体重を取得 (マイページと同じロジック) ---
$sql_today = "
  SELECT today_weight 
  FROM user_data 
  WHERE id = ?
  ORDER BY record_date DESC
  LIMIT 1
";
$stmt_today = $pdo->prepare($sql_today);
$stmt_today->execute([$user_id]);
$today = $stmt_today->fetch(PDO::FETCH_ASSOC);

// 今日の体重（未入力時は user_info の初期体重）
$today_weight = $today ? $today['today_weight'] : $user['weight'];

// --- 3. 進捗の計算 (マイページと同じ計算式) ---
$diff_weight = round($today_weight - $target_weight, 1);

// --- 4. 一覧表示用の全データ取得 ---
$sql_all = "SELECT record_date, today_weight, get_kcal, burn_kcal, sleep_time
            FROM user_data
            WHERE id = ?
            ORDER BY record_date ASC";
$stmt_all = $pdo->prepare($sql_all);
$stmt_all->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>今までの詳細記録確認</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    
</head>

<body>

    <?php
    include 'header.php';

    if ($diff_weight <= 0) {
        $diff_weight = '🏆💪目標達成🎉✨';
    } else {
        $diff_weight .= 'kg🔥';
    }

    ?>

    <h1>現在までの記録一覧</h1>

    <div class="progress">
        <p>
            目標体重まであと：
            <span class="remain"><?= $diff_weight ?> </span>

        </p>
    </div>

    <!-- データが未入力の日をお知らせ -->
    <?php
    //中尾UI変更点　echoを一時的に格納しています。
    ob_start();
    $sql_date = "SELECT registration_date FROM user_info WHERE id = ?";
    $stmt_date = $pdo->prepare($sql_date);
    $stmt_date->execute([$user_id]);
    $row = $stmt_date->fetch(PDO::FETCH_ASSOC);
    $start_date = strtotime($row['registration_date']);
    $today = strtotime(date("Y-m-d"));
    $diff = ($today - $start_date) / 86400 + 1;

    $sql_search = $pdo->prepare('select * from user_data where id=? AND record_date = ?');

    for ($i = 0; $i < $diff; $i++) {
        $search_date = new DateTime($row['registration_date']);
        $search_date->modify("+{$i} day");
        $next_date = $search_date->format('Y-m-d') . '<br>';

        $result = $sql_search->execute([$user_id, $next_date]);
        $data = $sql_search->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            //echo $search_date->format('Y-m-d') . 'は入力済みです。<br>';
        } else {
            echo $search_date->format('Y-m-d') . '❎<br>';
        }
    }

    $notice_html = ob_get_clean();

    // 未入力が1日でもあったら通知表示
    if (!empty(trim($notice_html))):
    ?>
        <div class="notice-box">
            <p style="font-weight: bold; margin-bottom: 8px;">
                データが未入力の日があります
            </p>

            <?= $notice_html ?>

            <p style="margin-top: 8px;">
                <a href="kiroku.php">👉 記録ページで入力してください</a>
            </p>
        </div>
    <?php endif; ?>
    <!-- ここまで -->

    <div class="table-area">
        <table>
                <tr>
                    <th>日付</th>
                    <th>体重 (kg)</th>
                    <th>摂取カロリー (kcal)</th>
                    <th>消費カロリー (kcal)</th>
                    <th>睡眠時間 (h)</th>
                    <th>編集</th> <!-- ■記録データ編集用 -->
                </tr>

                <?php foreach ($stmt_all as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['record_date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['today_weight'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['get_kcal'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['burn_kcal'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['sleep_time'], ENT_QUOTES, 'UTF-8') ?></td>
                        <!-- ■>>記録データ編集用 -->
                        <td><a class="btn-next" href="kiroku.php
                            ?edit_date=<?php echo $row['record_date']; ?>
                            &edit_weight=<?php echo $row['today_weight']; ?>
                            &edit_get_kcal=<?php echo $row['get_kcal']; ?>
                            &edit_burn_kcal=<?php echo $row['burn_kcal']; ?>
                            &edit_sleep_time=<?php echo $row['sleep_time']; ?>
                            ">編集</a></td>
                        <!-- ■<<記録データ編集用 -->
                    </tr>

                <?php endforeach; ?>
        </table>
    </div>


</body>

</html>