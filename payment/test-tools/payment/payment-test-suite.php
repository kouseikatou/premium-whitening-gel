<?php
/**
 * 決済システム テストスイート
 * 
 * Authorize.Net決済システムの全テスト機能をまとめたツール
 */

require_once '../../config.php';
require_once '../../autoload.php';
require_once '../../SampleCodeConstants.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

// エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Luhn algorithm for credit card validation
function luhnCheck($cardNumber) {
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    $sum = 0;
    $alternate = false;
    
    for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
        $digit = intval($cardNumber[$i]);
        
        if ($alternate) {
            $digit *= 2;
            if ($digit > 9) {
                $digit = ($digit % 10) + 1;
            }
        }
        
        $sum += $digit;
        $alternate = !$alternate;
    }
    
    return ($sum % 10) === 0;
}

// テスト用カード番号
$testCards = [
    'visa' => [
        'number' => '4111111111111111',
        'name' => 'Visa Test Card',
        'cvv' => '123',
        'expiry' => '12/25'
    ],
    'mastercard' => [
        'number' => '5555555555554444',
        'name' => 'MasterCard Test Card', 
        'cvv' => '123',
        'expiry' => '12/25'
    ],
    'amex' => [
        'number' => '378282246310005',
        'name' => 'American Express Test Card',
        'cvv' => '1234',
        'expiry' => '12/25'
    ],
    'discover' => [
        'number' => '6011111111111117',
        'name' => 'Discover Test Card',
        'cvv' => '123',
        'expiry' => '12/25'
    ]
];

