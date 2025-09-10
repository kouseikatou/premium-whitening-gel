<?php
/**
 * 決済システム状態確認ページ
 * 現在の環境設定とスプレッドシート連携状況を確認
 */

require_once 'config.php';
require_once 'google-sheets-complete.php';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済システム状態確認</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        .status-section {
            margin: 25px 0;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #3498db;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .status-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .status-value {
            font-family: monospace;
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .status-indicator {
            font-size: 18px;
            margin-left: 10px;
        }
        .test-mode {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-left-color: #f39c12;
        }
        .production-mode {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left-color: #27ae60;
        }
        .error-mode {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-left-color: #e74c3c;
        }
        .success { color: #27ae60; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        .info { color: #3498db; }
        
        .environment-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        .badge-sandbox {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        .badge-production {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .test-section {
            background: #e7f3ff;
            border: 1px solid #bee5eb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-warning { background: #f39c12; }
        
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        
        .recent-transactions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 決済システム状態確認</h1>
        
        <!-- 環境設定確認 -->
        <div class="status-section <?php echo (AUTHORIZENET_ENVIRONMENT === 'SANDBOX') ? 'test-mode' : 'production-mode'; ?>">
            <h2>🌍 決済環境設定</h2>
            
            <div class="status-item">
                <div class="status-label">現在の環境:</div>
                <div>
                    <span class="environment-badge <?php echo (AUTHORIZENET_ENVIRONMENT === 'SANDBOX') ? 'badge-sandbox' : 'badge-production'; ?>">
                        <?php echo AUTHORIZENET_ENVIRONMENT; ?>
                    </span>
                    <span class="status-indicator">
                        <?php if (AUTHORIZENET_ENVIRONMENT === 'SANDBOX'): ?>
                            🧪 <span class="warning">テストモード</span>
                        <?php else: ?>
                            🔴 <span class="error">本番モード</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="status-item">
                <div class="status-label">API Login ID:</div>
                <div class="status-value"><?php echo substr(AUTHORIZENET_LOGIN_ID, 0, 3) . '***' . substr(AUTHORIZENET_LOGIN_ID, -2); ?></div>
            </div>
            
            <div class="status-item">
                <div class="status-label">Transaction Key:</div>
                <div class="status-value">***<?php echo substr(AUTHORIZENET_TRANSACTION_KEY, -4); ?></div>
            </div>
            
            <div class="status-item">
                <div class="status-label">Client Key:</div>
                <div class="status-value"><?php echo substr(AUTHORIZENET_CLIENT_KEY, 0, 8) . '***' . substr(AUTHORIZENET_CLIENT_KEY, -8); ?></div>
            </div>
            
            <?php if (AUTHORIZENET_ENVIRONMENT === 'SANDBOX'): ?>
                <div style="background: #fff3cd; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <strong>🧪 テストモードで動作中</strong><br>
                    • 実際の決済は行われません<br>
                    • テスト用クレジットカードのみ使用可能<br>
                    • 開発・テスト用途に最適
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <strong>🔴 本番モードで動作中</strong><br>
                    • 実際の決済が行われます<br>
                    • 実際のクレジットカードが必要<br>
                    • 本番運用中
                </div>
            <?php endif; ?>
        </div>

        <!-- Google Sheets連携状況 -->
        <div class="status-section">
            <h2>📊 Google Sheets連携状況</h2>
            
            <div class="status-item">
                <div class="status-label">スプレッドシートID:</div>
                <div class="status-value">
                    <?php 
                    if (GOOGLE_SPREADSHEET_ID === 'YOUR_SPREADSHEET_ID_HERE') {
                        echo '<span class="error">未設定</span>';
                    } else {
                        echo substr(GOOGLE_SPREADSHEET_ID, 0, 8) . '***' . substr(GOOGLE_SPREADSHEET_ID, -8);
                    }
                    ?>
                </div>
                <span class="status-indicator">
                    <?php if (GOOGLE_SPREADSHEET_ID !== 'YOUR_SPREADSHEET_ID_HERE'): ?>
                        <span class="success">✅</span>
                    <?php else: ?>
                        <span class="error">❌</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="status-item">
                <div class="status-label">Apps Script URL:</div>
                <div class="status-value">
                    <?php 
                    if (GOOGLE_APPS_SCRIPT_URL === 'YOUR_APPS_SCRIPT_URL_HERE') {
                        echo '<span class="error">未設定</span>';
                    } else {
                        echo '設定済み';
                    }
                    ?>
                </div>
                <span class="status-indicator">
                    <?php if (GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE'): ?>
                        <span class="success">✅</span>
                    <?php else: ?>
                        <span class="error">❌</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="status-item">
                <div class="status-label">シート名:</div>
                <div class="status-value"><?php echo GOOGLE_SHEETS_SHEET_NAME; ?></div>
                <span class="status-indicator"><span class="success">✅</span></span>
            </div>
            
            <?php 
            $sheetsConfigured = (GOOGLE_SPREADSHEET_ID !== 'YOUR_SPREADSHEET_ID_HERE' && 
                                GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE');
            ?>
            
            <div style="background: <?php echo $sheetsConfigured ? '#d4edda' : '#f8d7da'; ?>; padding: 15px; border-radius: 6px; margin-top: 15px;">
                <?php if ($sheetsConfigured): ?>
                    <strong class="success">✅ Google Sheets連携: 設定完了</strong><br>
                    決済成功時にスプレッドシートへデータが送信されます
                <?php else: ?>
                    <strong class="error">❌ Google Sheets連携: 設定不完了</strong><br>
                    スプレッドシートへのデータ送信が行われません
                <?php endif; ?>
            </div>
        </div>

        <!-- 決済テスト情報 -->
        <?php if (AUTHORIZENET_ENVIRONMENT === 'SANDBOX'): ?>
        <div class="test-section">
            <h3>🧪 テスト用クレジットカード情報</h3>
            <div class="code-block">テスト用Visa:     4111111111111111
テスト用Mastercard: 5555555555554444
テスト用AmEx:      378282246310005

有効期限: 12/25 (将来の日付)
CVV: 123 (AmExは1234)
名前: Test User</div>
            <p><strong>注意:</strong> これらのカードは決済処理されますが、実際の請求は発生しません。</p>
        </div>
        <?php endif; ?>

        <!-- アクションボタン -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-success">決済フォームでテスト</a>
            <a href="setup-test.php" class="btn">詳細テスト実行</a>
            <?php if ($sheetsConfigured): ?>
                <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" class="btn" target="_blank">スプレッドシートを開く</a>
            <?php endif; ?>
            <a href="test-tools/sheets/test-data-sender.php" class="btn btn-warning">データ送信テスト</a>
        </div>

        <!-- システム情報 -->
        <div class="status-section" style="margin-top: 30px;">
            <h3>🔧 システム情報</h3>
            <div class="status-item">
                <div class="status-label">PHP バージョン:</div>
                <div class="status-value"><?php echo PHP_VERSION; ?></div>
            </div>
            <div class="status-item">
                <div class="status-label">cURL サポート:</div>
                <div class="status-value">
                    <?php echo function_exists('curl_init') ? '有効' : '無効'; ?>
                </div>
                <span class="status-indicator">
                    <?php echo function_exists('curl_init') ? '<span class="success">✅</span>' : '<span class="error">❌</span>'; ?>
                </span>
            </div>
            <div class="status-item">
                <div class="status-label">タイムゾーン:</div>
                <div class="status-value"><?php echo date_default_timezone_get(); ?></div>
            </div>
            <div class="status-item">
                <div class="status-label">現在時刻:</div>
                <div class="status-value"><?php echo date('Y-m-d H:i:s'); ?></div>
            </div>
        </div>
    </div>
</body>
</html>