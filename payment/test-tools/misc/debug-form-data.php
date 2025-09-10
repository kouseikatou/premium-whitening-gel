<?php
/**
 * Form Data形式でApps Scriptをテスト
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$appsScriptUrl = 'https://script.google.com/macros/s/AKfycbzu6FRvAV_nUXUG1X0__IxhRwBAF0vb1F_MX6lg-_MUxV4PIMsGMC0WszusNwK49S8k/exec';

echo "<h1>Form Data Test</h1>";

// Form Data形式でテスト
$formData = [
    'spreadsheetId' => '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs',
    'range' => 'Orders!A:Z',
    'data' => json_encode([
        [
            date('Y-m-d H:i:s'),
            'FORM-TEST-' . time(),
            'form@test.com',
            'Form',
            'Test',
            'Form Company',
            'Form Address',
            '1-2-3',
            'Tokyo',
            'Tokyo',
            '100-0001',
            'Japan',
            '090-1234-5678',
            'Form',
            'Shipping',
            'Shipping Company',
            'Shipping Address',
            'Apt 456',
            'Osaka',
            'Osaka',
            '550-0001',
            'Japan',
            '090-8765-4321',
            '100.00'
        ]
    ])
];

echo "<h2>Form Data送信:</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $appsScriptUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTPステータス:</strong> " . $httpCode . "</p>";
if ($curlError) {
    echo "<p style='color:red'><strong>cURLエラー:</strong> " . htmlspecialchars($curlError) . "</p>";
}
echo "<p><strong>レスポンス:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

if ($httpCode == 200) {
    echo "<h2 style='color:green'>✅ Form Data方式で成功！</h2>";
} else {
    echo "<h2 style='color:red'>❌ Form Data方式でも失敗</h2>";
    
    echo "<hr>";
    echo "<h2>簡易テスト: 単純なGETパラメータ</h2>";
    
    // 最も単純なGETパラメータでテスト
    $simpleUrl = $appsScriptUrl . '?test=simple&action=ping';
    
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $simpleUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    
    $response2 = curl_exec($ch2);
    $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    
    echo "<p><strong>Simple GET HTTPステータス:</strong> " . $httpCode2 . "</p>";
    echo "<p><strong>Simple GET レスポンス:</strong></p>";
    echo "<pre>" . htmlspecialchars($response2) . "</pre>";
}
?>