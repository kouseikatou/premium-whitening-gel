<?php
require_once 'config.php';

echo "<h1>認証情報デバッグ</h1>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>項目</th><th>値</th><th>長さ</th></tr>";

echo "<tr><td>環境</td><td>" . AUTHORIZENET_ENVIRONMENT . "</td><td>-</td></tr>";
echo "<tr><td>LOGIN_ID</td><td>" . AUTHORIZENET_LOGIN_ID . "</td><td>" . strlen(AUTHORIZENET_LOGIN_ID) . "</td></tr>";
echo "<tr><td>TRANSACTION_KEY</td><td>" . substr(AUTHORIZENET_TRANSACTION_KEY, 0, 4) . "****" . substr(AUTHORIZENET_TRANSACTION_KEY, -4) . "</td><td>" . strlen(AUTHORIZENET_TRANSACTION_KEY) . "</td></tr>";
echo "<tr><td>CLIENT_KEY</td><td>" . substr(AUTHORIZENET_CLIENT_KEY, 0, 10) . "****" . substr(AUTHORIZENET_CLIENT_KEY, -10) . "</td><td>" . strlen(AUTHORIZENET_CLIENT_KEY) . "</td></tr>";

echo "</table>";

echo "<h2>CLIENT_KEY詳細</h2>";
echo "<p>完全なCLIENT_KEY: <br><code>" . AUTHORIZENET_CLIENT_KEY . "</code></p>";
echo "<p>文字数: " . strlen(AUTHORIZENET_CLIENT_KEY) . "</p>";

if (strlen(AUTHORIZENET_CLIENT_KEY) !== 80) {
    echo "<p style='color: red;'>⚠️ 警告: CLIENT_KEYの長さが80文字ではありません！</p>";
}

// 正しいCLIENT_KEYの提案
echo "<h2>正しいCLIENT_KEY</h2>";
echo "<p>本番環境用の正しいCLIENT_KEY:</p>";
echo "<code>979TavNpCxM844zhyjcC6GbR4hj83BF3SEmsq8K423EqsJyEWfgSGDaJ8umHXw2D</code>";
echo "<p>文字数: " . strlen('979TavNpCxM844zhyjcC6GbR4hj83BF3SEmsq8K423EqsJyEWfgSGDaJ8umHXw2D') . "</p>";
?>