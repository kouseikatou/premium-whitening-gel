<?php
/**
 * クイックテストツール
 * 
 * エラーを素早く確認するためのシンプルなテストツール
 */

require_once '../../config.php';

// エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='ja'>";
echo "<head><meta charset='UTF-8'><title>クイックテスト</title></head>";
echo "<body style='font-family: monospace; padding: 20px; background: #f5f5f5;'>";

echo "<h1>🔍 クイックテスト結果</h1>";

// 1. 設定確認
echo "<h2>1. 設定確認</h2>";
echo "<p><strong>GOOGLE_SPREADSHEET_ID:</strong> " . GOOGLE_SPREADSHEET_ID . "</p>";
echo "<p><strong>GOOGLE_APPS_SCRIPT_URL:</strong> " . GOOGLE_APPS_SCRIPT_URL . "</p>";
echo "<p><strong>GOOGLE_SHEETS_API_KEY:</strong> " . substr(GOOGLE_SHEETS_API_KEY, 0, 20) . "...</p>";

// 2. Apps Script GET テスト
echo "<h2>2. Apps Script GET テスト</h2>";
echo "<p>URLにアクセスしています...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, GOOGLE_APPS_SCRIPT_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Test Tool)');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";
echo "<p><strong>Error:</strong> " . ($error ? $error : 'なし') . "</p>";
echo "<p><strong>Response:</strong></p>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($response) . "</textarea>";

// 3. Apps Script POST テスト
echo "<h2>3. Apps Script POST テスト</h2>";
echo "<p>テストデータを送信しています...</p>";

$testData = [
    'spreadsheetId' => GOOGLE_SPREADSHEET_ID,
    'sheetName' => 'Orders',
    'timestamp' => date('Y-m-d H:i:s'),
    'transactionId' => 'QUICK-TEST-' . time(),
    'email' => 'quicktest@example.com',
    'firstName' => 'クイック',
    'lastName' => 'テスト',
    'amount' => '123.45'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, GOOGLE_APPS_SCRIPT_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>送信データ:</strong></p>";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'>" . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</textarea>";

echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";
echo "<p><strong>Error:</strong> " . ($error ? $error : 'なし') . "</p>";
echo "<p><strong>Response:</strong></p>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($response) . "</textarea>";

// 4. 結果判定
echo "<h2>4. 結果判定</h2>";
if ($httpCode == 200) {
    echo "<p style='color: green;'>✅ <strong>成功!</strong> Apps Script が正常に動作しています。</p>";
} elseif ($httpCode == 401) {
    echo "<p style='color: red;'>❌ <strong>401 Unauthorized</strong> - Apps Script の権限設定に問題があります。</p>";
    echo "<p><a href='fix-apps-script.php' style='background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 修正ガイドを見る</a></p>";
} elseif ($httpCode == 404) {
    echo "<p style='color: red;'>❌ <strong>404 Not Found</strong> - Apps Script の URL が間違っています。</p>";
} else {
    echo "<p style='color: orange;'>⚠️ <strong>その他のエラー</strong> - HTTP Code: " . $httpCode . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Sheetsポータルに戻る</a></p>";
echo "</body></html>";
?>