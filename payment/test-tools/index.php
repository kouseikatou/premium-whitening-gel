<?php
/**
 * テストツール統合ポータル
 * 
 * 全てのテストツールへのアクセスポイント
 */

require_once '../config.php';

// 環境確認
$apiEnvironment = AUTHORIZENET_ENVIRONMENT;
$sheetsConfigured = !empty(GOOGLE_SPREADSHEET_ID) && !empty(GOOGLE_APPS_SCRIPT_URL);
$paymentConfigured = !empty(\SampleCodeConstants::MERCHANT_LOGIN_ID) && !empty(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テストツール統合ポータル</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 3em; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .header p { font-size: 1.2em; margin: 10px 0; opacity: 0.9; }
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin: 40px 0; }
        .tool-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .tool-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.3); }
        .tool-icon { font-size: 4em; text-align: center; margin-bottom: 20px; }
        .tool-title { font-size: 1.8em; font-weight: bold; color: #2c3e50; margin-bottom: 15px; text-align: center; }
        .tool-description { color: #7f8c8d; line-height: 1.6; margin-bottom: 20px; text-align: center; }
        .tool-features { list-style: none; padding: 0; margin: 20px 0; }
        .tool-features li { padding: 8px 0; border-bottom: 1px solid #ecf0f1; }
        .tool-features li:last-child { border-bottom: none; }
        .tool-features li::before { content: "✓ "; color: #27ae60; font-weight: bold; }
        .tool-status { display: flex; align-items: center; justify-content: center; margin: 20px 0; padding: 10px; border-radius: 8px; font-weight: bold; }
        .status-ready { background: #d4edda; color: #155724; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-error { background: #f8d7da; color: #721c24; }
        .btn { display: block; text-align: center; padding: 15px 20px; border: none; border-radius: 8px; color: white; text-decoration: none; font-size: 1.1em; font-weight: bold; margin: 10px 0; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(45deg, #3498db, #2980b9); }
        .btn-primary:hover { background: linear-gradient(45deg, #2980b9, #21618c); }
        .btn-success { background: linear-gradient(45deg, #27ae60, #229954); }
        .btn-success:hover { background: linear-gradient(45deg, #229954, #1e7e34); }
        .btn-disabled { background: #95a5a6; cursor: not-allowed; }
        .environment-info { background: white; border-radius: 15px; padding: 20px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .environment-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; margin: 0 10px; }
        .env-production { background: #e74c3c; color: white; }
        .env-sandbox { background: #27ae60; color: white; }
        .info-section { background: white; border-radius: 15px; padding: 30px; margin: 30px 0; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .info-item { text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px; }
        .info-item h3 { margin: 0 0 10px 0; color: #2c3e50; }
        .info-item p { margin: 0; color: #7f8c8d; }
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .quick-link { background: #ecf0f1; padding: 15px; border-radius: 8px; text-align: center; }
        .quick-link a { color: #2c3e50; text-decoration: none; font-weight: bold; }
        .quick-link a:hover { color: #3498db; }
        .footer { text-align: center; color: white; margin-top: 50px; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 テストツール統合ポータル</h1>
            <p>決済システムとGoogleスプレッドシート連携のテストツール</p>
        </div>

        <!-- 環境情報 -->
        <div class="environment-info">
            <h2 style="margin: 0 0 15px 0; color: #2c3e50;">🌍 現在の環境</h2>
            <div style="text-align: center;">
                <span class="environment-badge <?php echo $apiEnvironment === 'PRODUCTION' ? 'env-production' : 'env-sandbox'; ?>">
                    <?php echo $apiEnvironment; ?> 環境
                </span>
                <?php if ($apiEnvironment === 'PRODUCTION'): ?>
                    <p style="color: #e74c3c; margin: 10px 0;"><strong>⚠️ 本番環境です。テストカードは使用できません。</strong></p>
                <?php else: ?>
                    <p style="color: #27ae60; margin: 10px 0;"><strong>✅ テスト環境です。テストカードが使用できます。</strong></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- テストツール -->
        <div class="tools-grid">
            <!-- Googleスプレッドシートテスト -->
            <div class="tool-card">
                <div class="tool-icon">📊</div>
                <div class="tool-title">Google Sheets テストスイート</div>
                <div class="tool-description">
                    Googleスプレッドシートとの連携機能をテストします
                </div>
                
                <ul class="tool-features">
                    <li>スプレッドシート設定の検証</li>
                    <li>Apps Script連携テスト</li>
                    <li>データ送信テスト（単発・バルク）</li>
                    <li>読み取り機能のテスト</li>
                </ul>
                
                <div class="tool-status <?php echo $sheetsConfigured ? 'status-ready' : 'status-warning'; ?>">
                    <?php if ($sheetsConfigured): ?>
                        ✅ 設定完了 - テスト可能
                    <?php else: ?>
                        ⚠️ 設定確認が必要
                    <?php endif; ?>
                </div>
                
                <a href="sheets/sheets-test-suite.php" class="btn <?php echo $sheetsConfigured ? 'btn-success' : 'btn-disabled'; ?>">
                    📊 Sheets テストを開始
                </a>
            </div>

            <!-- 決済システムテスト -->
            <div class="tool-card">
                <div class="tool-icon">💳</div>
                <div class="tool-title">決済システム テストスイート</div>
                <div class="tool-description">
                    Authorize.Net決済システムの機能をテストします
                </div>
                
                <ul class="tool-features">
                    <li>API認証のテスト</li>
                    <li>環境設定の確認</li>
                    <li>クレジットカード番号検証</li>
                    <li>CLIENT_KEY管理</li>
                </ul>
                
                <div class="tool-status <?php echo $paymentConfigured ? 'status-ready' : 'status-error'; ?>">
                    <?php if ($paymentConfigured): ?>
                        ✅ 設定完了 - テスト可能
                    <?php else: ?>
                        ❌ 認証情報が未設定
                    <?php endif; ?>
                </div>
                
                <a href="payment/payment-test-suite.php" class="btn <?php echo $paymentConfigured ? 'btn-primary' : 'btn-disabled'; ?>">
                    💳 決済テストを開始
                </a>
            </div>
        </div>

        <!-- システム情報 -->
        <div class="info-section">
            <h2 style="margin: 0 0 20px 0; color: #2c3e50; text-align: center;">📋 システム情報</h2>
            <div class="info-grid">
                <div class="info-item">
                    <h3>🌍 環境</h3>
                    <p><?php echo $apiEnvironment; ?></p>
                </div>
                <div class="info-item">
                    <h3>📊 スプレッドシート</h3>
                    <p><?php echo $sheetsConfigured ? '設定済み' : '未設定'; ?></p>
                </div>
                <div class="info-item">
                    <h3>💳 決済システム</h3>
                    <p><?php echo $paymentConfigured ? '設定済み' : '未設定'; ?></p>
                </div>
                <div class="info-item">
                    <h3>🔧 設定ファイル</h3>
                    <p>config.php</p>
                </div>
            </div>
        </div>

        <!-- クイックリンク -->
        <div class="info-section">
            <h2 style="margin: 0 0 20px 0; color: #2c3e50; text-align: center;">🔗 クイックリンク</h2>
            <div class="quick-links">
                <div class="quick-link">
                    <a href="../index.php" target="_blank">💳 決済フォーム</a>
                </div>
                <div class="quick-link">
                    <a href="../config.php" target="_blank">⚙️ 設定ファイル</a>
                </div>
                <div class="quick-link">
                    <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" target="_blank">📊 スプレッドシート</a>
                </div>
                <div class="quick-link">
                    <a href="https://account.authorize.net/" target="_blank">🏦 Authorize.Net</a>
                </div>
                <div class="quick-link">
                    <a href="https://script.google.com/" target="_blank">📝 Apps Script</a>
                </div>
                <div class="quick-link">
                    <a href="../data-export-sample.php" target="_blank">📄 データサンプル</a>
                </div>
            </div>
        </div>

        <!-- 使用方法 -->
        <div class="info-section">
            <h2 style="margin: 0 0 20px 0; color: #2c3e50; text-align: center;">📖 使用方法</h2>
            <div style="color: #7f8c8d; line-height: 1.8;">
                <h3 style="color: #2c3e50;">1. 初回セットアップ</h3>
                <ul>
                    <li>config.phpで各種APIキーを設定</li>
                    <li>Google Apps Scriptをデプロイ</li>
                    <li>スプレッドシートにヘッダー行を設定</li>
                </ul>

                <h3 style="color: #2c3e50;">2. テスト実行</h3>
                <ul>
                    <li>各テストスイートで設定を確認</li>
                    <li>段階的にテストを実行</li>
                    <li>エラーが発生した場合は設定を見直し</li>
                </ul>

                <h3 style="color: #2c3e50;">3. 本番運用</h3>
                <ul>
                    <li>すべてのテストが正常に完了後</li>
                    <li>決済フォームで実際の運用開始</li>
                    <li>定期的なテストの実行を推奨</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>💻 Authorize.Net + Google Sheets 統合システム</p>
            <p>🔧 テスト環境: <?php echo $apiEnvironment; ?></p>
        </div>
    </div>
</body>
</html>