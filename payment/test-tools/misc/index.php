<?php
/**
 * その他テストツール ポータル
 */

$tools = [
    'debug-form-data.php' => [
        'name' => 'フォームデータデバッグ',
        'description' => 'POSTデータの確認とデバッグ',
        'icon' => '📝',
        'type' => 'debug'
    ],
    'simple-test.php' => [
        'name' => 'シンプルテスト',
        'description' => '基本的な動作確認用テスト',
        'icon' => '🔍',
        'type' => 'test'
    ],
    'test-translation.php' => [
        'name' => '翻訳テスト',
        'description' => '多言語対応のテスト',
        'icon' => '🌐',
        'type' => 'test'
    ]
];

$files = [
    'test-sample-codes.sh' => [
        'name' => 'サンプルコードテスト',
        'description' => 'Bashスクリプトによるテスト実行',
        'icon' => '📜',
        'type' => 'script'
    ],
    'upload-checklist.md' => [
        'name' => 'アップロード確認リスト',
        'description' => 'デプロイ前の確認項目',
        'icon' => '📋',
        'type' => 'document'
    ]
];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🔧 その他テストツール</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 2.5em; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .section { background: white; border-radius: 10px; padding: 25px; margin: 20px 0; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .section h2 { margin: 0 0 20px 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; }
        .tool-item { padding: 15px; border: 1px solid #ecf0f1; border-radius: 8px; transition: transform 0.2s ease; }
        .tool-item:hover { transform: translateY(-2px); background: #f8f9fa; }
        .tool-icon { font-size: 2em; display: inline-block; margin-right: 10px; }
        .tool-title { font-weight: bold; color: #2c3e50; margin-bottom: 5px; }
        .tool-desc { color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; }
        .tool-type { display: inline-block; padding: 3px 8px; background: #ecf0f1; color: #7f8c8d; border-radius: 12px; font-size: 0.8em; }
        .btn { display: inline-block; padding: 8px 15px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9em; margin-top: 8px; }
        .btn:hover { background: #2980b9; }
        .nav-links { text-align: center; margin: 30px 0; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.3); }
        .info-box { background: #e3f2fd; border: 1px solid #bbdefb; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 その他テストツール</h1>
            <p>開発・デバッグ用のユーティリティツール</p>
        </div>

        <div class="nav-links">
            <a href="../index.php">🏠 メインポータル</a>
            <a href="../sheets/">📊 スプレッドシートテスト</a>
            <a href="../payment/">💳 決済テスト</a>
        </div>

        <div class="section">
            <h2>🧪 デバッグ・テストツール</h2>
            <div class="tools-grid">
                <?php foreach ($tools as $file => $tool): ?>
                    <div class="tool-item">
                        <div>
                            <span class="tool-icon"><?php echo $tool['icon']; ?></span>
                            <span class="tool-title"><?php echo $tool['name']; ?></span>
                            <span class="tool-type"><?php echo $tool['type']; ?></span>
                        </div>
                        <div class="tool-desc"><?php echo $tool['description']; ?></div>
                        <a href="<?php echo $file; ?>" class="btn">開く</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section">
            <h2>📄 ドキュメント・スクリプト</h2>
            <div class="tools-grid">
                <?php foreach ($files as $file => $tool): ?>
                    <div class="tool-item">
                        <div>
                            <span class="tool-icon"><?php echo $tool['icon']; ?></span>
                            <span class="tool-title"><?php echo $tool['name']; ?></span>
                            <span class="tool-type"><?php echo $tool['type']; ?></span>
                        </div>
                        <div class="tool-desc"><?php echo $tool['description']; ?></div>
                        <?php if ($tool['type'] === 'document'): ?>
                            <a href="<?php echo $file; ?>" class="btn" target="_blank">表示</a>
                        <?php else: ?>
                            <a href="<?php echo $file; ?>" class="btn" download>ダウンロード</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section">
            <h2>📁 旧テストディレクトリ</h2>
            <div class="info-box">
                <strong>tests/</strong> ディレクトリには、以前作成されたテストファイルが保存されています。
                必要に応じて参照してください。
            </div>
            <a href="tests/" class="btn">tests/ ディレクトリを開く</a>
        </div>
    </div>
</body>
</html>