<?php
/**
 * CLIENT_KEY検証ツール
 * Authorize.Net Accept.jsのCLIENT_KEYが正しく設定されているかテストします
 */

require_once 'config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CLIENT_KEY検証ツール</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 0; }
        button:hover { background: #0056b3; }
        #testArea { margin-top: 20px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Authorize.Net CLIENT_KEY検証ツール</h1>
    
    <div class="info">
        <strong>現在の設定:</strong><br>
        環境: <?php echo AUTHORIZENET_ENVIRONMENT; ?><br>
        LOGIN_ID: <?php echo substr(AUTHORIZENET_LOGIN_ID, 0, 4) . str_repeat('*', strlen(AUTHORIZENET_LOGIN_ID) - 4); ?><br>
        CLIENT_KEY: <?php echo substr(AUTHORIZENET_CLIENT_KEY, 0, 10) . str_repeat('*', strlen(AUTHORIZENET_CLIENT_KEY) - 10); ?> (長さ: <?php echo strlen(AUTHORIZENET_CLIENT_KEY); ?>)
    </div>

    <?php if (AUTHORIZENET_ENVIRONMENT === 'SANDBOX'): ?>
    <script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script>
    <?php else: ?>
    <script type="text/javascript" src="https://js.authorize.net/v1/Accept.js" charset="utf-8"></script>
    <?php endif; ?>

    <div id="testArea">
        <h2>Accept.js認証テスト</h2>
        <button onclick="testClientKey()">CLIENT_KEYをテスト</button>
        <div id="testResults"></div>
    </div>

    <script>
        function testClientKey() {
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.innerHTML = '<p>テスト中...</p>';
            
            // テスト用の認証データ
            const authData = {
                clientKey: "<?php echo AUTHORIZENET_CLIENT_KEY; ?>",
                apiLoginID: "<?php echo AUTHORIZENET_LOGIN_ID; ?>"
            };
            
            // テスト用のダミーカードデータ（Accept.jsが認証情報を検証するために必要）
            const cardData = {
                cardNumber: "4111111111111111", // Visaテストカード
                month: "12",
                year: "2025",
                cardCode: "123"
            };
            
            const secureData = {
                authData: authData,
                cardData: cardData
            };
            
            console.log('Testing with:', {
                environment: '<?php echo AUTHORIZENET_ENVIRONMENT; ?>',
                loginID: authData.apiLoginID,
                clientKeyLength: authData.clientKey.length
            });
            
            Accept.dispatchData(secureData, function(response) {
                let resultHTML = '';
                
                if (response.messages.resultCode === "Error") {
                    resultHTML += '<div class="error"><strong>❌ エラー:</strong><br>';
                    
                    for (let i = 0; i < response.messages.message.length; i++) {
                        const msg = response.messages.message[i];
                        resultHTML += `エラーコード: ${msg.code}<br>`;
                        resultHTML += `メッセージ: ${msg.text}<br>`;
                        
                        // 具体的なエラー分析
                        if (msg.code === 'E_WC_21') {
                            resultHTML += '<strong>原因:</strong> 認証に失敗しました。LOGIN_IDまたはCLIENT_KEYが正しくない可能性があります。<br>';
                        } else if (msg.code === 'E_WC_19') {
                            resultHTML += '<strong>原因:</strong> 環境の不一致。サンドボックス認証情報を本番環境で使用している、またはその逆の可能性があります。<br>';
                        } else if (msg.code === 'E_WC_14') {
                            resultHTML += '<strong>原因:</strong> Accept.js暗号化に失敗。CLIENT_KEYが無効または期限切れの可能性があります。<br>';
                        }
                    }
                    
                    resultHTML += '</div>';
                } else {
                    resultHTML += '<div class="success"><strong>✅ 成功:</strong> CLIENT_KEYとLOGIN_IDが正しく設定されています！<br>';
                    resultHTML += `データ記述子: ${response.opaqueData.dataDescriptor}<br>`;
                    resultHTML += `データ値の長さ: ${response.opaqueData.dataValue.length}文字</div>`;
                }
                
                // 追加情報
                resultHTML += '<div class="info"><strong>テスト詳細:</strong><br>';
                resultHTML += `環境: <?php echo AUTHORIZENET_ENVIRONMENT; ?><br>`;
                resultHTML += `Accept.js URL: ${document.querySelector('script[src*="Accept.js"]').src}<br>`;
                resultHTML += `CLIENT_KEY長さ: ${authData.clientKey.length}<br>`;
                resultHTML += '</div>';
                
                resultsDiv.innerHTML = resultHTML;
                
                console.log('Full response:', response);
            });
        }
        
        // 自動実行（ページ読み込み時）
        window.onload = function() {
            console.log('Accept.js loaded, ready for testing');
        };
    </script>

    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h3>トラブルシューティング</h3>
        <ul>
            <li><strong>E_WC_21 (認証失敗):</strong> LOGIN_IDまたはCLIENT_KEYが間違っています</li>
            <li><strong>E_WC_19 (環境不一致):</strong> サンドボックス認証情報を本番で使用、またはその逆</li>
            <li><strong>E_WC_14 (暗号化失敗):</strong> CLIENT_KEYが無効または期限切れ</li>
            <li><strong>成功:</strong> 認証情報が正しく、Accept.jsが正常に動作しています</li>
        </ul>
    </div>

    <p style="margin-top: 30px; font-size: 12px; color: #666;">このファイルはテスト完了後に削除してください。</p>
</body>
</html>