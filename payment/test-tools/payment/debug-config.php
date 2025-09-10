<?php
/**
 * 設定確認用デバッグファイル
 * config.phpの設定が正しく読み込まれているかチェック
 */

require_once 'config.php';
require_once 'SampleCodeConstants.php';

echo "<h2>Authorize.Net 設定確認</h2>";

echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th style='padding: 10px; background: #f0f0f0;'>設定項目</th><th style='padding: 10px; background: #f0f0f0;'>値</th></tr>";

// 環境設定
echo "<tr><td style='padding: 8px;'>環境 (AUTHORIZENET_ENVIRONMENT)</td><td style='padding: 8px; font-weight: bold; color: " . (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION' ? 'red' : 'green') . ";'>" . AUTHORIZENET_ENVIRONMENT . "</td></tr>";

// LOGIN_ID
$loginId = defined('AUTHORIZENET_LOGIN_ID') ? AUTHORIZENET_LOGIN_ID : 'undefined';
$maskedLoginId = strlen($loginId) > 4 ? substr($loginId, 0, 4) . str_repeat('*', strlen($loginId) - 4) : $loginId;
echo "<tr><td style='padding: 8px;'>Login ID</td><td style='padding: 8px;'>" . htmlspecialchars($maskedLoginId) . "</td></tr>";

// TRANSACTION_KEY
$transKey = defined('AUTHORIZENET_TRANSACTION_KEY') ? AUTHORIZENET_TRANSACTION_KEY : 'undefined';
$maskedTransKey = strlen($transKey) > 4 ? substr($transKey, 0, 4) . str_repeat('*', strlen($transKey) - 4) : $transKey;
echo "<tr><td style='padding: 8px;'>Transaction Key</td><td style='padding: 8px;'>" . htmlspecialchars($maskedTransKey) . "</td></tr>";

// CLIENT_KEY
$clientKey = defined('AUTHORIZENET_CLIENT_KEY') ? AUTHORIZENET_CLIENT_KEY : 'undefined';
$maskedClientKey = strlen($clientKey) > 8 ? substr($clientKey, 0, 8) . str_repeat('*', strlen($clientKey) - 8) : $clientKey;
echo "<tr><td style='padding: 8px;'>Client Key</td><td style='padding: 8px;'>" . htmlspecialchars($maskedClientKey) . "</td></tr>";

// SampleCodeConstants 確認
echo "<tr><td style='padding: 8px;'>SampleCodeConstants Login ID</td><td style='padding: 8px;'>" . htmlspecialchars(substr(\SampleCodeConstants::MERCHANT_LOGIN_ID, 0, 4) . str_repeat('*', strlen(\SampleCodeConstants::MERCHANT_LOGIN_ID) - 4)) . "</td></tr>";

echo "</table>";

// Accept.js URL
$acceptJsUrl = (AUTHORIZENET_ENVIRONMENT === 'SANDBOX') ? 'https://jstest.authorize.net/v1/Accept.js' : 'https://js.authorize.net/v1/Accept.js';
echo "<p><strong>Accept.js URL:</strong> " . htmlspecialchars($acceptJsUrl) . "</p>";

// API エンドポイント
$apiEndpoint = (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') ? 'https://api.authorize.net/xml/v1/request.api' : 'https://apitest.authorize.net/xml/v1/request.api';
echo "<p><strong>API エンドポイント:</strong> " . htmlspecialchars($apiEndpoint) . "</p>";

// 警告メッセージ
if (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<strong>⚠️ 注意:</strong> 本番環境 (PRODUCTION) で動作しています。実際の決済が処理されます。";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<strong>✓ テスト環境:</strong> サンドボックス環境で動作しています。テスト決済のみ処理されます。";
    echo "</div>";
}

// 本番環境での推奨事項
if (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') {
    echo "<h3>本番環境での推奨事項</h3>";
    echo "<ul>";
    echo "<li>実際のクレジットカードのみ使用してください</li>";
    echo "<li>テストカード番号（4111111111111111など）は使用できません</li>";
    echo "<li>CVVコードは正確に入力してください</li>";
    echo "<li>有効期限は現在の日付より後の日付を使用してください</li>";
    echo "<li>カード名義人の名前を正確に入力してください</li>";
    echo "</ul>";
}

echo "<p style='margin-top: 30px; font-size: 12px; color: #666;'>このファイルは設定確認後に削除してください。</p>";
?>