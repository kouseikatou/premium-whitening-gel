<?php
/**
 * サポートされるカードタイプを確認するツール
 */

require_once 'config.php';
require_once 'autoload.php';
require_once 'SampleCodeConstants.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

// 環境設定
$apiEnvironment = AUTHORIZENET_ENVIRONMENT;
$environment = ($apiEnvironment === 'PRODUCTION') ? 
               \net\authorize\api\constants\ANetEnvironment::PRODUCTION : 
               \net\authorize\api\constants\ANetEnvironment::SANDBOX;

// 認証情報
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
$merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
$merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);

// GetMerchantDetailsリクエスト
$request = new AnetAPI\GetMerchantDetailsRequest();
$request->setMerchantAuthentication($merchantAuthentication);

// コントローラーでリクエスト実行
$controller = new AnetController\GetMerchantDetailsController($request);
$response = $controller->executeWithApiResponse($environment);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カードタイプ確認</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .enabled { color: #28a745; font-weight: bold; }
        .disabled { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 加盟店アカウント情報</h1>
        
        <div class="info">
            <strong>現在の設定:</strong><br>
            環境: <?php echo $apiEnvironment; ?><br>
            Login ID: <?php echo substr(\SampleCodeConstants::MERCHANT_LOGIN_ID, 0, 4) . str_repeat('*', strlen(\SampleCodeConstants::MERCHANT_LOGIN_ID) - 4); ?>
        </div>

        <?php if ($response != null): ?>
            <?php if ($response->getMessages()->getResultCode() == "Ok"): ?>
                <div class="success">
                    ✅ 加盟店情報を正常に取得しました
                </div>
                
                <h2>📋 アカウント詳細</h2>
                <table>
                    <tr>
                        <th>項目</th>
                        <th>値</th>
                    </tr>
                    <?php if ($response->getIsTestMode() !== null): ?>
                    <tr>
                        <td>テストモード</td>
                        <td><?php echo $response->getIsTestMode() ? '有効' : '無効'; ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($response->getProcessors() !== null): ?>
                    <tr>
                        <td colspan="2"><strong>プロセッサー情報</strong></td>
                    </tr>
                    <?php foreach ($response->getProcessors() as $processor): ?>
                    <tr>
                        <td>プロセッサー名</td>
                        <td><?php echo htmlspecialchars($processor->getName()); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($response->getMerchantName() !== null): ?>
                    <tr>
                        <td>加盟店名</td>
                        <td><?php echo htmlspecialchars($response->getMerchantName()); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($response->getGatewayId() !== null): ?>
                    <tr>
                        <td>ゲートウェイID</td>
                        <td><?php echo htmlspecialchars($response->getGatewayId()); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <h2>💳 サポートされるカードタイプのテスト</h2>
                <p>以下のテストカードで各カードタイプのサポート状況を確認できます：</p>
                
                <table>
                    <tr>
                        <th>カードタイプ</th>
                        <th>テスト番号</th>
                        <th>状態</th>
                        <th>備考</th>
                    </tr>
                    <tr>
                        <td>Visa</td>
                        <td>4111111111111111</td>
                        <td class="enabled">通常利用可能</td>
                        <td>最も一般的</td>
                    </tr>
                    <tr>
                        <td>Mastercard</td>
                        <td>5555555555554444</td>
                        <td class="enabled">通常利用可能</td>
                        <td>広く受け入れられている</td>
                    </tr>
                    <tr>
                        <td>American Express</td>
                        <td>370000000000002</td>
                        <td class="disabled">エラー17発生</td>
                        <td>要設定・要契約</td>
                    </tr>
                    <tr>
                        <td>Discover</td>
                        <td>6011111111111117</td>
                        <td>未確認</td>
                        <td>地域により制限あり</td>
                    </tr>
                </table>

                <div class="warning">
                    <strong>⚠️ American Express エラー17の解決方法:</strong><br>
                    1. 加盟店サービスプロバイダー（銀行・決済代行会社）に連絡<br>
                    2. American Express処理の有効化を依頼<br>
                    3. Authorize.Net管理画面で設定を有効化<br>
                    4. 追加手数料の確認
                </div>

            <?php else: ?>
                <div class="error">
                    ❌ 加盟店情報の取得に失敗しました<br>
                    <?php
                    $errorMessages = $response->getMessages()->getMessage();
                    foreach ($errorMessages as $errorMessage) {
                        echo "エラー " . $errorMessage->getCode() . ": " . $errorMessage->getText() . "<br>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="error">
                ❌ APIからの応答がありません
            </div>
        <?php endif; ?>

        <div class="info">
            <h3>📞 次のステップ</h3>
            <ol>
                <li><strong>加盟店サービスプロバイダーに連絡</strong><br>
                    銀行またはクレジットカード処理会社にAmex有効化を依頼</li>
                <li><strong>Authorize.Net管理画面で設定</strong><br>
                    Account → Merchant Profile → Payment Methods</li>
                <li><strong>テスト実行</strong><br>
                    Visaカードで正常動作を確認後、Amexでテスト</li>
            </ol>
        </div>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666;">
            このファイルは確認後に削除してください。
        </p>
    </div>
</body>
</html>