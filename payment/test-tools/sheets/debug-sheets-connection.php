<?php
/**
 * Googleスプレッドシート接続デバッグツール
 * 
 * 送信失敗の原因を詳細に調査します
 */

require_once '../../config.php';
require_once '../../google-sheets-complete.php';

// エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ログファイルのパス
$logFile = '../../phplog';

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $result = ['success' => false, 'message' => '', 'data' => [], 'debug_info' => []];
    
    switch ($action) {
        case 'test_apps_script_direct':
            // Apps Scriptに直接テストデータを送信
            $testData = [
                'spreadsheetId' => GOOGLE_SPREADSHEET_ID,
                'sheetName' => defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders',
                'timestamp' => date('Y-m-d H:i:s'),
                'transactionId' => 'DEBUG-' . time(),
                'email' => 'debug@test.com',
                'firstName' => 'デバッグ',
                'lastName' => 'テスト',
                'amount' => '999.99'
            ];
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, GOOGLE_APPS_SCRIPT_URL);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($testData));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            
            // Verboseログを取得
            $verboseLog = fopen('php://temp', 'w+');
            curl_setopt($curl, CURLOPT_STDERR, $verboseLog);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            $curlInfo = curl_getinfo($curl);
            
            // Verboseログを読み取り
            rewind($verboseLog);
            $verboseOutput = stream_get_contents($verboseLog);
            fclose($verboseLog);
            
            curl_close($curl);
            
            $result['success'] = ($httpCode == 200 && !$curlError);
            $result['message'] = $result['success'] ? 'Apps Scriptテスト成功' : 'Apps Scriptテスト失敗';
            $result['data'] = [
                'sent_data' => $testData,
                'response' => $response,
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'curl_info' => $curlInfo,
                'verbose_log' => $verboseOutput
            ];
            break;
            
        case 'test_url_accessibility':
            // Apps Script URLへのアクセシビリティテスト
            $urls = [
                'apps_script' => GOOGLE_APPS_SCRIPT_URL,
                'sheets_api' => "https://sheets.googleapis.com/v4/spreadsheets/" . GOOGLE_SPREADSHEET_ID . "?key=" . GOOGLE_SHEETS_API_KEY
            ];
            
            $results = [];
            foreach ($urls as $name => $url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Debug Tool)');
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                $results[$name] = [
                    'url' => $url,
                    'http_code' => $httpCode,
                    'response_length' => strlen($response),
                    'response' => substr($response, 0, 500) . (strlen($response) > 500 ? '...' : ''),
                    'error' => $error,
                    'accessible' => ($httpCode == 200 && !$error)
                ];
            }
            
            $result['success'] = true;
            $result['message'] = 'URLアクセシビリティテスト完了';
            $result['data'] = $results;
            break;
            
        case 'check_logs':
            // PHPエラーログの確認
            $logs = [];
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logLines = explode("\n", $logContent);
                $recentLogs = array_slice($logLines, -20); // 最新20行
                $logs['recent_logs'] = $recentLogs;
                $logs['file_size'] = filesize($logFile);
                $logs['file_exists'] = true;
            } else {
                $logs['file_exists'] = false;
                $logs['message'] = 'ログファイルが見つかりません';
            }
            
            $result['success'] = true;
            $result['message'] = 'ログファイル確認完了';
            $result['data'] = $logs;
            break;
            
        case 'test_config_values':
            // 設定値の詳細確認
            $config = [
                'GOOGLE_SPREADSHEET_ID' => GOOGLE_SPREADSHEET_ID,
                'GOOGLE_SPREADSHEET_ID_length' => strlen(GOOGLE_SPREADSHEET_ID),
                'GOOGLE_APPS_SCRIPT_URL' => GOOGLE_APPS_SCRIPT_URL,
                'GOOGLE_APPS_SCRIPT_URL_length' => strlen(GOOGLE_APPS_SCRIPT_URL),
                'GOOGLE_SHEETS_API_KEY' => substr(GOOGLE_SHEETS_API_KEY, 0, 10) . '...',
                'GOOGLE_SHEETS_API_KEY_length' => strlen(GOOGLE_SHEETS_API_KEY),
                'GOOGLE_SHEETS_SHEET_NAME' => defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders',
                'curl_version' => curl_version(),
                'php_version' => PHP_VERSION,
                'openssl_version' => OPENSSL_VERSION_TEXT ?? 'Not available'
            ];
            
            $result['success'] = true;
            $result['message'] = '設定値確認完了';
            $result['data'] = $config;
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🔍 Googleスプレッドシート接続デバッグ</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; color: #e74c3c; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-header { background: #e74c3c; color: white; padding: 15px 20px; font-weight: bold; font-size: 18px; }
        .section-content { padding: 20px; }
        .button-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-info { background: #3498db; color: white; }
        .btn-info:hover { background: #2980b9; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .result { margin: 20px 0; padding: 20px; border-radius: 8px; display: none; }
        .result.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .result.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .data-display { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; max-height: 600px; overflow-y: auto; }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; font-size: 12px; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #e74c3c; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: none; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .error-box { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info-box { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { color: #3498db; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: #ecf0f1; border-radius: 5px; }
        .nav-links a:hover { background: #d5dbdb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Googleスプレッドシート接続デバッグ</h1>
            <p>送信失敗の原因を詳細に調査します</p>
        </div>

        <div class="nav-links">
            <a href="../index.php">🏠 メインポータル</a>
            <a href="index.php">📊 Sheetsポータル</a>
            <a href="sheets-test-suite.php">🧪 テストスイート</a>
        </div>

        <div class="error-box">
            <strong>🚨 現在の問題:</strong> Googleスプレッドシートへの送信が失敗しています。<br>
            以下のテストを順番に実行して原因を特定しましょう。
        </div>

        <!-- 基本診断 -->
        <div class="section">
            <div class="section-header">🔧 基本診断</div>
            <div class="section-content">
                <div class="button-grid">
                    <button class="btn btn-info" onclick="runTest('test_config_values')">⚙️ 設定値確認</button>
                    <button class="btn btn-warning" onclick="runTest('test_url_accessibility')">🌐 URL接続テスト</button>
                    <button class="btn btn-danger" onclick="runTest('check_logs')">📋 エラーログ確認</button>
                </div>
                
                <div class="info-box">
                    <strong>💡 基本診断について:</strong>
                    <ul>
                        <li><strong>設定値確認</strong>: config.phpの設定値とシステム情報を確認</li>
                        <li><strong>URL接続テスト</strong>: Apps ScriptとSheets APIへの接続を確認</li>
                        <li><strong>エラーログ確認</strong>: PHPエラーログから問題を特定</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 詳細テスト -->
        <div class="section">
            <div class="section-header">🧪 詳細テスト</div>
            <div class="section-content">
                <div class="button-grid">
                    <button class="btn btn-danger" onclick="runTest('test_apps_script_direct')">🎯 Apps Script直接テスト</button>
                </div>
                
                <div class="info-box">
                    <strong>💡 詳細テストについて:</strong>
                    <ul>
                        <li><strong>Apps Script直接テスト</strong>: Google Apps Scriptに直接データを送信して詳細な応答を確認</li>
                        <li>cURLの詳細ログも取得してネットワークレベルの問題を特定</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 現在の設定表示 -->
        <div class="section">
            <div class="section-header">📋 現在の設定</div>
            <div class="section-content">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <th style="padding: 10px; text-align: left; background: #f8f9fa;">設定項目</th>
                        <th style="padding: 10px; text-align: left; background: #f8f9fa;">値</th>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">スプレッドシートID</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo substr(GOOGLE_SPREADSHEET_ID, 0, 20) . '...'; ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">Apps Script URL</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo substr(GOOGLE_APPS_SCRIPT_URL, 0, 60) . '...'; ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">APIキー</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo substr(GOOGLE_SHEETS_API_KEY, 0, 15) . '...'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;">シート名</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 結果表示 -->
        <div id="result" class="result"></div>
        <div class="spinner" id="spinner"></div>

        <!-- よくある問題と解決策 -->
        <div class="section">
            <div class="section-header">💡 よくある問題と解決策</div>
            <div class="section-content">
                <h3>1. Apps Script URLの問題</h3>
                <ul>
                    <li>URLが正しくデプロイされていない</li>
                    <li>権限設定が「全員」になっていない</li>
                    <li>Apps Scriptがエラーで動作していない</li>
                </ul>
                
                <h3>2. スプレッドシートの問題</h3>
                <ul>
                    <li>スプレッドシートIDが間違っている</li>
                    <li>シート名が「Orders」になっていない</li>
                    <li>Apps Scriptがスプレッドシートにアクセス権限を持っていない</li>
                </ul>
                
                <h3>3. ネットワークの問題</h3>
                <ul>
                    <li>サーバーからGoogleへのHTTPS接続が制限されている</li>
                    <li>cURLのSSL設定に問題がある</li>
                    <li>タイムアウトが発生している</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function showSpinner() {
            document.getElementById('spinner').style.display = 'block';
            document.getElementById('result').style.display = 'none';
        }

        function hideSpinner() {
            document.getElementById('spinner').style.display = 'none';
        }

        function showResult(success, message, data = null) {
            hideSpinner();
            const resultDiv = document.getElementById('result');
            resultDiv.className = 'result ' + (success ? 'success' : 'error');
            resultDiv.style.display = 'block';
            
            let html = '<h3>' + (success ? '✅ ' : '❌ ') + message + '</h3>';
            
            if (data) {
                html += '<div class="data-display"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
            }
            
            resultDiv.innerHTML = html;
        }

        function runTest(action) {
            showSpinner();
            
            const formData = new FormData();
            formData.append('action', action);
            
            fetch('debug-sheets-connection.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // レスポンスのコンテンツタイプを確認
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // HTMLレスポンスの場合
                    return response.text().then(text => {
                        throw new Error('サーバーからHTMLレスポンスが返されました: ' + text.substring(0, 200) + '...');
                    });
                }
            })
            .then(result => {
                showResult(result.success, result.message, result.data);
            })
            .catch(error => {
                showResult(false, 'エラーが発生しました: ' + error.message);
            });
        }

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            console.log('デバッグツールが読み込まれました');
        });
    </script>
</body>
</html>