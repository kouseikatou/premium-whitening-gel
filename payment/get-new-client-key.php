<?php
/**
 * 新しいCLIENT_KEYを取得するツール
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
    <title>CLIENT_KEY情報取得</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔑 CLIENT_KEY情報取得</h1>
    
    <div class="info">
        <strong>現在の設定:</strong><br>
        環境: <?php echo $apiEnvironment; ?><br>
        Login ID: <?php echo substr(\SampleCodeConstants::MERCHANT_LOGIN_ID, 0, 4) . str_repeat('*', strlen(\SampleCodeConstants::MERCHANT_LOGIN_ID) - 4); ?><br>
        現在のCLIENT_KEY: <?php echo substr(AUTHORIZENET_CLIENT_KEY, 0, 10) . str_repeat('*', strlen(AUTHORIZENET_CLIENT_KEY) - 10); ?> (長さ: <?php echo strlen(AUTHORIZENET_CLIENT_KEY); ?>)
    </div>

    <?php if ($response != null): ?>
        <?php if ($response->getMessages()->getResultCode() == "Ok"): ?>
            <div class="success">
                ✅ 加盟店情報を正常に取得しました
            </div>
            
            <?php if ($response->getPublicClientKey()): ?>
                <div class="success">
                    <h3>🎉 新しいCLIENT_KEYが取得できました！</h3>
                    <div class="code">
                        <strong>新しいCLIENT_KEY:</strong><br>
                        <?php echo $response->getPublicClientKey(); ?>
                    </div>
                </div>
                
                <div class="warning">
                    <h3>⚠️ 設定の更新が必要です</h3>
                    <p>config.phpのCLIENT_KEYを以下の値に更新してください：</p>
                    <pre>define('AUTHORIZENET_CLIENT_KEY', '<?php echo $response->getPublicClientKey(); ?>');</pre>
                </div>
                
                <div class="info">
                    <h3>📋 更新手順</h3>
                    <ol>
                        <li>config.phpを開く</li>
                        <li>本番環境用CLIENT_KEYを上記の値に置き換える</li>
                        <li>ファイルを保存</li>
                        <li>決済フォームで再テスト</li>
                    </ol>
                </div>
                
            <?php else: ?>
                <div class="warning">
                    ⚠️ PUBLIC_CLIENT_KEYが取得できませんでした。
                    Authorize.Net管理画面で手動で生成する必要があります。
                </div>
            <?php endif; ?>
            
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
        <h3>🔧 手動でCLIENT_KEYを生成する方法</h3>
        <ol>
            <li><a href="https://account.authorize.net/" target="_blank">Authorize.Net管理画面</a>にログイン</li>
            <li>Account → Settings → Security Settings</li>
            <li>General Security Settings → Manage Public Client Key</li>
            <li>新しいキーを生成</li>
            <li>生成されたキーをconfig.phpに設定</li>
        </ol>
    </div>

    <div class="warning">
        <h3>🚨 E_WC_21エラーの一般的な原因</h3>
        <ul>
            <li><strong>CLIENT_KEYが無効または期限切れ</strong> - 新しいキーを生成</li>
            <li><strong>API Login IDが間違っている</strong> - 管理画面で確認</li>
            <li><strong>環境の不一致</strong> - サンドボックス認証情報を本番で使用</li>
            <li><strong>アカウントが無効</strong> - Authorize.Netサポートに連絡</li>
        </ul>
    </div>
    
    <p style="margin-top: 30px; font-size: 12px; color: #666;">
        このファイルは確認後に削除してください。
    </p>
</body>
</html>