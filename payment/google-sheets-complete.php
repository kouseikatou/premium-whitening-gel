<?php
/**
 * Google Sheets Complete Integration
 * 決済完了後にGoogleスプレッドシートに注文情報を記録する完全版
 * 
 * このファイルには以下がすべて含まれています：
 * - 設定
 * - 統合機能
 * - セットアップ手順
 * - テスト機能
 * - Apps Scriptコード
 */

// ==========================================
// 設定セクション
// ==========================================

// Google Sheets API設定はconfig.phpから読み込みます
require_once 'config.php';

// 設定値の存在確認
if (!defined('GOOGLE_SHEETS_API_KEY') || !defined('GOOGLE_SPREADSHEET_ID') || !defined('GOOGLE_APPS_SCRIPT_URL')) {
    die('Error: Google Sheets configuration not found in config.php');
}

// スプレッドシートのヘッダー行
$SPREADSHEET_HEADERS = [
    'A1' => '注文日時',
    'B1' => '取引ID',
    'C1' => 'メールアドレス',
    'D1' => 'お客様名（姓）',
    'E1' => 'お客様名（名）',
    'F1' => '会社名',
    'G1' => '国',
    'H1' => '都道府県',
    'I1' => '市区町村',
    'J1' => '住所（区・町名）',
    'K1' => '住所（番地・建物）',
    'L1' => '郵便番号',
    'M1' => '電話番号',
    'N1' => '配送先名（姓）',
    'O1' => '配送先名（名）',
    'P1' => '配送先会社名',
    'Q1' => '配送先国',
    'R1' => '配送先都道府県',
    'S1' => '配送先市区町村',
    'T1' => '配送先住所（区・町名）',
    'U1' => '配送先住所（番地・建物）',
    'V1' => '配送先郵便番号',
    'W1' => '配送先電話番号',
    'X1' => '決済金額（USD）'
];

// ==========================================
// メイン関数
// ==========================================

/**
 * 注文データをGoogleスプレッドシートに送信（メイン関数）
 */
function sendOrderToGoogleSheets($orderData) {
    // 設定の検証
    $configErrors = validateGoogleSheetsConfig();
    if (!empty($configErrors)) {
        error_log("Google Sheets configuration errors: " . implode(', ', $configErrors));
        return false;
    }
    
    // Apps Script方式を試す（推奨）
    if (GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE' && GOOGLE_APPS_SCRIPT_URL !== '') {
        $result = sendOrderViaAppsScript($orderData);
        if ($result) {
            return true;
        }
        error_log("Apps Script method failed, trying Service Account method");
    }
    
    // サービスアカウント方式を試す
    if (file_exists(GOOGLE_SERVICE_ACCOUNT_FILE)) {
        $result = sendOrderViaServiceAccount($orderData);
        if ($result) {
            return true;
        }
    }
    
    error_log("All Google Sheets integration methods failed");
    return false;
}

/**
 * Google Apps Script経由でデータを送信（推奨方式）
 */
