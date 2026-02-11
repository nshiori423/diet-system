<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>記録／編集</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php
    session_start();
    require "db.php";
    require_once 'redirect.php';

    $sql = "
  SELECT registration_date 
  FROM user_info 
  WHERE id = ?
";

    //ログイン中のユーザーの登録日を取得
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['userId']]);
    $day = $stmt->fetch(PDO::FETCH_ASSOC);

    // 消費カロリー早見表
    $sql_consumed = "
  SELECT
    `行動`,
    `1回あたりの時間(分)`,
    `45㎏`,
    `50㎏`,
    `60㎏`,
    `70㎏`,
    `80㎏`
  FROM consumed_kcal
";
    $consumed_list = $pdo->query($sql_consumed)->fetchAll(PDO::FETCH_ASSOC);

    // 摂取カロリー早見表
    $sql_intake = "
  SELECT
    `メニュー`,
    `kcal`
  FROM intake_kcal
";
    $intake_list = $pdo->query($sql_intake)->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <h1>📝記録／編集</h1>

    <div class="kakunin-container">

        <!-- 左：消費カロリー -->
        <div class="accordion-box">
            <button type="button" class="accordion-title">🔽🔽🔽消費カロリー早見表🔽🔽🔽</button>
            <div class="accordion-content">
                <table class="consumed-table">
                    <tr>
                        <th>行動</th>
                        <th>時間(分)</th>
                        <th>45kg</th>
                        <th>50kg</th>
                        <th>60kg</th>
                        <th>70kg</th>
                        <th>80kg</th>
                    </tr>
                    <?php foreach ($consumed_list as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['行動'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['1回あたりの時間(分)'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['45㎏'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['50㎏'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['60㎏'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['70㎏'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['80㎏'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- 中央：記録フォーム -->
        <div class="profile-box" style="max-width: 500px;">

            <form action="input_data.php" method="POST">

                <label>
                    日付を選択
                    <input
                        type="date"
                        name="date"
                        max="<?= date('Y-m-d') ?>" //選択可能な最大値を当日に設定
                        min="<?= $day['registration_date'] ?>" //選択可能な最小値を登録日に設定
                        <?php
                        //■記録データ編集用
                        if (isset($_GET['edit_date'])) {
                            $dateTime = new DateTime($_GET['edit_date']);
                            $formattedDate = $dateTime->format('Y-m-d');
                            echo "value=" . $formattedDate;
                        }
                        ?>
                        required>
                </label>

                <label>
                    今日の体重（kg）
                    <input
                        type="number"
                        name="weight"
                        step="0.1"
                        <?php
                        //■記録データ編集用
                        if (isset($_GET['edit_weight'])) {
                            echo "value=" . (float)$_GET['edit_weight'];
                        }
                        ?>
                        required>
                </label>

                <label>
                    摂取カロリー（kcal）
                    <input
                        type="number"
                        name="get_kcal"
                        <?php
                        //■記録データ編集用
                        if (isset($_GET['edit_get_kcal'])) {
                            echo "value=" . (float)$_GET['edit_get_kcal'];
                        }
                        ?>
                        required>
                </label>

                <label>
                    消費カロリー（kcal）
                    <input
                        type="number"
                        name="burn_kcal"
                        <?php
                        //■記録データ編集用
                        if (isset($_GET['edit_burn_kcal'])) {
                            echo "value=" . (float)$_GET['edit_burn_kcal'];
                        }
                        ?>
                        required>
                </label>

                <label>
                    睡眠時間（時間）
                    <input
                        type="number"
                        name="sleep"
                        step="0.1"
                        <?php
                        //■記録データ編集用
                        if (isset($_GET['edit_sleep_time'])) {
                            echo "value=" . (float)$_GET['edit_sleep_time'];
                        }
                        ?>
                        required>
                </label>

                <div class="link-area">
                    <input
                        type="submit"
                        value="登録 / 更新"
                        class="btn-next"
                        style="border:none; cursor:pointer;">
                </div>

            </form>
        </div>

        <!-- 右：摂取カロリー -->
        <div class="accordion-box">
            <button type="button" class="accordion-title">👉👉👉摂取カロリー早見表👈👈👈</button>
            <div class="accordion-content">
                <table>
                    <tr>
                        <th>メニュー</th>
                        <th>kcal</th>
                    </tr>
                    <?php foreach ($intake_list as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['メニュー'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['kcal'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.accordion-title').forEach(btn => {
                btn.addEventListener('click', () => {
                    const content = btn.nextElementSibling;
                    content.style.display =
                        content.style.display === 'block' ? 'none' : 'block';
                });
            });
        });
    </script>

</body>

</html>