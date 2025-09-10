<?php
/**
 * Google Sheets テストスイート
 * 
 * Googleスプレッドシート関連の全テスト機能をまとめたツール
 */

require_once '../../config.php';
require_once '../../google-sheets-complete.php';

// エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

// テストデータのセット
$testDataSets = [
    'basic' => [
        'transactionId' => 'SHEET-TEST-' . date('YmdHis'),
        'email' => 'sheets-test@example.com',
        'firstName' => 'シート',
        'lastName' => 'テスト',
        'company' => 'テスト株式会社',
        'address' => '千代田区丸の内1-1-1',
        'apartment' => 'テストビル101',
        'city' => '東京都',
        'state' => '東京都',
        'zip' => '100-0001',
        'country' => '日本',
        'phone' => '03-1234-5678',
        'shippingFirstName' => 'シート',
        'shippingLastName' => 'テスト',
        'shippingCompany' => 'テスト株式会社',
        'shippingAddress' => '千代田区丸の内1-1-1',
        'shippingApartment' => 'テストビル101',
        'shippingCity' => '東京都',
        'shippingState' => '東京都',
        'shippingZip' => '100-0001',
        'shippingCountry' => '日本',
        'shippingPhone' => '03-1234-5678',
        'amount' => '100.00',
        'isDifferentAddress' => false
    ],
    'bulk' => [
        'count' => 5,
        'template' => [
            'email' => 'bulk-test-{i}@example.com',
            'firstName' => 'バルク{i}',
            'lastName' => 'テスト',
            'company' => 'バルクテスト株式会社',
            'address' => '渋谷区道玄坂2-{i}-1',
            'city' => '東京都',
            'state' => '東京都',
            'zip' => '150-0043',
            'country' => '日本',
            'phone' => '03-3333-444{i}',
            'amount' => '50.00',
            'isDifferentAddress' => false
        ]
    ]
];

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $result = ['success' => false, 'message' => '', 'data' => []];
    
    switch ($action) {
        case 'test_config':
            // 設定テスト
            $errors = validateGoogleSheetsConfig();
            $result['success'] = empty($errors);
            $result['message'] = empty($errors) ? '設定は正常です' : '設定エラーがあります';
            $result['data'] = ['errors' => $errors];
            break;
            
        case 'test_read':
            // 読み取りテスト
            debugGoogleSheetsApi();
            $result['success'] = true;
            $result['message'] = '読み取りテストを実行しました（出力は下記を参照）';
            break;
            
        case 'send_basic':
            // 基本データ送信
            $testData = $testDataSets['basic'];
            $success = sendOrderToGoogleSheets($testData);
            $result['success'] = $success;
            $result['message'] = $success ? 'Googleスプレッドシートへの送信に成功しました' : 'Googleスプレッドシートへの送信に失敗しました';
            $result['data'] = $testData;
            break;
            
        case 'send_bulk':
            // バルクデータ送信
            $count = intval($_POST['bulk_count'] ?? 5);
            $template = $testDataSets['bulk']['template'];
            $results = [];
            
            for ($i = 1; $i <= $count; $i++) {
                $testData = [];
                foreach ($template as $key => $value) {
                    $testData[$key] = str_replace('{i}', $i, $value);
                }
                $testData['transactionId'] = 'BULK-' . date('YmdHis') . '-' . sprintf('%03d', $i);
                
                $success = sendOrderToGoogleSheets($testData);
                $results[] = ['success' => $success, 'data' => $testData];
                
                // 少し待機（API制限対策）
                usleep(500000); // 0.5秒
            }
            
            $successCount = count(array_filter($results, function($r) { return $r['success']; }));
            $result['success'] = $successCount > 0;
            $result['message'] = "{$successCount}/{$count} 件のデータ送信に成功しました";
            $result['data'] = $results;
            break;
            
        case 'clear_sheet':
            // シートクリア（ヘッダー以外）
            $result['success'] = false;
            $result['message'] = 'シートクリア機能は安全のため無効化されています';
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 設定状態の確認
$configStatus = [
    'spreadsheet_id' => !empty(GOOGLE_SPREADSHEET_ID),
    'api_key' => !empty(GOOGLE_SHEETS_API_KEY),
    'apps_script_url' => !empty(GOOGLE_APPS_SCRIPT_URL) && GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE',
    'sheet_name' => defined('GOOGLE_SHEETS_SHEET_NAME')
];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Google Sheets テストスイート</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-header { background: #34495e; color: white; padding: 15px 20px; font-weight: bold; font-size: 18px; }
        .section-content { padding: 20px; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .status-item { padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; }
        .status-ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .button-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .input-group { margin: 15px 0; }
        .input-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .result { margin: 20px 0; padding: 20px; border-radius: 8px; display: none; }
        .result.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .result.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .data-display { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; max-height: 400px; overflow-y: auto; }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
        .info-box { background: #e3f2fd; border: 1px solid #bbdefb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning-box { background: #fff3e0; border: 1px solid #ffcc02; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .config-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .config-table th, .config-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .config-table th { background: #f8f9fa; font-weight: bold; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: none; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Google Sheets テストスイート</h1>
            <p>Googleスプレッドシート関連の機能をテストするための統合ツールです。</p>
        </div>

        <!-- 設定状態 -->
        <div class="section">
            <div class="section-header">📋 設定状態</div>
            <div class="section-content">
                <div class="status-grid">
                    <div class="status-item <?php echo $configStatus['spreadsheet_id'] ? 'status-ok' : 'status-error'; ?>">
                        <div>📄 スプレッドシートID</div>
                        <div><?php echo $configStatus['spreadsheet_id'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['api_key'] ? 'status-ok' : 'status-error'; ?>">
                        <div>🔑 APIキー</div>
                        <div><?php echo $configStatus['api_key'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['apps_script_url'] ? 'status-ok' : 'status-error'; ?>">
                        <div>🔧 Apps Script URL</div>
                        <div><?php echo $configStatus['apps_script_url'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['sheet_name'] ? 'status-ok' : 'status-error'; ?>">
                        <div>📝 シート名</div>
                        <div><?php echo $configStatus['sheet_name'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                </div>
                
                <table class="config-table">
                    <tr>
                        <th>設定項目</th>
                        <th>現在の値</th>
                    </tr>
                    <tr>
                        <td>スプレッドシートID</td>
                        <td><code><?php echo substr(GOOGLE_SPREADSHEET_ID, 0, 20) . '...'; ?></code></td>
                    </tr>
                    <tr>
                        <td>シート名</td>
                        <td><code><?php echo defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders'; ?></code></td>
                    </tr>
                    <tr>
                        <td>APIキー</td>
                        <td><code><?php echo substr(GOOGLE_SHEETS_API_KEY, 0, 10) . '...'; ?></code></td>
                    </tr>
                    <tr>
                        <td>Apps Script URL</td>
                        <td><code><?php echo substr(GOOGLE_APPS_SCRIPT_URL, 0, 50) . '...'; ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 基本テスト -->
        <div class="section">
            <div class="section-header">🧪 基本テスト</div>
            <div class="section-content">
                <div class="button-grid">
                    <button class="btn btn-info" onclick="runTest('test_config')">📋 設定検証</button>
                    <button class="btn btn-primary" onclick="runTest('test_read')">📖 読み取りテスト</button>
                    <button class="btn btn-success" onclick="runTest('send_basic')">📤 基本データ送信</button>
                </div>
                
                <div class="info-box">
                    <strong>💡 基本テストについて:</strong>
                    <ul>
                        <li><strong>設定検証</strong>: config.phpの設定値をチェック</li>
                        <li><strong>読み取りテスト</strong>: Google Sheets APIで読み取り可能かテスト</li>
                        <li><strong>基本データ送信</strong>: 1件のテストデータをスプレッドシートに送信</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- バルクテスト -->
        <div class="section">
            <div class="section-header">🔄 バルクテスト</div>
            <div class="section-content">
                <div class="input-group">
                    <label for="bulkCount">送信データ数:</label>
                    <input type="number" id="bulkCount" value="5" min="1" max="20">
                </div>
                
                <div class="button-grid">
                    <button class="btn btn-warning" onclick="runBulkTest()">📦 バルクデータ送信</button>
                </div>
                
                <div class="warning-box">
                    <strong>⚠️ バルクテストについて:</strong>
                    <ul>
                        <li>指定した件数のテストデータを連続送信します</li>
                        <li>API制限を考慮して0.5秒間隔で送信されます</li>
                        <li>大量データのテストに使用してください</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- スプレッドシートリンク -->
        <div class="section">
            <div class="section-header">🔗 スプレッドシートリンク</div>
            <div class="section-content">
                <p>テスト結果を確認するためのリンク:</p>
                <div class="button-grid">
                    <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" target="_blank" class="btn btn-primary">
                        📊 スプレッドシートを開く
                    </a>
                </div>
            </div>
        </div>

        <!-- 結果表示 -->
        <div id="result" class="result"></div>
        <div class="spinner" id="spinner"></div>

        <!-- 読み取りテスト出力用 -->
        <div id="readTestOutput" style="display: none;">
            <div class="section">
                <div class="section-header">📖 読み取りテスト結果</div>
                <div class="section-content">
                    <div id="readTestContent"></div>
                </div>
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
            
            fetch('sheets-test-suite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (action === 'test_read') {
                    // 読み取りテストは特別処理
                    showResult(true, '読み取りテストを実行しました');
                    // 実際の出力は下記のdebugGoogleSheetsApiで表示される
                } else {
                    showResult(result.success, result.message, result.data);
                }
            })
            .catch(error => {
                showResult(false, 'エラーが発生しました: ' + error);
            });
        }

        function runBulkTest() {
            const count = document.getElementById('bulkCount').value;
            showSpinner();
            
            const formData = new FormData();
            formData.append('action', 'send_bulk');
            formData.append('bulk_count', count);
            
            fetch('sheets-test-suite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                showResult(result.success, result.message, result.data);
            })
            .catch(error => {
                showResult(false, 'エラーが発生しました: ' + error);
            });
        }

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Google Sheets テストスイートが読み込まれました');
        });
    </script>
</body>
</html>