function sendOrderViaAppsScript($orderData) {
    // 動作確認済みの方式でデータを送信
    $data = array(
        'spreadsheetId' => GOOGLE_SPREADSHEET_ID,
        'sheetName' => defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders',
        'timestamp' => date('Y-m-d H:i:s'),
        'transactionId' => $orderData['transactionId'] ?? '',
        'email' => $orderData['email'] ?? '',
        'firstName' => $orderData['firstName'] ?? '',
        'lastName' => $orderData['lastName'] ?? '',
        'company' => $orderData['company'] ?? '',
        'address' => $orderData['address'] ?? '',
        'apartment' => $orderData['apartment'] ?? '',
        'city' => $orderData['city'] ?? '',
        'state' => $orderData['state'] ?? '',
        'zip' => $orderData['zip'] ?? '',
        'country' => $orderData['country'] ?? '',
        'phone' => $orderData['phone'] ?? '',
        'shippingFirstName' => $orderData['shippingFirstName'] ?? '',
        'shippingLastName' => $orderData['shippingLastName'] ?? '',
        'shippingCompany' => $orderData['shippingCompany'] ?? '',
        'shippingAddress' => $orderData['shippingAddress'] ?? '',
        'shippingApartment' => $orderData['shippingApartment'] ?? '',
        'shippingCity' => $orderData['shippingCity'] ?? '',
        'shippingState' => $orderData['shippingState'] ?? '',
        'shippingZip' => $orderData['shippingZip'] ?? '',
        'shippingCountry' => $orderData['shippingCountry'] ?? '',
        'shippingPhone' => $orderData['shippingPhone'] ?? '',
        'amount' => $orderData['amount'] ?? '',
        'isDifferentAddress' => $orderData['isDifferentAddress'] ?? false
    );
    
    // 動作確認済みのcURL設定
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, GOOGLE_APPS_SCRIPT_URL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // 重要：リダイレクトを追跡
    
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    
    error_log("Apps Script URL: " . GOOGLE_APPS_SCRIPT_URL);
    error_log("Apps Script Response: " . $response);
    error_log("Apps Script HTTP Code: " . $httpCode);
    
    curl_close($curl);
    
    if ($curlError) {
        error_log("cURL Error: " . $curlError);
        return false;
    }
    
    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['success']) && $responseData['success'] === true) {
            error_log("Order data successfully sent to Google Sheets via Apps Script");
            return true;
        }
    }
    
    error_log("Failed to send order data via Apps Script. HTTP Code: " . $httpCode);
    return false;
}

/**
 * サービスアカウント経由でデータを送信（上級者向け）
 */
