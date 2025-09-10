<?php
/**
 * 決済システムテストツール ポータル
 */

require_once '../../config.php';
require_once '../../SampleCodeConstants.php';

$tools = [
    'payment-test-suite.php' => [
        'name' => '決済テストスイート',
        'description' => 'Authorize.Net決済システムの統合テスト',
        'icon' => '🧪',
        'features' => ['API認証', '環境確認', 'カード検証', 'CLIENT_KEY管理']
    ],
    'check-card-types.php' => [
        'name' => 'カードタイプ確認',
        'description' => '対応カードタイプの確認ツール',
        'icon' => '💳',
        'features' => ['カードタイプ判定', 'Luhn検証', 'テストカード']
    ],
    'debug-auth.php' => [
        'name' => '認証情報デバッグ',
        'description' => 'Authorize.Net認証情報の確認',
        'icon' => '🔑',
        'features' => ['認証情報表示', 'キー長確認', 'マスキング表示']
    ],
    'debug-config.php' => [
        'name' => '設定デバッグ',
        'description' => 'システム設定の詳細確認',
        'icon' => '⚙️',
        'features' => ['環境設定', 'API設定', 'キー検証']
    ],
    'validate-client-key.php' => [
        'name' => 'CLIENT_KEY検証',
        'description' => 'CLIENT_KEYの有効性確認',
        'icon' => '🎫',
        'features' => ['キー検証', 'API通信', 'エラー診断']
    ]
];

$environment = AUTHORIZENET_ENVIRONMENT;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>💳 決済システム テストツール</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 2.5em; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .environment-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; margin: 10px; }
        .env-production { background: #e74c3c; color: white; }
        .env-sandbox { background: #27ae60; color: white; }
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .tool-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: transform 0.3s ease; }
        .tool-card:hover { transform: translateY(-5px); }
        .tool-icon { font-size: 3em; text-align: center; margin-bottom: 15px; }
        .tool-title { font-size: 1.3em; font-weight: bold; margin-bottom: 10px; color: #2c3e50; }
        .tool-desc { color: #7f8c8d; margin-bottom: 15px; line-height: 1.5; }
        .tool-features { list-style: none; padding: 0; margin: 15px 0; }
        .tool-features li { padding: 5px 0; color: #27ae60; font-size: 0.9em; }
        .tool-features li::before { content: "✓ "; font-weight: bold; }
        .btn { display: block; text-align: center; padding: 12px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; transition: background 0.3s; }
        .btn:hover { background: #2980b9; }
        .nav-links { text-align: center; margin: 30px 0; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.3); }
        .warning-box { background: rgba(255,255,255,0.9); padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 5px solid #f39c12; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💳 決済システム テストツール</h1>
            <span class="environment-badge <?php echo $environment === 'PRODUCTION' ? 'env-production' : 'env-sandbox'; ?>">
                <?php echo $environment; ?> 環境
            </span>
            <p>Authorize.Net決済システムのテストツール一覧</p>
        </div>

        <?php if ($environment === 'PRODUCTION'): ?>
        <div class="warning-box">
            <strong>⚠️ 本番環境での注意事項</strong>
            <ul>
                <li>テストカードは使用できません</li>
                <li>実際のクレジットカード情報は入力しないでください</li>
                <li>テストは最小限に留めてください</li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="nav-links">
            <a href="../index.php">🏠 メインポータル</a>
            <a href="../sheets/">📊 スプレッドシートテスト</a>
            <a href="../../index.php" target="_blank">💳 決済フォーム</a>
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