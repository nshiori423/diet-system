


<?php
$dsn  = 'mysql:host=localhost;dbname=smartdietDB;charset=utf8';
$user = 'YOUR_DB_USER';
$pass = 'YOUR_DB_PASSWORD';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('DB接続エラー');
}