function sendOrderViaServiceAccount($orderData) {
    $serviceAccountFile = GOOGLE_SERVICE_ACCOUNT_FILE;
    
    if (!file_exists($serviceAccountFile)) {
        error_log("Service account file not found: " . $serviceAccountFile);
        return false;
    }
    
    // サービスアカウント認証情報を読み込み
    $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);
    if (!$serviceAccount) {
        error_log("Failed to parse service account file");
        return false;
    }
    
    // JWT トークンを作成
    $accessToken = createJwtToken($serviceAccount);
    if (!$accessToken) {
        error_log("Failed to create access token");
        return false;
    }
    
    // データを準備
    $rowData = prepareOrderRowData($orderData);
    
    // シート名を取得
    $sheetName = defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders';
    $rangeForApi = $sheetName . '!A:Z';
    
    // Google Sheets APIにリクエスト
    $url = "https://sheets.googleapis.com/v4/spreadsheets/" . GOOGLE_SPREADSHEET_ID . "/values/" . $rangeForApi . ":append?valueInputOption=RAW";
    
    $postData = json_encode([
        'range' => $rangeForApi,
        'majorDimension' => 'ROWS',
        'values' => [$rowData]
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
        'Content-Length: ' . strlen($postData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        error_log("Order data successfully sent to Google Sheets via Service Account");
        return true;
    } else {
        error_log("Failed to send order data to Google Sheets. HTTP Code: " . $httpCode . ", Response: " . $response);
        return false;
    }
}

// ==========================================
// 補助関数
// ==========================================

/**
 * 注文データを行データに変換
 */
function prepareOrderRowData($orderData) {
    return [
        date('Y-m-d H:i:s'), // 注文日時
        $orderData['transactionId'] ?? '', // 取引ID
        $orderData['email'] ?? '', // メールアドレス
        $orderData['lastName'] ?? '', // お客様名（姓）
        $orderData['firstName'] ?? '', // お客様名（名）
        $orderData['company'] ?? '', // 会社名
        $orderData['country'] ?? '', // 国
        $orderData['state'] ?? '', // 都道府県
        $orderData['city'] ?? '', // 市区町村
        $orderData['address'] ?? '', // 住所（区・町名）
        $orderData['apartment'] ?? '', // 住所（番地・建物）
        $orderData['zip'] ?? '', // 郵便番号
        $orderData['phone'] ?? '', // 電話番号
        $orderData['shippingLastName'] ?? '', // 配送先名（姓）
        $orderData['shippingFirstName'] ?? '', // 配送先名（名）
        $orderData['shippingCompany'] ?? '', // 配送先会社名
        $orderData['shippingCountry'] ?? '', // 配送先国
        $orderData['shippingState'] ?? '', // 配送先都道府県
        $orderData['shippingCity'] ?? '', // 配送先市区町村
        $orderData['shippingAddress'] ?? '', // 配送先住所（区・町名）
        $orderData['shippingApartment'] ?? '', // 配送先住所（番地・建物）
        $orderData['shippingZip'] ?? '', // 配送先郵便番号
        $orderData['shippingPhone'] ?? '', // 配送先電話番号
        $orderData['amount'] ?? '' // 決済金額（USD）
    ];
}

/**
 * 設定の検証
 */
function validateGoogleSheetsConfig() {
    $errors = [];
    
    if (GOOGLE_SPREADSHEET_ID === 'YOUR_SPREADSHEET_ID_HERE' || empty(GOOGLE_SPREADSHEET_ID)) {
        $errors[] = 'スプレッドシートIDが設定されていません';
    }
    
    if ((GOOGLE_APPS_SCRIPT_URL === 'YOUR_APPS_SCRIPT_URL_HERE' || empty(GOOGLE_APPS_SCRIPT_URL)) && 
        !file_exists(GOOGLE_SERVICE_ACCOUNT_FILE)) {
        $errors[] = 'Google Apps Script URLまたはサービスアカウントファイルが設定されていません';
    }
    
    return $errors;
}

/**
 * JWTトークンを作成（サービスアカウント用）
 */
function createJwtToken($serviceAccount) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
    $now = time();
    $payload = json_encode([
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ]);
    
    $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = '';
    if (!openssl_sign($base64Header . '.' . $base64Payload, $signature, $serviceAccount['private_key'], 'SHA256')) {
        return false;
    }
    
    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    
    // アクセストークンを取得
    $tokenResponse = getAccessToken($jwt);
    $tokenData = json_decode($tokenResponse, true);
    
    return isset($tokenData['access_token']) ? $tokenData['access_token'] : false;
}

/**
 * アクセストークンを取得
 */
function getAccessToken($jwt) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// ==========================================
// テスト機能
// ==========================================

/**
 * Google Sheets設定をテスト
 */
function testGoogleSheetsIntegration() {
    echo "<h2>Google Sheets 統合テスト</h2>";
    
    // 設定確認
    echo "<h3>設定確認:</h3>";
    echo "<ul>";
    echo "<li>スプレッドシートID: " . GOOGLE_SPREADSHEET_ID . "</li>";
    echo "<li>Apps Script URL: " . (GOOGLE_APPS_SCRIPT_URL === 'YOUR_APPS_SCRIPT_URL_HERE' ? '<span style="color:red">未設定</span>' : '<span style="color:green">設定済み</span>') . "</li>";
    echo "<li>サービスアカウント: " . (file_exists(GOOGLE_SERVICE_ACCOUNT_FILE) ? '<span style="color:green">設定済み</span>' : '<span style="color:red">未設定</span>') . "</li>";
    echo "</ul>";
    
    // 設定検証
    $errors = validateGoogleSheetsConfig();
    if (!empty($errors)) {
        echo "<p style='color: red;'>設定エラー:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        return;
    }
    
    // テストデータ（新しいフィールド順序に対応）
    $testData = [
        'transactionId' => 'TEST-' . time(),
        'email' => 'test@example.com',
        'lastName' => 'ユーザー',
        'firstName' => 'テスト',
        'company' => 'テスト会社',
        'country' => '日本',
        'state' => '東京都',
        'city' => '東京',
        'address' => 'テスト住所',
        'apartment' => '1-2-3',
        'zip' => '100-0001',
        'phone' => '090-1234-5678',
        'shippingLastName' => 'ユーザー',
        'shippingFirstName' => 'テスト',
        'shippingCompany' => 'テスト会社',
        'shippingCountry' => '日本',
        'shippingState' => '東京都',
        'shippingCity' => '東京',
        'shippingAddress' => 'テスト住所',
        'shippingApartment' => '1-2-3',
        'shippingZip' => '100-0001',
        'shippingPhone' => '090-1234-5678',
        'amount' => '100.00'
    ];
    
    echo "<p>テストデータを送信中...</p>";
    
    $result = sendOrderToGoogleSheets($testData);
    
    if ($result) {
        echo "<p style='color: green;'>✓ テストデータの送信成功！</p>";
        echo "<p><a href='https://docs.google.com/spreadsheets/d/" . GOOGLE_SPREADSHEET_ID . "/edit' target='_blank'>スプレッドシートを確認</a></p>";
    } else {
        echo "<p style='color: red;'>✗ テストデータの送信失敗</p>";
        echo "<p>PHPエラーログを確認してください。</p>";
    }
}

