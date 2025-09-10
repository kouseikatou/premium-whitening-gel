<?php
/**
 * Googleスプレッドシートテストツール ポータル
 */

require_once '../../config.php';

$tools = [
    'sheets-test-suite.php' => [
        'name' => 'Sheets テストスイート',
        'description' => 'Googleスプレッドシート機能の統合テスト',
        'icon' => '🧪',
        'features' => ['設定検証', '読み取りテスト', 'データ送信', 'バルクテスト']
    ],
    'test-data-sender.php' => [
        'name' => 'データ送信ツール',
        'description' => 'テストデータの送信とフォーマット確認',
        'icon' => '📤',
        'features' => ['プリセットデータ', 'カスタムデータ', 'JSON検証']
    ],
    'data-export-sample.php' => [
        'name' => 'データフォーマット例',
        'description' => 'データ形式のサンプルと実装例',
        'icon' => '📋',
        'features' => ['JSON形式', 'CSV形式', '実装例']
    ],
    'debug-apps-script.php' => [
        'name' => 'Apps Script デバッグ',
        'description' => 'Google Apps Script連携のデバッグ',
        'icon' => '🔧',
        'features' => ['URL確認', '通信テスト', 'レスポンス確認']
    ],
    'debug-sheets-connection.php' => [
        'name' => '接続デバッグツール',
        'description' => '送信失敗の原因を詳細に調査',
        'icon' => '🔍',
        'features' => ['エラーログ確認', 'URL接続テスト', '詳細診断']
    ],
    'quick-test.php' => [
        'name' => 'クイックテスト',
        'description' => '簡単で素早い接続テスト',
        'icon' => '⚡',
        'features' => ['即座テスト', '結果表示', 'エラー診断']
    ],
    'fix-apps-script.php' => [
        'name' => 'Apps Script 修正ガイド',
        'description' => '401エラーの解決方法',
        'icon' => '🔧',
        'features' => ['権限設定', 'デプロイ方法', 'コード例']
    ]
];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>📊 Googleスプレッドシート テストツール</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); min-height: 100vh; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 2.5em; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .tool-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: transform 0.3s ease; }
        .tool-card:hover { transform: translateY(-5px); }
        .tool-icon { font-size: 3em; text-align: center; margin-bottom: 15px; }
        .tool-title { font-size: 1.3em; font-weight: bold; margin-bottom: 10px; color: #2c3e50; }
        .tool-desc { color: #7f8c8d; margin-bottom: 15px; line-height: 1.5; }
        .tool-features { list-style: none; padding: 0; margin: 15px 0; }
        .tool-features li { padding: 5px 0; color: #27ae60; }
        .tool-features li::before { content: "✓ "; font-weight: bold; }
        .btn { display: block; text-align: center; padding: 12px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; transition: background 0.3s; }
        .btn:hover { background: #2980b9; }
        .nav-links { text-align: center; margin: 30px 0; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Googleスプレッドシート テストツール</h1>
            <p>Googleスプレッドシート連携機能のテストツール一覧</p>
        </div>

        <div class="nav-links">
            <a href="../index.php">🏠 メインポータル</a>
            <a href="../payment/">💳 決済テスト</a>
            <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" target="_blank">📊 スプレッドシート</a>
        </div>

        <div class="tools-grid">
            <?php foreach ($tools as $file => $tool): ?>
                <div class="tool-card">
                    <div class="tool-icon"><?php echo $tool['icon']; ?></div>
                    <div class="tool-title"><?php echo $tool['name']; ?></div>
                    <div class="tool-desc"><?php echo $tool['description']; ?></div>
                    <ul class="tool-features">
                        <?php foreach ($tool['features'] as $feature): ?>
                            <li><?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo $file; ?>" class="btn">ツールを開く</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>