// 環境設定
$apiEnvironment = AUTHORIZENET_ENVIRONMENT;
$environment = ($apiEnvironment === 'PRODUCTION') ? 
               \net\authorize\api\constants\ANetEnvironment::PRODUCTION : 
               \net\authorize\api\constants\ANetEnvironment::SANDBOX;

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $result = ['success' => false, 'message' => '', 'data' => []];
    
    switch ($action) {
        case 'test_auth':
            // 認証テスト
            try {
                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
                $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
                
                $request = new AnetAPI\GetMerchantDetailsRequest();
                $request->setMerchantAuthentication($merchantAuthentication);
                
                $controller = new AnetController\GetMerchantDetailsController($request);
                $response = $controller->executeWithApiResponse($environment);
                
                if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
                    $result['success'] = true;
                    $result['message'] = '認証テスト成功';
                    $result['data'] = [
                        'merchant_name' => $response->getMerchantName(),
                        'gateway_id' => $response->getGatewayId(),
                        'public_client_key' => $response->getPublicClientKey()
                    ];
                } else {
                    $result['message'] = '認証失敗: ' . ($response ? $response->getMessages()->getMessage()[0]->getText() : '応答なし');
                }
            } catch (Exception $e) {
                $result['message'] = '認証エラー: ' . $e->getMessage();
            }
            break;
            
        case 'validate_card':
            // カード番号検証
            $cardNumber = $_POST['card_number'] ?? '';
            $cardNumber = preg_replace('/\D/', '', $cardNumber);
            
            if (empty($cardNumber)) {
                $result['message'] = 'カード番号を入力してください';
            } else {
                $isValid = luhnCheck($cardNumber);
                $cardType = '';
                
                if (preg_match('/^4/', $cardNumber)) $cardType = 'Visa';
                elseif (preg_match('/^5[1-5]/', $cardNumber)) $cardType = 'MasterCard';
                elseif (preg_match('/^3[47]/', $cardNumber)) $cardType = 'American Express';
                elseif (preg_match('/^6011/', $cardNumber)) $cardType = 'Discover';
                else $cardType = 'Unknown';
                
                $result['success'] = $isValid;
                $result['message'] = $isValid ? 'カード番号は有効です' : 'カード番号が無効です';
                $result['data'] = [
                    'card_number' => $cardNumber,
                    'card_type' => $cardType,
                    'luhn_valid' => $isValid,
                    'length' => strlen($cardNumber)
                ];
            }
            break;
            
        case 'test_environment':
            // 環境テスト
            $result['success'] = true;
            $result['message'] = '環境設定を確認しました';
            $result['data'] = [
                'current_environment' => $apiEnvironment,
                'endpoint' => $environment === \net\authorize\api\constants\ANetEnvironment::PRODUCTION ? 'PRODUCTION' : 'SANDBOX',
                'login_id' => \SampleCodeConstants::MERCHANT_LOGIN_ID,
                'login_id_length' => strlen(\SampleCodeConstants::MERCHANT_LOGIN_ID),
                'transaction_key_length' => strlen(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY),
                'client_key' => AUTHORIZENET_CLIENT_KEY,
                'client_key_length' => strlen(AUTHORIZENET_CLIENT_KEY),
                'expected_client_key_length' => 80
            ];
            break;
            
        case 'get_client_key':
            // CLIENT_KEY取得
            try {
                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
                $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
                
                $request = new AnetAPI\GetMerchantDetailsRequest();
                $request->setMerchantAuthentication($merchantAuthentication);
                
                $controller = new AnetController\GetMerchantDetailsController($request);
                $response = $controller->executeWithApiResponse($environment);
                
                if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
                    $newClientKey = $response->getPublicClientKey();
                    $result['success'] = true;
                    $result['message'] = '新しいCLIENT_KEYを取得しました';
                    $result['data'] = [
                        'current_client_key' => AUTHORIZENET_CLIENT_KEY,
                        'new_client_key' => $newClientKey,
                        'key_changed' => (AUTHORIZENET_CLIENT_KEY !== $newClientKey)
                    ];
                } else {
                    $result['message'] = 'CLIENT_KEY取得失敗: ' . ($response ? $response->getMessages()->getMessage()[0]->getText() : '応答なし');
                }
            } catch (Exception $e) {
                $result['message'] = 'CLIENT_KEY取得エラー: ' . $e->getMessage();
            }
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 設定状態の確認
$configStatus = [
    'environment' => !empty(AUTHORIZENET_ENVIRONMENT),
    'login_id' => !empty(\SampleCodeConstants::MERCHANT_LOGIN_ID),
    'transaction_key' => !empty(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY),
    'client_key' => !empty(AUTHORIZENET_CLIENT_KEY) && strlen(AUTHORIZENET_CLIENT_KEY) === 80
];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>決済システム テストスイート</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-header { background: #2c3e50; color: white; padding: 15px 20px; font-weight: bold; font-size: 18px; }
        .section-content { padding: 20px; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .status-item { padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; }
        .status-ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
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
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0; }
        .card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; }
        .card h4 { margin: 0 0 10px 0; color: #495057; }
        .card-number { font-family: monospace; font-size: 18px; letter-spacing: 2px; margin: 10px 0; }
        .card-info { font-size: 14px; color: #6c757d; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: none; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .environment-badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .env-production { background: #dc3545; color: white; }
        .env-sandbox { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                💳 決済システム テストスイート
                <span class="environment-badge <?php echo $apiEnvironment === 'PRODUCTION' ? 'env-production' : 'env-sandbox'; ?>">
                    <?php echo $apiEnvironment; ?>
                </span>
            </h1>
            <p>Authorize.Net決済システムの機能をテストするための統合ツールです。</p>
        </div>

        <!-- 設定状態 -->
        <div class="section">
            <div class="section-header">⚙️ 設定状態</div>
            <div class="section-content">
                <div class="status-grid">
                    <div class="status-item <?php echo $configStatus['environment'] ? 'status-ok' : 'status-error'; ?>">
                        <div>🌍 環境設定</div>
                        <div><?php echo $configStatus['environment'] ? "✓ {$apiEnvironment}" : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['login_id'] ? 'status-ok' : 'status-error'; ?>">
                        <div>🔑 ログインID</div>
                        <div><?php echo $configStatus['login_id'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['transaction_key'] ? 'status-ok' : 'status-error'; ?>">
                        <div>🗝️ トランザクションキー</div>
                        <div><?php echo $configStatus['transaction_key'] ? '✓ 設定済み' : '✗ 未設定'; ?></div>
                    </div>
                    <div class="status-item <?php echo $configStatus['client_key'] ? 'status-ok' : 'status-warning'; ?>">
                        <div>🎫 クライアントキー</div>
                        <div><?php echo $configStatus['client_key'] ? '✓ 設定済み (80文字)' : '⚠ 要確認'; ?></div>
                    </div>
                </div>
                
                <table class="config-table">
                    <tr>
                        <th>設定項目</th>
                        <th>現在の値</th>
                        <th>ステータス</th>
                    </tr>
                    <tr>
                        <td>環境</td>
                        <td><code><?php echo $apiEnvironment; ?></code></td>
                        <td><?php echo $apiEnvironment === 'PRODUCTION' ? '🔴 本番環境' : '🟢 テスト環境'; ?></td>
                    </tr>
                    <tr>
                        <td>ログインID</td>
                        <td><code><?php echo substr(\SampleCodeConstants::MERCHANT_LOGIN_ID, 0, 8) . '...'; ?></code></td>
                        <td><?php echo strlen(\SampleCodeConstants::MERCHANT_LOGIN_ID) . ' 文字'; ?></td>
                    </tr>
                    <tr>
                        <td>トランザクションキー</td>
                        <td><code><?php echo substr(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY, 0, 8) . '...'; ?></code></td>
                        <td><?php echo strlen(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY) . ' 文字'; ?></td>
                    </tr>
                    <tr>
                        <td>クライアントキー</td>
                        <td><code><?php echo substr(AUTHORIZENET_CLIENT_KEY, 0, 20) . '...'; ?></code></td>
                        <td><?php echo strlen(AUTHORIZENET_CLIENT_KEY) . ' 文字 (' . (strlen(AUTHORIZENET_CLIENT_KEY) === 80 ? '正常' : '要確認') . ')'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 認証テスト -->
        <div class="section">
            <div class="section-header">🔐 認証テスト</div>
            <div class="section-content">
                <div class="button-grid">
                    <button class="btn btn-primary" onclick="runTest('test_auth')">🔐 認証テスト</button>
                    <button class="btn btn-info" onclick="runTest('test_environment')">🌍 環境確認</button>
                    <button class="btn btn-warning" onclick="runTest('get_client_key')">🔄 CLIENT_KEY取得</button>
                </div>
                
                <div class="info-box">
                    <strong>💡 認証テストについて:</strong>
                    <ul>
                        <li><strong>認証テスト</strong>: Authorize.Net APIへの接続と認証をテスト</li>
                        <li><strong>環境確認</strong>: 現在の設定値と環境情報を表示</li>
                        <li><strong>CLIENT_KEY取得</strong>: 最新のCLIENT_KEYを取得</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- カード検証 -->
        <div class="section">
            <div class="section-header">💳 カード番号検証</div>
            <div class="section-content">
                <div class="input-group">
                    <label for="cardNumber">カード番号:</label>
                    <input type="text" id="cardNumber" placeholder="4111111111111111" maxlength="19">
                </div>
                
                <div class="button-grid">
                    <button class="btn btn-success" onclick="validateCard()">✅ カード番号検証</button>
                    <button class="btn btn-info" onclick="clearCardField()">🗑️ クリア</button>
                </div>
            </div>
        </div>

        <!-- テストカード一覧 -->
        <div class="section">
            <div class="section-header">💳 テストカード一覧 (<?php echo $apiEnvironment === 'SANDBOX' ? 'SANDBOX用' : 'PRODUCTION非対応'; ?>)</div>
            <div class="section-content">
                <?php if ($apiEnvironment === 'SANDBOX'): ?>
                    <div class="card-grid">
                        <?php foreach ($testCards as $type => $card): ?>
                            <div class="card">
                                <h4><?php echo $card['name']; ?></h4>
                                <div class="card-number"><?php echo $card['number']; ?></div>
                                <div class="card-info">
                                    CVV: <?php echo $card['cvv']; ?> | 
                                    有効期限: <?php echo $card['expiry']; ?>
                                </div>
                                <button class="btn btn-info" onclick="copyCardNumber('<?php echo $card['number']; ?>')" style="margin-top: 10px; width: 100%;">
                                    📋 コピー
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="warning-box">
                        <strong>⚠️ 本番環境について:</strong>
                        <p>現在は本番環境(PRODUCTION)に設定されています。テストカードは使用できません。</p>
                        <p>テストを行う場合は、環境をSANDBOXに変更してください。</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 決済フォームリンク -->
        <div class="section">
            <div class="section-header">🔗 決済フォームテスト</div>
            <div class="section-content">
                <p>実際の決済フォームでテストを行う:</p>
                <div class="button-grid">
                    <a href="../../index.php" target="_blank" class="btn btn-primary">
                        💳 決済フォームを開く
                    </a>
                    <a href="../../authorize-credit-card.php" target="_blank" class="btn btn-warning">
                        ⚡ 決済処理ページ
                    </a>
                </div>
            </div>
        </div>

        <!-- 結果表示 -->
        <div id="result" class="result"></div>
        <div class="spinner" id="spinner"></div>
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
            
            fetch('payment-test-suite.php', {
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

        function validateCard() {
            const cardNumber = document.getElementById('cardNumber').value.replace(/\D/g, '');
            
            if (!cardNumber) {
                alert('カード番号を入力してください');
                return;
            }
            
            showSpinner();
            
            const formData = new FormData();
            formData.append('action', 'validate_card');
            formData.append('card_number', cardNumber);
            
            fetch('payment-test-suite.php', {
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

        function copyCardNumber(cardNumber) {
            navigator.clipboard.writeText(cardNumber).then(function() {
                alert('カード番号をコピーしました: ' + cardNumber);
            });
        }

        function clearCardField() {
            document.getElementById('cardNumber').value = '';
            document.getElementById('result').style.display = 'none';
        }

        // カード番号入力フォーマット
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = formattedValue;
        });

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            console.log('決済システム テストスイートが読み込まれました');
        });
    </script>
</body>
</html>