/**
 * 詳細デバッグテスト
 */
function debugGoogleSheetsApi() {
    echo "<h2>Google Sheets API デバッグテスト</h2>";
    
    $spreadsheetId = GOOGLE_SPREADSHEET_ID;
    $sheetName = defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders';
    $range = $sheetName . '!A1:Z1';
    $apiKey = GOOGLE_SHEETS_API_KEY;
    
    // 読み取りテスト
    echo "<h3>読み取りテスト:</h3>";
    $readUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $readUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTPステータスコード: " . $httpCode . "</p>";
    
    if ($httpCode == 200) {
        echo "<p style='color:green'>読み取り成功!</p>";
    } else {
        echo "<p style='color:red'>読み取り失敗</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    
    // 書き込みテスト（APIキー - 失敗するはず）
    echo "<h3>書き込みテスト（APIキー使用 - 失敗予定）:</h3>";
    
    $testData = [date('Y-m-d H:i:s'), 'TEST-' . time(), 'test@example.com'];
    $writeUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:append?valueInputOption=RAW&key={$apiKey}";
    
    $postData = json_encode([
        'range' => $range,
        'majorDimension' => 'ROWS',
        'values' => [$testData]
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $writeUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTPステータスコード: " . $httpCode . "</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// ==========================================
// セットアップ手順（コメント内に記載）
// ==========================================

/*
=== Google Sheets 統合セットアップ手順 ===

問題：Google Sheets APIでは書き込み操作にOAuth認証が必要で、APIキーだけでは書き込みできません。

解決方法：Google Apps Script Web Appを使用してデータ書き込みを代行します。

=== ステップ1: Google Apps Script Web Appの作成 ===

1. https://script.google.com/ にアクセス
2. 「新しいプロジェクト」を作成
3. 以下のコードを貼り付け：

// ライブラリをインポートする場合（Apps Scriptエディタで設定が必要）
// ライブラリ ID: 1n9ILIDRppTaH1i0B7wora2bFmZnZAwPovHRfxS1sk3h-BMH3PSJpdsyd
// バージョン: 11

function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    const spreadsheetId = data.spreadsheetId;
    const sheetName = data.sheetName || 'Orders';
    
    // ライブラリを使用する場合（例）
    // const libraryResult = YourLibraryName.someFunction(data);
    
    // メイン処理
    const spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    const sheet = spreadsheet.getSheetByName(sheetName) || spreadsheet.getActiveSheet();
    
    // 注文データを行として準備
    const orderRow = prepareOrderRow(data);
    
    // シートに追加
    sheet.appendRow(orderRow);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Data successfully added to spreadsheet',
        timestamp: new Date().toISOString(),
        sheetName: sheetName,
        rowsAdded: 1
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        error: error.toString(),
        timestamp: new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// 注文データを行形式に変換する関数
function prepareOrderRow(data) {
  return [
    data.timestamp || new Date().toISOString(),
    data.transactionId || '',
    data.email || '',
    data.lastName || '',
    data.firstName || '',
    data.company || '',
    data.country || '',
    data.state || '',
    data.city || '',
    data.address || '',
    data.apartment || '',
    data.zip || '',
    data.phone || '',
    data.shippingLastName || '',
    data.shippingFirstName || '',
    data.shippingCompany || '',
    data.shippingCountry || '',
    data.shippingState || '',
    data.shippingCity || '',
    data.shippingAddress || '',
    data.shippingApartment || '',
    data.shippingZip || '',
    data.shippingPhone || '',
    data.amount || '',
    data.isDifferentAddress || false
  ];
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web App is running',
      timestamp: new Date().toISOString()
    }))
    .setMimeType(ContentService.MimeType.JSON);
}

4. 「デプロイ」→「新しいデプロイ」
5. 種類：「ウェブアプリ」
6. 実行者：「自分」
7. アクセス：「全員」
8. Web App URLをコピー

=== ステップ2: PHPの設定更新 ===

config.phpで以下を確認：
define('GOOGLE_APPS_SCRIPT_URL', 'https://script.google.com/macros/s/AKfycbyvdE-1Pgv7RRyyKrpspMfCEOMch9pi9BC7JH8Fgoxdxkpu3_V6a0xaXZmgAuMsfrli2g/exec');

ライブラリ統合情報：
- ライブラリ ID: 1n9ILIDRppTaH1i0B7wora2bFmZnZAwPovHRfxS1sk3h-BMH3PSJpdsyd
- バージョン: 11
- 統合後のWeb App URL: 上記のURL

=== ステップ3: スプレッドシートの準備 ===

1. スプレッドシートを開く：
   https://docs.google.com/spreadsheets/d/11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs/edit

2. シート名を「Orders」に変更

3. ヘッダー行を設定（A1から）：
   注文日時, 取引ID, メールアドレス, お客様名（姓）, お客様名（名）, 会社名, 国, 都道府県, 
   市区町村, 住所（区・町名）, 住所（番地・建物）, 郵便番号, 電話番号, 配送先名（姓）, 
   配送先名（名）, 配送先会社名, 配送先国, 配送先都道府県, 配送先市区町村, 
   配送先住所（区・町名）, 配送先住所（番地・建物）, 配送先郵便番号, 配送先電話番号, 決済金額（USD）

=== ステップ4: テスト ===

ブラウザで以下にアクセス：
- テスト: ?action=test
- デバッグ: ?action=debug

=== 使用方法 ===

authorize-credit-card.php で以下のように使用：

require_once 'google-sheets-complete.php';

$orderData = [
    'transactionId' => $tresponse->getTransId(),
    'email' => $_POST['mell-contact'] ?? '',
    'firstName' => $_POST['FirstName'] ?? '',
    // ... 他のデータ
];

$result = sendOrderToGoogleSheets($orderData);
if ($result) {
    echo "注文データをスプレッドシートに記録しました";
} else {
    echo "スプレッドシートへの記録に失敗しました";
}

*/

// ==========================================
// Web インターフェース（テスト用）
// ==========================================

if (isset($_GET['action'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    switch ($_GET['action']) {
        case 'test':
            testGoogleSheetsIntegration();
            break;
        case 'debug':
            debugGoogleSheetsApi();
            break;
        default:
            echo "<h2>Google Sheets Complete Integration</h2>";
            echo "<p><a href='?action=test'>設定テスト</a></p>";
            echo "<p><a href='?action=debug'>デバッグテスト</a></p>";
    }
}

?>