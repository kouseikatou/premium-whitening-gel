<?php
/**
 * テスト用データ送信ツール
 * 
 * 決済システムを通さずに、テストデータを送信できます
 */

require_once '../../config.php';
require_once '../../google-sheets-complete.php';
require_once '../../send-to-external-system.php';

// エラー表示を有効化（テスト用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// テストデータのセット
$testDataSets = [
    'normal' => [
        'transactionId' => 'TEST-' . date('YmdHis') . '-' . rand(1000, 9999),
        'email' => 'test@example.com',
        'lastName' => 'ユーザー',
        'firstName' => 'テスト',
        'company' => 'テスト株式会社',
        'country' => '日本',
        'state' => '東京都',
        'city' => '東京都',
        'address' => '千代田区丸の内1-1-1',
        'apartment' => '101号室',
        'zip' => '100-0001',
        'phone' => '03-1234-5678',
        'shippingLastName' => 'ユーザー',
        'shippingFirstName' => 'テスト',
        'shippingCompany' => 'テスト株式会社',
        'shippingCountry' => '日本',
        'shippingState' => '東京都',
        'shippingCity' => '東京都',
        'shippingAddress' => '千代田区丸の内1-1-1',
        'shippingApartment' => '101号室',
        'shippingZip' => '100-0001',
        'shippingPhone' => '03-1234-5678',
        'amount' => '140.00',
        'isDifferentAddress' => false
    ],
    'different_address' => [
        'transactionId' => 'TEST-DIFF-' . date('YmdHis') . '-' . rand(1000, 9999),
        'email' => 'different@example.com',
        'lastName' => '太郎',
        'firstName' => '請求',
        'company' => '請求先会社',
        'country' => '日本',
        'state' => '東京都',
        'city' => '東京都',
        'address' => '千代田区丸の内1-1-1',
        'apartment' => '',
        'zip' => '100-0001',
        'phone' => '03-1111-2222',
        'shippingLastName' => '花子',
        'shippingFirstName' => '配送',
        'shippingCompany' => '配送先会社',
        'shippingCountry' => '日本',
        'shippingState' => '東京都',
        'shippingCity' => '東京都',
        'shippingAddress' => '渋谷区道玄坂2-2-2',
        'shippingApartment' => '202号室',
        'shippingZip' => '150-0043',
        'shippingPhone' => '03-3333-4444',
        'amount' => '280.50',
        'isDifferentAddress' => true
    ],
    'international' => [
        'transactionId' => 'TEST-INTL-' . date('YmdHis') . '-' . rand(1000, 9999),
        'email' => 'international@example.com',
        'lastName' => 'Smith',
        'firstName' => 'John',
        'company' => 'Global Corp',
        'country' => 'USA',
        'state' => 'NY',
        'city' => 'New York',
        'address' => '123 Main Street',
        'apartment' => 'Suite 456',
        'zip' => '10001',
        'phone' => '+1-212-555-1234',
        'shippingLastName' => 'Smith',
        'shippingFirstName' => 'John',
        'shippingCompany' => 'Global Corp',
        'shippingCountry' => 'USA',
        'shippingState' => 'NY',
        'shippingCity' => 'New York',
        'shippingAddress' => '123 Main Street',
        'shippingApartment' => 'Suite 456',
        'shippingZip' => '10001',
        'shippingPhone' => '+1-212-555-1234',
        'amount' => '99.99',
        'isDifferentAddress' => false
    ]
];

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $dataSet = $_POST['data_set'] ?? 'normal';
    $customData = isset($_POST['custom_data']) ? json_decode($_POST['custom_data'], true) : null;
    
    // 使用するデータを決定
    $orderData = $customData ?: ($testDataSets[$dataSet] ?? $testDataSets['normal']);
    
    // タイムスタンプを追加
    $orderData['timestamp'] = date('Y-m-d H:i:s');
    
    $result = ['success' => false, 'message' => ''];
    
    switch ($action) {
        case 'google_sheets':
            // Google Sheetsに送信
            $success = sendOrderToGoogleSheets($orderData);
            $result['success'] = $success;
            $result['message'] = $success ? 
                'Googleスプレッドシートへの送信に成功しました' : 
                'Googleスプレッドシートへの送信に失敗しました';
            $result['data'] = $orderData;
            break;
            
        case 'external_system':
            // 外部システムに送信
            if (defined('EXTERNAL_SYSTEM_URL')) {
                $success = sendToExternalSystem($orderData);
                $result['success'] = $success;
                $result['message'] = $success ? 
                    '外部システムへの送信に成功しました' : 
                    '外部システムへの送信に失敗しました';
            } else {
                $result['message'] = '外部システムのURLが設定されていません';
            }
            $result['data'] = $orderData;
            break;
            
        case 'preview':
            // プレビューのみ
            $result['success'] = true;
            $result['message'] = 'データプレビュー';
            $result['data'] = $orderData;
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// HTML表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テスト用データ送信ツール</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 5px; }
        .button-group { margin: 20px 0; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; font-size: 16px; }
        button:hover { background: #0056b3; }
        button.secondary { background: #6c757d; }
        button.success { background: #28a745; }
        button.danger { background: #dc3545; }
        select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        textarea { height: 300px; font-family: monospace; font-size: 12px; }
        .result { margin: 20px 0; padding: 20px; border-radius: 5px; display: none; }
        .result.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .result.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; overflow-x: auto; }
        .data-preview { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin: 10px 0; }
        .config-status { margin: 20px 0; }
        .status-item { padding: 5px 10px; margin: 5px 0; border-radius: 3px; }
        .status-ok { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .tab-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-button { padding: 10px 20px; background: #e9ecef; border: none; border-radius: 5px 5px 0 0; cursor: pointer; }
        .tab-button.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 テスト用データ送信ツール</h1>
        
        <div class="info">
            <strong>このツールについて:</strong><br>
            決済システムを通さずに、テストデータを送信できます。<br>
            Googleスプレッドシート、外部システム、または両方にテストデータを送信してシステムの動作を確認できます。
        </div>

        <div class="config-status">
            <h3>設定状態</h3>
            <?php
            // Google Sheets設定チェック
            $googleSheetsConfigured = !empty(GOOGLE_SPREADSHEET_ID) && GOOGLE_SPREADSHEET_ID !== 'YOUR_SPREADSHEET_ID_HERE';
            $appsScriptConfigured = !empty(GOOGLE_APPS_SCRIPT_URL) && GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE';
            $externalSystemConfigured = defined('EXTERNAL_SYSTEM_URL') && !empty(EXTERNAL_SYSTEM_URL);
            ?>
            
            <div class="status-item <?php echo $googleSheetsConfigured ? 'status-ok' : 'status-error'; ?>">
                📊 Googleスプレッドシート: <?php echo $googleSheetsConfigured ? '✓ 設定済み' : '✗ 未設定'; ?>
                <?php if ($googleSheetsConfigured): ?>
                    (ID: <?php echo substr(GOOGLE_SPREADSHEET_ID, 0, 10); ?>...)
                <?php endif; ?>
            </div>
            
            <div class="status-item <?php echo $appsScriptConfigured ? 'status-ok' : 'status-error'; ?>">
                📝 Apps Script: <?php echo $appsScriptConfigured ? '✓ 設定済み' : '✗ 未設定'; ?>
            </div>
            
            <div class="status-item <?php echo $externalSystemConfigured ? 'status-ok' : 'status-error'; ?>">
                🔗 外部システム: <?php echo $externalSystemConfigured ? '✓ 設定済み' : '✗ 未設定'; ?>
                <?php if ($externalSystemConfigured): ?>
                    (<?php echo parse_url(EXTERNAL_SYSTEM_URL, PHP_URL_HOST); ?>)
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2>テストデータセット選択</h2>
            
            <div class="tab-buttons">
                <button class="tab-button active" onclick="switchTab('preset')">プリセットデータ</button>
                <button class="tab-button" onclick="switchTab('custom')">カスタムデータ</button>
            </div>
            
            <div id="preset-tab" class="tab-content active">
                <select id="dataSetSelect" onchange="updatePreview()">
                    <option value="normal">通常の注文（請求先と配送先が同じ）</option>
                    <option value="different_address">配送先が異なる注文</option>
                    <option value="international">国際配送の注文</option>
                </select>
                
                <div id="dataPreview" class="data-preview"></div>
            </div>
            
            <div id="custom-tab" class="tab-content">
                <p>JSON形式でカスタムデータを入力してください:</p>
                <textarea id="customDataInput" placeholder='{"transactionId": "CUSTOM-001", "email": "custom@example.com", ...}'></textarea>
                <button class="secondary" onclick="validateCustomData()">JSON検証</button>
                <button class="secondary" onclick="loadSampleData()">サンプルデータを読み込む</button>
            </div>
        </div>

        <div class="button-group">
            <h3>送信アクション</h3>
            <button onclick="sendData('preview')">📋 プレビューのみ</button>
            <button class="success" onclick="sendData('google_sheets')">📊 Googleスプレッドシートに送信</button>
            <button class="success" onclick="sendData('external_system')">🔗 外部システムに送信</button>
            <button class="danger" onclick="clearResults()">🗑️ 結果をクリア</button>
        </div>

        <div id="result" class="result"></div>

        <div class="warning">
            <strong>⚠️ 注意事項:</strong>
            <ul>
                <li>これはテストツールです。実際の決済データは送信しないでください。</li>
                <li>送信されたデータは実際のシステムに記録される可能性があります。</li>
                <li>テスト後は必要に応じてテストデータを削除してください。</li>
            </ul>
        </div>
    </div>

    <script>
        // プリセットデータ
        const testDataSets = <?php echo json_encode($testDataSets, JSON_UNESCAPED_UNICODE); ?>;
        
        // タブ切り替え
        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            if (tab === 'preset') {
                document.querySelector('.tab-button:first-child').classList.add('active');
                document.getElementById('preset-tab').classList.add('active');
            } else {
                document.querySelector('.tab-button:last-child').classList.add('active');
                document.getElementById('custom-tab').classList.add('active');
            }
        }
        
        // プレビュー更新
        function updatePreview() {
            const dataSet = document.getElementById('dataSetSelect').value;
            const data = testDataSets[dataSet];
            document.getElementById('dataPreview').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }
        
        // カスタムデータ検証
        function validateCustomData() {
            const input = document.getElementById('customDataInput').value;
            try {
                const data = JSON.parse(input);
                alert('✓ 有効なJSONです');
                return true;
            } catch (e) {
                alert('✗ 無効なJSON: ' + e.message);
                return false;
            }
        }
        
        // サンプルデータ読み込み
        function loadSampleData() {
            const sample = testDataSets.normal;
            document.getElementById('customDataInput').value = JSON.stringify(sample, null, 2);
        }
        
        // データ送信
        function sendData(action) {
            const isCustom = document.getElementById('custom-tab').classList.contains('active');
            let data = {};
            
            if (isCustom) {
                const customData = document.getElementById('customDataInput').value;
                if (!customData) {
                    alert('カスタムデータを入力してください');
                    return;
                }
                if (!validateCustomData()) return;
                data.custom_data = customData;
            } else {
                data.data_set = document.getElementById('dataSetSelect').value;
            }
            
            data.action = action;
            
            // 送信
            fetch('test-data-sender.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(result => {
                const resultDiv = document.getElementById('result');
                resultDiv.className = 'result ' + (result.success ? 'success' : 'error');
                resultDiv.style.display = 'block';
                
                let html = '<h3>' + (result.success ? '✓ ' : '✗ ') + result.message + '</h3>';
                if (result.data) {
                    html += '<h4>送信データ:</h4>';
                    html += '<pre>' + JSON.stringify(result.data, null, 2) + '</pre>';
                }
                
                resultDiv.innerHTML = html;
            })
            .catch(error => {
                const resultDiv = document.getElementById('result');
                resultDiv.className = 'result error';
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>✗ エラーが発生しました</h3><p>' + error + '</p>';
            });
        }
        
        // 結果クリア
        function clearResults() {
            document.getElementById('result').style.display = 'none';
            document.getElementById('result').innerHTML = '';
        }
        
        // 初期化
        updatePreview();
    </script>
</body>
</html>