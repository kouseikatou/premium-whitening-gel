<?php
/**
 * 決済システム管理ダッシュボード
 * すべての機能へのリンク集とシステム概要
 */

require_once 'config.php';

// 現在の環境確認
$isTestMode = (AUTHORIZENET_ENVIRONMENT === 'SANDBOX');
$sheetsConfigured = (GOOGLE_SPREADSHEET_ID !== 'YOUR_SPREADSHEET_ID_HERE' && 
                    GOOGLE_APPS_SCRIPT_URL !== 'YOUR_APPS_SCRIPT_URL_HERE');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済システム管理ダッシュボード</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        .status-bar {
            background: <?php echo $isTestMode ? '#fff3cd' : '#d4edda'; ?>;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid <?php echo $isTestMode ? '#f39c12' : '#27ae60'; ?>;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-test {
            background: #f39c12;
            color: white;
        }
        
        .badge-prod {
            background: #27ae60;
            color: white;
        }
        
        .badge-connected {
            background: #3498db;
            color: white;
        }
        
        .badge-disconnected {
            background: #e74c3c;
            color: white;
        }
        
        .main-content {
            background: white;
            padding: 0;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 0;
        }
        
        .nav-section {
            padding: 30px;
            border-right: 1px solid #ecf0f1;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .nav-section:nth-child(even) {
            background: #f8f9fa;
        }
        
        .nav-section h3 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: white;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            border-color: #3498db;
            background: #e8f4fd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }
        
        .nav-link.primary {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .nav-link.primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }
        
        .nav-link.success {
            background: #27ae60;
            color: white;
            border-color: #27ae60;
        }
        
        .nav-link.success:hover {
            background: #229954;
            border-color: #229954;
        }
        
        .nav-link.warning {
            background: #f39c12;
            color: white;
            border-color: #f39c12;
        }
        
        .nav-link.warning:hover {
            background: #e67e22;
            border-color: #e67e22;
        }
        
        .nav-link.danger {
            background: #e74c3c;
            color: white;
            border-color: #e74c3c;
        }
        
        .nav-link.danger:hover {
            background: #c0392b;
            border-color: #c0392b;
        }
        
        .icon {
            font-size: 18px;
            min-width: 20px;
            text-align: center;
        }
        
        .description {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 4px;
            line-height: 1.4;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            border-radius: 0 0 15px 15px;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 15px;
        }
        
        .footer-link {
            color: #bdc3c7;
            text-decoration: none;
            font-size: 14px;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .external-link {
            position: relative;
        }
        
        .external-link::after {
            content: "↗";
            position: absolute;
            top: -2px;
            right: -12px;
            font-size: 10px;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .nav-grid {
                grid-template-columns: 1fr;
            }
            
            .status-bar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #ecf0f1;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1>🚀 決済システム管理ダッシュボード</h1>
            <p class="subtitle">Authorize.Net + Google Sheets 統合システム</p>
        </div>
        
        <!-- ステータスバー -->
        <div class="status-bar">
            <div class="status-item">
                <span class="icon">🌍</span>
                <span>決済環境:</span>
                <span class="status-badge <?php echo $isTestMode ? 'badge-test' : 'badge-prod'; ?>">
                    <?php echo $isTestMode ? 'テストモード' : '本番モード'; ?>
                </span>
            </div>
            <div class="status-item">
                <span class="icon">📊</span>
                <span>Sheets連携:</span>
                <span class="status-badge <?php echo $sheetsConfigured ? 'badge-connected' : 'badge-disconnected'; ?>">
                    <?php echo $sheetsConfigured ? '接続済み' : '未設定'; ?>
                </span>
            </div>
            <div class="status-item">
                <span class="icon">⏰</span>
                <span><?php echo date('Y-m-d H:i:s'); ?></span>
            </div>
        </div>
        
        <!-- メインコンテンツ -->
        <div class="main-content">
            <div class="nav-grid">
                <!-- 決済機能 -->
                <div class="nav-section">
                    <h3><span class="icon">💳</span>決済機能</h3>
                    <div class="nav-links">
                        <a href="index.php" class="nav-link primary">
                            <span class="icon">🛒</span>
                            <div>
                                <div>決済フォーム</div>
                                <div class="description">顧客向け決済ページ</div>
                            </div>
                        </a>
                        <a href="payment-status-check.php" class="nav-link">
                            <span class="icon">🔍</span>
                            <div>
                                <div>決済状況確認</div>
                                <div class="description">環境設定・連携状況確認</div>
                            </div>
                        </a>
                        <a href="authorize-credit-card.php" class="nav-link">
                            <span class="icon">⚡</span>
                            <div>
                                <div>決済処理エンジン</div>
                                <div class="description">決済処理の実行部分</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- テスト・デバッグ -->
                <div class="nav-section">
                    <h3><span class="icon">🧪</span>テスト・デバッグ</h3>
                    <div class="nav-links">
                        <a href="setup-test.php" class="nav-link success">
                            <span class="icon">✅</span>
                            <div>
                                <div>設定テストページ</div>
                                <div class="description">システム設定の総合確認</div>
                            </div>
                        </a>
                        <a href="setup-checklist.html" class="nav-link">
                            <span class="icon">📋</span>
                            <div>
                                <div>セットアップチェックリスト</div>
                                <div class="description">初期設定の進捗管理</div>
                            </div>
                        </a>
                        <a href="test-tools/sheets/test-data-sender.php" class="nav-link warning">
                            <span class="icon">📤</span>
                            <div>
                                <div>データ送信テスト</div>
                                <div class="description">Sheets連携の単体テスト</div>
                            </div>
                        </a>
                        <a href="test-tools/payment/index.php" class="nav-link">
                            <span class="icon">🔧</span>
                            <div>
                                <div>決済テストツール</div>
                                <div class="description">決済機能の詳細テスト</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Google Sheets関連 -->
                <div class="nav-section">
                    <h3><span class="icon">📊</span>Google Sheets管理</h3>
                    <div class="nav-links">
                        <?php if ($sheetsConfigured): ?>
                            <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" class="nav-link primary external-link" target="_blank">
                                <span class="icon">📋</span>
                                <div>
                                    <div>注文管理スプレッドシート</div>
                                    <div class="description">決済データの確認・管理</div>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="nav-link" style="opacity: 0.5; cursor: not-allowed;">
                                <span class="icon">📋</span>
                                <div>
                                    <div>スプレッドシート（未設定）</div>
                                    <div class="description">設定完了後に利用可能</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <a href="test-tools/sheets/debug-sheets-connection.php" class="nav-link">
                            <span class="icon">🔗</span>
                            <div>
                                <div>Sheets接続デバッグ</div>
                                <div class="description">連携エラーの詳細確認</div>
                            </div>
                        </a>
                        <a href="test-tools/sheets/sheets-test-suite.php" class="nav-link">
                            <span class="icon">🧮</span>
                            <div>
                                <div>Sheetsテストスイート</div>
                                <div class="description">包括的な連携テスト</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- 設定・管理 -->
                <div class="nav-section">
                    <h3><span class="icon">⚙️</span>設定・管理</h3>
                    <div class="nav-links">
                        <a href="config.php" class="nav-link danger" onclick="return confirm('設定ファイルを直接編集しますか？')">
                            <span class="icon">📝</span>
                            <div>
                                <div>設定ファイル編集</div>
                                <div class="description">config.php の直接編集</div>
                            </div>
                        </a>
                        <a href="SETUP_GUIDE.md" class="nav-link" target="_blank">
                            <span class="icon">📖</span>
                            <div>
                                <div>セットアップガイド</div>
                                <div class="description">詳細な設定手順書</div>
                            </div>
                        </a>
                        <a href="test-tools/misc/debug-form-data.php" class="nav-link">
                            <span class="icon">🐛</span>
                            <div>
                                <div>フォームデータデバッグ</div>
                                <div class="description">送信データの確認</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- 外部リンク -->
                <div class="nav-section">
                    <h3><span class="icon">🌐</span>外部サービス</h3>
                    <div class="nav-links">
                        <a href="https://account.authorize.net/" class="nav-link external-link" target="_blank">
                            <span class="icon">💳</span>
                            <div>
                                <div>Authorize.Net管理画面</div>
                                <div class="description">決済ゲートウェイの管理</div>
                            </div>
                        </a>
                        <a href="https://developer.authorize.net/" class="nav-link external-link" target="_blank">
                            <span class="icon">👨‍💻</span>
                            <div>
                                <div>Authorize.Net開発者サイト</div>
                                <div class="description">API ドキュメント・SANDBOX</div>
                            </div>
                        </a>
                        <a href="https://script.google.com/" class="nav-link external-link" target="_blank">
                            <span class="icon">📜</span>
                            <div>
                                <div>Google Apps Script</div>
                                <div class="description">連携スクリプトの管理</div>
                            </div>
                        </a>
                        <a href="https://sheets.google.com/" class="nav-link external-link" target="_blank">
                            <span class="icon">📊</span>
                            <div>
                                <div>Google Sheets</div>
                                <div class="description">スプレッドシート管理</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- クイックアクション -->
                <div class="nav-section">
                    <h3><span class="icon">⚡</span>クイックアクション</h3>
                    <div class="nav-links">
                        <a href="index.php?lang=ja" class="nav-link">
                            <span class="icon">🇯🇵</span>
                            <div>
                                <div>日本語決済フォーム</div>
                                <div class="description">日本語版での決済テスト</div>
                            </div>
                        </a>
                        <a href="index.php?lang=en" class="nav-link">
                            <span class="icon">🇺🇸</span>
                            <div>
                                <div>English Payment Form</div>
                                <div class="description">英語版での決済テスト</div>
                            </div>
                        </a>
                        <a href="?clear_cache=1" class="nav-link warning" onclick="return confirm('キャッシュをクリアしますか？')">
                            <span class="icon">🗑️</span>
                            <div>
                                <div>キャッシュクリア</div>
                                <div class="description">一時ファイルの削除</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- フッター -->
        <div class="footer">
            <div class="footer-links">
                <a href="SETUP_GUIDE.md" class="footer-link">セットアップガイド</a>
                <a href="payment-status-check.php" class="footer-link">システム状態</a>
                <a href="setup-test.php" class="footer-link">動作テスト</a>
                <a href="mailto:support@example.com" class="footer-link">サポート</a>
            </div>
            <p>&copy; 2024 決済システム管理ダッシュボード - Authorize.Net + Google Sheets統合</p>
        </div>
    </div>

    <?php if (isset($_GET['clear_cache'])): ?>
    <script>
        alert('キャッシュクリア処理を実行しました。');
        // 実際のキャッシュクリア処理をここに追加
        window.location.href = 'dashboard.php';
    </script>
    <?php endif; ?>
</body>
</html>