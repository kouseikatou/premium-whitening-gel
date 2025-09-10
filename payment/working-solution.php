<?php
/**
 * 動作確認済みのGoogle Apps Script連携コード
 * リサーチ結果に基づく正しい実装
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Google Apps Script URL
$appsScriptUrl = 'https://script.google.com/macros/s/AKfycbzu6FRvAV_nUXUG1X0__IxhRwBAF0vb1F_MX6lg-_MUxV4PIMsGMC0WszusNwK49S8k/exec';

// テストデータ
$data = array(
    'spreadsheetId' => '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs',
    'timestamp' => date('Y-m-d H:i:s'),
    'transactionId' => 'WORKING-TEST-' . time(),
    'email' => 'working@test.com',
    'firstName' => 'Working',
    'lastName' => 'Test',
    'company' => 'Working Company',
    'address' => 'Working Address',
    'apartment' => '1-2-3',
    'city' => 'Tokyo',
    'state' => 'Tokyo',
    'zip' => '100-0001',
    'country' => 'Japan',
    'phone' => '090-1234-5678',
    'amount' => '100.00'
);

echo "<h1>動作確認済みソリューション</h1>";
echo "<h2>送信データ:</h2>";
echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";

// 正しいcURL設定（リサーチ結果に基づく）
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $appsScriptUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // 重要：リダイレクトを追跡

// ヘッダー設定
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

// JSONデータを送信
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

// SSL設定（必要に応じて）
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// タイムアウト設定
curl_setopt($curl, CURLOPT_TIMEOUT, 30);

echo "<h2>送信中...</h2>";

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);

echo "<p><strong>HTTPステータス:</strong> " . $httpCode . "</p>";

if ($curlError) {
    echo "<p style='color:red'><strong>cURLエラー:</strong> " . htmlspecialchars($curlError) . "</p>";
} else {
    echo "<p><strong>レスポンス:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // JSONレスポンスを解析
    $responseData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($responseData['status']) && $responseData['status'] === 'success') {
            echo "<h2 style='color:green'>✅ 成功！</h2>";
            echo "<p><a href='https://docs.google.com/spreadsheets/d/11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs/edit' target='_blank'>スプレッドシートを確認</a></p>";
        } else {
            echo "<h2 style='color:orange'>⚠️ レスポンス受信、内容確認が必要</h2>";
        }
    }
}

curl_close($curl);
?>