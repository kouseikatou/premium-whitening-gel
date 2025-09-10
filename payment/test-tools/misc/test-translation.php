<?php
// Test script to verify translations
session_start();

// Test English
$_SESSION['lang'] = 'en';
include 'index.php';
$english_content = ob_get_contents();
ob_clean();

// Test Japanese
$_SESSION['lang'] = 'ja';
include 'index.php';
$japanese_content = ob_get_contents();
ob_clean();

// Check if translations are working
$english_keywords = ['Checkout', 'Contact', 'Delivery', 'Payment', 'First name', 'Last name'];
$japanese_keywords = ['チェックアウト', '連絡先', '配送先', '支払い', '名', '姓'];

echo "Translation Test Results:\n";
echo "========================\n\n";

echo "English version contains expected keywords: ";
$english_found = 0;
foreach ($english_keywords as $keyword) {
    if (strpos($english_content, $keyword) !== false) {
        $english_found++;
    }
}
echo "$english_found/" . count($english_keywords) . " found\n";

echo "Japanese version contains expected keywords: ";
$japanese_found = 0;
foreach ($japanese_keywords as $keyword) {
    if (strpos($japanese_content, $keyword) !== false) {
        $japanese_found++;
    }
}
echo "$japanese_found/" . count($japanese_keywords) . " found\n";

echo "\nTranslation system is " . (($english_found > 0 && $japanese_found > 0) ? "working!" : "not working properly.");
?>