<?php
/**
 * 最もシンプルなGoogle Apps Scriptテスト
 * まずはGETリクエストから確認
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$appsScriptUrl = 'https://script.google.com/macros/s/AKfycbzu6FRvAV_nUXUG1X0__IxhRwBAF0vb1F_MX6lg-_MUxV4PIMsGMC0WszusNwK49S8k/exec';

echo "<h1>段階的テスト</h1>";

// ステップ1: 単純なGETテスト
echo "<h2>ステップ1: GETテスト</h2>";
$getResponse = file_get_contents($appsScriptUrl);
echo "<p><strong>GET レスポンス:</strong></p>";
echo "<pre>" . htmlspecialchars($getResponse) . "</pre>";

// ステップ2: cURLでGETテスト
echo "<h2>ステップ2: cURL GETテスト</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $appsScriptUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$curlGetResponse = curl_exec($ch);
$getHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>cURL GET HTTPステータス:</strong> " . $getHttpCode . "</p>";
echo "<p><strong>cURL GET レスポンス:</strong></p>";
echo "<pre>" . htmlspecialchars($curlGetResponse) . "</pre>";

// ステップ3: 最もシンプルなPOSTテスト（パラメータ形式）
echo "<h2>ステップ3: シンプルPOSTテスト（パラメータ形式）</h2>";

$postData = "name=TestUser&email=test@example.com&message=HelloWorld";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $appsScriptUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$simplePostResponse = curl_exec($ch);
$postHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$postError = curl_error($ch);
curl_close($ch);

echo "<p><strong>Simple POST HTTPステータス:</strong> " . $postHttpCode . "</p>";
if ($postError) {
    echo "<p style='color:red'><strong>POST エラー:</strong> " . htmlspecialchars($postError) . "</p>";
}
echo "<p><strong>Simple POST レスポンス:</strong></p>";
echo "<pre>" . htmlspecialchars($simplePostResponse) . "</pre>";

// Apps Scriptのログ確認を促す
echo "<hr>";
echo "<h2>次のステップ:</h2>";
echo "<ol>";
echo "<li><a href='https://script.google.com/' target='_blank'>Google Apps Script</a> でプロジェクトを開く</li>";
echo "<li>「実行」→「実行トランスクリプト」でログを確認</li>";
echo "<li>doPost関数が実際に呼ばれているかチェック</li>";
echo "</ol>";
?>