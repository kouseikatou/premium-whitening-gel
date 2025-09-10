<?php
/**
 * Google Apps Script デバッグテスト
 */

$appsScriptUrl = 'https://script.google.com/macros/s/AKfycbzu6FRvAV_nUXUG1X0__IxhRwBAF0vb1F_MX6lg-_MUxV4PIMsGMC0WszusNwK49S8k/exec';

$testData = [
    'spreadsheetId' => '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs',
    'timestamp' => date('Y-m-d H:i:s'),
    'transactionId' => 'DEBUG-TEST-' . time(),
    'email' => 'debug@test.com',
    'lastName' => 'テスト姓',
    'firstName' => 'テスト名',
    'company' => 'テスト会社',
    'country' => 'Japan',
    'state' => 'テスト都道府県',
    'city' => 'テスト市',
    'address' => 'テスト区町名',
    'apartment' => 'テスト番地',
    'zip' => '123-4567',
    'phone' => '090-1234-5678',
    'shippingLastName' => '配送テスト姓',
    'shippingFirstName' => '配送テスト名',
    'shippingCompany' => '配送テスト会社',
    'shippingCountry' => 'Japan',
    'shippingState' => '配送テスト都道府県',
    'shippingCity' => '配送テスト市',
    'shippingAddress' => '配送テスト区町名',
    'shippingApartment' => '配送テスト番地',
    'shippingZip' => '987-6543',
    'shippingPhone' => '080-9876-5432',
    'amount' => '140.00',
    'isDifferentAddress' => true
];

echo "<h1>Google Apps Script テスト</h1>";
echo "<h2>送信データ:</h2>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $appsScriptUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "<h2>応答:</h2>";
echo "<p>HTTPコード: " . $httpCode . "</p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<p><a href='https://docs.google.com/spreadsheets/d/11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs/edit' target='_blank'>スプレッドシートを確認</a></p>";
?>