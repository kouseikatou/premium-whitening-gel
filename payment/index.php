<?php
session_start();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;

// Set Content Security Policy header
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://js.authorize.net https://jstest.authorize.net https://maps.googleapis.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://cdn.jsdelivr.net; connect-src 'self' https://api.authorize.net https://api2.authorize.net https://apitest.authorize.net https://js.authorize.net https://jstest.authorize.net https://maps.googleapis.com https://zipcloud.ibsnet.co.jp");

// Load API configuration
require_once 'config.php';

// Language translations
$translations = [
    'en' => [
        'page_title' => 'Checkout - Premium Whitening Gel',
        'back_to_cart' => '← Back to cart',
        'checkout' => 'Checkout',
        'contact' => 'Contact',
        'email' => 'Email',
        'delivery' => 'Delivery',
        'country' => 'Country/Region',
        'japan' => 'Japan',
        'usa' => 'United States',
        'canada' => 'Canada',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'company' => 'Company (optional)',
        'address' => 'District/Area',
        'apartment' => 'Street address & building number (e.g., 1-23-5)',
        'city' => 'City',
        'state' => 'State / Province',
        'postal_code' => 'Postal code',
        'phone' => 'Phone',
        'save_info' => 'Save this information for next time',
        'shipping' => 'Shipping address',
        'same_as_delivery' => 'Same as delivery address',
        'payment' => 'Payment',
        'secure_notice' => 'All transactions are secure and encrypted.',
        'credit_card' => 'Credit card',
        'card_number' => 'Card number',
        'expiration' => 'Expiration date (MM / YY)',
        'security_code' => 'Security code',
        'name_on_card' => 'Name on card',
        'pay_now' => 'Pay now',
        'secure_payment' => 'Your payment information is secure and protected',
        'product_name' => 'Premium Whitening Gel',
        'subtotal' => 'Subtotal',
        'shipping_fee' => 'Shipping',
        'taxes' => 'Taxes',
        'total' => 'Total',
        'english_input_notice' => 'Please enter in English as the local payment site may cause character encoding issues',
        'optional' => 'optional'
    ],
    'ja' => [
        'page_title' => 'チェックアウト - プレミアムホワイトニングジェル',
        'back_to_cart' => '← カートに戻る',
        'checkout' => 'チェックアウト',
        'contact' => '連絡先',
        'email' => 'メールアドレス',
        'delivery' => '配送先',
        'country' => '国・地域',
        'japan' => '日本',
        'usa' => 'アメリカ合衆国',
        'canada' => 'カナダ',
        'first_name' => '名',
        'last_name' => '姓',
        'company' => '会社名（任意）',
        'address' => '区・町名',
        'apartment' => '丁目・番地・号（例: 1-23-5）',
        'city' => '市区町村',
        'state' => '都道府県',
        'postal_code' => '郵便番号',
        'phone' => '電話番号',
        'save_info' => '次回のために情報を保存する',
        'shipping' => '配送住所',
        'same_as_delivery' => '配送先住所と同じ',
        'payment' => '支払い',
        'secure_notice' => 'すべてのトランザクションは安全で暗号化されています。',
        'credit_card' => 'クレジットカード',
        'card_number' => 'カード番号',
        'expiration' => '有効期限（MM / YY）',
        'security_code' => 'セキュリティコード',
        'name_on_card' => 'カード名義',
        'pay_now' => '今すぐ支払う',
        'secure_payment' => 'お支払い情報は保護され、安全です',
        'product_name' => 'プレミアムホワイトニングジェル',
        'subtotal' => '小計',
        'shipping_fee' => '送料',
        'taxes' => '税金',
        'total' => '合計',
        'english_input_notice' => '現地決済サイトが文字化けを起こすため英字で記載をしてください',
        'optional' => '任意'
    ]
];

$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $t['page_title']; ?></title>
    <link rel="icon" href="data:;base64,iVBORw0KGgo=" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            touch-action: manipulation;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafafa;
            color: #333;
            line-height: 1.5;
        }

        .header {
            background: white;
            border-bottom: 1px solid #e1e1e1;
            padding: 20px 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-button {
            color: #2c5aa0;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            text-decoration: underline;
        }

        .logo {
            font-size: 20px;
            font-weight: 500;
            color: #333;
        }


        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 400px;
            min-height: calc(100vh - 80px);
        }

        @media (max-width: 968px) and (min-width: 769px) {
            .container {
                grid-template-columns: 1fr;
                gap: 0;
                display: block;
            }
            
            .order-summary {
                order: -1;
                position: sticky;
                top: 20px;
                margin-bottom: 0;
                border-left: none;
                border-bottom: 1px solid #e1e1e1;
                max-height: 25vh;
                overflow-y: auto;
                z-index: 50;
            }
            
            .main-content {
                border-right: none;
                min-height: calc(75vh);
            }
            
            /* Show product info on tablets */
            .desktop-product-info {
                display: block;
            }
        }

        .main-content {
            padding: 40px;
            background: white;
            border-right: 1px solid #e1e1e1;
        }

        .order-summary {
            background: #fafafa;
            padding: 40px;
            border-left: 1px solid #e1e1e1;
            position: sticky;
            top: 20px;
            height: fit-content;
            transition: all 0.3s ease;
        }
        
        /* Show product info on desktop */
        .desktop-product-info {
            display: block;
        }

        @media (max-width: 768px) {
            .header {
                position: sticky;
                top: 0;
                z-index: 100;
            }
            
            .header-content {
                padding: 0 16px;
                flex-wrap: wrap;
                gap: 12px;
            }
            
            .logo {
                font-size: 18px;
                order: 1;
                flex: 1;
                text-align: center;
            }
            
            .back-button {
                order: 0;
                font-size: 13px;
            }
            
            .header-content > div:last-child {
                order: 2;
                font-size: 13px;
            }
            
            .container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                padding-top: 0;
            }
            
            .order-summary {
                order: 1;
                padding: 16px;
                height: auto;
                max-height: 18vh; /* より小さく */
                background: #fafafa;
                border-bottom: 2px solid #e1e1e1;
                border-left: none;
                overflow-y: auto;
                flex-shrink: 0;
                display: block;
                width: 100%;
                margin-bottom: 0;
            }
            
            .main-content {
                order: 2;
                padding: 16px;
                margin-top: 0;
                border-right: none;
                flex: 1;
            }
            
            
            .order-summary .summary-row {
                padding: 8px 0;
                font-size: 14px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .order-summary .summary-row.total {
                padding-top: 12px;
                margin-top: 8px;
                font-size: 15px;
                border-top: 1px solid #e1e1e1;
                font-weight: 600;
            }
            
            /* Hide product info on mobile */
            .desktop-product-info {
                display: none;
            }
            
            
            h1 {
                font-size: 22px;
                margin-bottom: 16px;
                margin-top: 8px;
            }
            
            h2 {
                font-size: 16px;
                margin-bottom: 12px;
            }
            
            .section {
                margin-bottom: 20px;
            }
            
            body {
                overflow-x: hidden;
            }
        }

        h1 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 30px;
            color: #333;
        }

        h2 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 16px;
            color: #333;
        }

        .section {
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 400;
            color: #737373;
            margin-bottom: 6px;
        }

        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d9d9d9;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            transition: border-color 0.2s;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #2c5aa0;
            box-shadow: 0 0 0 2px rgba(44, 90, 160, 0.1);
        }

        @media (max-width: 768px) {
            input, select {
                padding: 14px 16px;
                font-size: 16px; /* Prevents zoom on iOS */
                border-radius: 6px;
                -webkit-tap-highlight-color: transparent;
            }
            
            input:focus, select:focus {
                border-color: #2c5aa0;
                box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.15);
                transform: scale(1.01);
                transition: all 0.2s ease;
                position: relative;
                z-index: 10;
            }
            
            input:invalid {
                border-color: #ff6b6b;
            }
            
            input:valid {
                border-color: #51cf66;
            }
            
            .button-primary {
                padding: 18px 24px;
                font-size: 18px;
                border-radius: 8px;
                min-height: 54px;
                -webkit-tap-highlight-color: transparent;
            }
            
            .button-primary:active {
                transform: scale(0.98);
                transition: transform 0.1s ease;
            }
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 20px 0;
        }

        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin: 0;
            margin-top: 1px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 2px solid #d9d9d9;
            border-radius: 4px;
            background: white;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .checkbox-container input[type="checkbox"]:hover {
            border-color: #2c5aa0;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }

        .checkbox-container input[type="checkbox"]:checked {
            background: #2c5aa0;
            border-color: #2c5aa0;
        }

        .checkbox-container input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 13px;
            font-weight: bold;
            line-height: 1;
        }

        .checkbox-container input[type="checkbox"]:focus {
            outline: none;
            border-color: #2c5aa0;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.2);
        }

        .checkbox-container label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
            color: #333;
            font-size: 15px;
            line-height: 1.4;
            user-select: none;
        }

        @media (max-width: 768px) {
            .checkbox-container {
                gap: 14px;
                margin: 24px 0;
                align-items: flex-start;
            }
            
            .checkbox-container input[type="checkbox"] {
                width: 22px;
                height: 22px;
                margin-top: 0;
            }
            
            .checkbox-container input[type="checkbox"]:checked::after {
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 14px;
            }
            
            .checkbox-container label {
                font-size: 16px;
                line-height: 1.5;
                -webkit-tap-highlight-color: transparent;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-row {
                gap: 20px;
            }
            
            .help-text {
                font-size: 13px;
                margin-top: 8px;
                line-height: 1.4;
            }
            
            /* Address search button mobile improvements */
            button[onclick*="searchAddress"], button[onclick*="searchShippingAddress"] {
                width: 100%;
                padding: 12px 16px !important;
                margin-top: 12px !important;
                background: #2c5aa0 !important;
                color: white !important;
                border: none !important;
                border-radius: 6px !important;
                font-size: 15px !important;
                font-weight: 500 !important;
                cursor: pointer !important;
                -webkit-tap-highlight-color: transparent;
                transition: all 0.2s ease;
            }
            
            button[onclick*="searchAddress"]:active, button[onclick*="searchShippingAddress"]:active {
                transform: scale(0.98);
                background: #1e3d6f !important;
            }
        }


        .shipping-notice {
            background: #f0f7ff;
            color: #0c5aa6;
            padding: 12px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            margin-bottom: 16px;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
            background: #f0f0f0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-quantity {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #666;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .product-details {
            flex: 1;
        }

        .product-name {
            font-weight: 500;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .product-variant {
            color: #666;
            font-size: 14px;
        }

        .product-price {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .divider {
            border-top: 1px solid #e1e1e1;
            margin: 16px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 1px solid #e1e1e1;
            margin-top: 16px;
            padding-top: 16px;
            font-size: 16px;
            font-weight: 600;
        }

        .button-primary {
            background: #2c5aa0;
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s;
        }

        .button-primary:hover {
            background: #1e3d6f;
        }

        .security-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            font-size: 12px;
            color: #737373;
        }

        .lock-icon {
            width: 16px;
            height: 20px;
            background: #737373;
            border-radius: 3px;
            position: relative;
        }

        .lock-icon::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 3px;
            width: 10px;
            height: 10px;
            border: 2px solid #737373;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
        }

        .help-text {
            font-size: 12px;
            color: #737373;
            margin-top: 4px;
        }

        .payment-method {
            background: #ffffff;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .payment-method-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e1e1e1;
        }

        .payment-method-icon {
            width: 20px;
            height: 20px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="%234a90e2" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>') center/contain no-repeat;
        }

        .card-number-container {
            position: relative;
        }

        .card-type-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 20px;
            display: none;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .card-type-icon.visa {
            background-image: url('https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/visa.svg');
            display: block;
            background-size: 85%;
        }

        .card-type-icon.mastercard {
            background-image: url('https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/mastercard.svg');
            display: block;
            background-size: 90%;
        }

        .card-type-icon.amex {
            background-image: url('https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/americanexpress.svg');
            display: block;
            background-size: 75%;
        }

        .card-type-icon.discover {
            background-image: url('https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/discover.svg');
            display: block;
            background-size: 85%;
        }

        .card-type-icon.jcb {
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCA0MCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjI0IiByeD0iNCIgZmlsbD0iIzAwNjZDQyIvPgo8dGV4dCB4PSIyMCIgeT0iMTQiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5KQ0I8L3RleHQ+Cjwvc3ZnPgo=');
            display: block;
            background-size: 70%;
        }

        .card-type-icon.diners {
            background-image: url('https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/dinersclub.svg');
            display: block;
            background-size: 85%;
        }

        .cvv-container {
            position: relative;
        }

        .cvv-help {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            cursor: pointer;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="%23666" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>') center/contain no-repeat;
        }

        .cvv-tooltip {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
            z-index: 1000;
        }

        .cvv-tooltip.show {
            opacity: 1;
            visibility: visible;
        }

        .cvv-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 20px;
            border: 4px solid transparent;
            border-top-color: #333;
        }


        .expiry-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .expiry-separator {
            font-size: 18px;
            color: #666;
            margin: 0 4px;
        }

        /* Mobile responsive styles for credit card */
        @media (max-width: 768px) {
            .payment-method {
                padding: 16px;
                margin-bottom: 12px;
            }

            .payment-method-header {
                margin-bottom: 16px;
                padding-bottom: 10px;
            }


            .card-type-icon {
                width: 28px;
                height: 18px;
                right: 10px;
            }

            .cvv-help {
                width: 18px;
                height: 18px;
                right: 10px;
            }

            .cvv-tooltip {
                font-size: 11px;
                padding: 6px 8px;
                right: -10px;
            }

            .expiry-container {
                gap: 6px;
            }

            .expiry-separator {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .payment-method {
                padding: 12px;
            }


            .expiry-container {
                justify-content: center;
                max-width: 200px;
                margin: 0 auto;
            }

            .cvv-tooltip {
                right: -20px;
                bottom: 110%;
            }
        }

        /* Field message styles */
        .field-message {
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            animation: fadeIn 0.3s ease-in;
        }

        .field-message.error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .field-message.warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Processing overlay styles */
        #processingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(3px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-in;
        }

        .processing-content {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }

        .processing-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007cff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .processing-content h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .processing-content p {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
        }

        /* Payment error message styles */
        .payment-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            animation: fadeIn 0.3s ease-in;
            margin-top: 16px;
        }

        .payment-error strong {
            display: block;
            margin-bottom: 4px;
        }
    </style>
    <?php if (AUTHORIZENET_ENVIRONMENT === 'SANDBOX'): ?>
    <script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script>
    <?php else: ?>
    <script type="text/javascript" src="https://js.authorize.net/v1/Accept.js" charset="utf-8"></script>
    <?php endif; ?>
    <script>
        // Pass PHP constants to JavaScript
        const GOOGLE_MAPS_API_KEY = '<?php echo GOOGLE_MAPS_API_KEY; ?>';
        const AUTHORIZENET_ENVIRONMENT = '<?php echo AUTHORIZENET_ENVIRONMENT; ?>';
        
        // Debug environment
        console.log('Environment:', AUTHORIZENET_ENVIRONMENT);
    </script>
</head>
<body>
    <?php
    require_once 'SampleCodeConstants.php';
    ?>
    <header class="header">
        <div class="header-content">
            <a href="https://dha-team.com/index.html" class="back-button">
                <?php echo $t['back_to_cart']; ?>
            </a>
            <div class="logo">Infinite Tooth Care</div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="?lang=en" style="text-decoration: none; color: <?php echo $lang == 'en' ? '#2c5aa0' : '#666'; ?>; font-size: 14px;">EN</a>
                <span style="color: #ccc;">|</span>
                <a href="?lang=ja" style="text-decoration: none; color: <?php echo $lang == 'ja' ? '#2c5aa0' : '#666'; ?>; font-size: 14px;">日本語</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <form action="authorize-credit-card.php" id="paymentForm" method="POST">
                <h1><?php echo $t['checkout']; ?></h1>

                <section class="section">
                    <h2><?php echo $t['contact']; ?></h2>
                    <div class="form-group">
                        <label for="mell-contact"><?php echo $t['email']; ?></label>
                        <input type="email" name="mell-contact" id="mell-contact" placeholder="<?php echo $t['email']; ?>" required/>
                    </div>
                </section>

                <section class="section">
                    <h2><?php echo $t['delivery']; ?></h2>
                    <p class="help-text"><?php echo $t['english_input_notice']; ?></p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="FirstName"><?php echo $t['first_name']; ?></label>
                            <input type="text" name="FirstName" id="FirstName" placeholder="<?php echo $t['first_name']; ?>" required/>
                        </div>
                        <div class="form-group">
                            <label for="LastName"><?php echo $t['last_name']; ?></label>
                            <input type="text" name="LastName" id="LastName" placeholder="<?php echo $t['last_name']; ?>" required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Company"><?php echo $t['company']; ?></label>
                        <input type="text" name="Company" id="Company" placeholder="<?php echo $t['company']; ?>"/>
                    </div>

                    <div class="form-group">
                        <label for="zip"><?php echo $t['postal_code']; ?></label>
                        <input type="text" name="zip" id="zip" placeholder="<?php echo $t['postal_code']; ?>" required onblur="searchAddress()"/>
                        <button type="button" onclick="searchAddress()" style="margin-top: 8px; padding: 8px 16px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 14px;"><?php echo $lang == 'ja' ? '住所検索' : 'Search Address'; ?></button>
                    </div>

                    <div class="form-group">
                        <label for="x_ship_to_country"><?php echo $t['country']; ?></label>
                        <select name="x_ship_to_country" id="x_ship_to_country" required>
                            <option value="Japan"><?php echo $t['japan']; ?></option>
                            <option value="United States"><?php echo $t['usa']; ?></option>
                            <option value="Canada"><?php echo $t['canada']; ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="select"><?php echo $t['state']; ?></label>
                        <input type="text" name="select" id="select" placeholder="<?php echo $t['state']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label for="city"><?php echo $t['city']; ?></label>
                        <input type="text" name="city" id="city" placeholder="<?php echo $t['city']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label for="street"><?php echo $t['address']; ?></label>
                        <input type="text" name="street" id="street" placeholder="<?php echo $t['address']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label for="apartment"><?php echo $t['apartment']; ?></label>
                        <input type="text" name="apartment" id="apartment" placeholder="<?php echo $t['apartment']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label for="contact"><?php echo $t['phone']; ?></label>
                        <input type="tel" name="contact" id="contact" placeholder="<?php echo $t['phone']; ?>" required/>
                    </div>
                </section>

                <section class="section">
                    <h2><?php echo $t['shipping']; ?></h2>
                    <div class="checkbox-container">
                        <input type="checkbox" id="sameAsDelivery" onchange="toggleShippingAddress()" checked>
                        <label for="sameAsDelivery"><?php echo $t['same_as_delivery']; ?></label>
                    </div>

                    <div id="shippingAddressSection">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="shippingFirstName"><?php echo $t['first_name']; ?></label>
                                <input type="text" name="shippingFirstName" id="shippingFirstName" placeholder="<?php echo $t['first_name']; ?>" required/>
                            </div>
                            <div class="form-group">
                                <label for="shippingLastName"><?php echo $t['last_name']; ?></label>
                                <input type="text" name="shippingLastName" id="shippingLastName" placeholder="<?php echo $t['last_name']; ?>" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shippingCompany"><?php echo $t['company']; ?></label>
                            <input type="text" name="shippingCompany" id="shippingCompany" placeholder="<?php echo $t['company']; ?>"/>
                        </div>

                        <div class="form-group">
                            <label for="shippingZip"><?php echo $t['postal_code']; ?></label>
                            <input type="text" name="shippingZip" id="shippingZip" placeholder="<?php echo $t['postal_code']; ?>" required onblur="searchShippingAddress()"/>
                            <button type="button" onclick="searchShippingAddress()" style="margin-top: 8px; padding: 8px 16px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 14px;"><?php echo $lang == 'ja' ? '住所検索' : 'Search Address'; ?></button>
                        </div>

                        <div class="form-group">
                            <label for="shippingCountry"><?php echo $t['country']; ?></label>
                            <select name="shippingCountry" id="shippingCountry" required>
                                <option value="Japan"><?php echo $t['japan']; ?></option>
                                <option value="United States"><?php echo $t['usa']; ?></option>
                                <option value="Canada"><?php echo $t['canada']; ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="shippingState"><?php echo $t['state']; ?></label>
                            <input type="text" name="shippingState" id="shippingState" placeholder="<?php echo $t['state']; ?>" required/>
                        </div>

                        <div class="form-group">
                            <label for="shippingCity"><?php echo $t['city']; ?></label>
                            <input type="text" name="shippingCity" id="shippingCity" placeholder="<?php echo $t['city']; ?>" required/>
                        </div>

                        <div class="form-group">
                            <label for="shippingStreet"><?php echo $t['address']; ?></label>
                            <input type="text" name="shippingStreet" id="shippingStreet" placeholder="<?php echo $t['address']; ?>" required/>
                        </div>

                        <div class="form-group">
                            <label for="shippingApartment"><?php echo $t['apartment']; ?></label>
                            <input type="text" name="shippingApartment" id="shippingApartment" placeholder="<?php echo $t['apartment']; ?>" required/>
                        </div>

                        <div class="form-group">
                            <label for="shippingPhone"><?php echo $t['phone']; ?></label>
                            <input type="tel" name="shippingPhone" id="shippingPhone" placeholder="<?php echo $t['phone']; ?>" required/>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h2><?php echo $t['payment']; ?></h2>
                    <div class="shipping-notice">
                        <?php echo $t['secure_notice']; ?>
                    </div>

                    <div class="payment-method">
                        <div class="payment-method-header">
                            <div class="payment-method-icon"></div>
                            <span><?php echo $t['credit_card']; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="cardNumber"><?php echo $t['card_number']; ?></label>
                            <div class="card-number-container">
                                <input type="text" name="cardNumber" id="cardNumber" placeholder="1234 5678 9012 3456" required oninput="formatCardNumber(this); detectCardType(this.value)" maxlength="19"/>
                                <div class="card-type-icon" id="cardTypeIcon"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="expMonth"><?php echo $t['expiration']; ?></label>
                                <div class="expiry-container">
                                    <input type="text" name="expMonth" id="expMonth" placeholder="MM" maxlength="2" required oninput="formatExpiry(this)" style="width: 50%;"/>
                                    <span class="expiry-separator">/</span>
                                    <input type="text" name="expYear" id="expYear" placeholder="YY" maxlength="2" required oninput="formatExpiry(this)" style="width: 50%;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cardCode"><?php echo $t['security_code']; ?></label>
                                <div class="cvv-container">
                                    <input type="text" name="cardCode" id="cardCode" placeholder="123" maxlength="4" required oninput="formatCVV(this)"/>
                                    <div class="cvv-help" onmouseover="showCVVHelp()" onmouseout="hideCVVHelp()"></div>
                                    <div class="cvv-tooltip" id="cvvTooltip">
                                        <?php echo $lang == 'ja' ? '3桁または4桁のセキュリティコード' : '3 or 4 digit security code'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cardName"><?php echo $t['name_on_card']; ?></label>
                            <input type="text" name="cardName" id="cardName" placeholder="<?php echo $t['name_on_card']; ?>" required oninput="formatCardName(this)"/>
                        </div>

                    </div>

                    <input type="hidden" name="dataValue" id="dataValue" />
                    <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
                    
                    <button type="button" class="button-primary" onclick="sendPaymentDataToAnet()"><?php echo $t['pay_now']; ?></button>
                    
                    <!-- Payment error display area -->
                    <div id="paymentErrorContainer" style="display: none; margin-top: 16px;"></div>
                    
                    <div class="security-info">
                        <div class="lock-icon"></div>
                        <span><?php echo $t['secure_payment']; ?></span>
                    </div>
                </section>
            </form>
        </div>

        <div class="order-summary">
            <div class="desktop-product-info">
                <div class="product-item">
                    <div class="product-image">
<img src="/payment/infinitetoothcares-gel.png" alt="Infinite Tooth Cares Gel" >                    </div>
                    <div class="product-details">
                        <div class="product-name"><?php echo $t['product_name']; ?></div>
                    </div>
                    <div class="product-price">$140.00</div>
                </div>
                <div class="divider"></div>
            </div>
            
            <div class="summary-row">
                <span><?php echo $t['subtotal']; ?></span>
                <span>$100.00</span>
            </div>
            <div class="summary-row">
                <span><?php echo $t['shipping_fee']; ?></span>
                <span>$40.00</span>
            </div>
            <div class="summary-row total">
                <span><?php echo $t['total']; ?></span>
                <strong>USD $140.00</strong>
            </div>
        </div>
    </div>



    <script type="text/javascript">
    function toggleShippingAddress() {
        const checkbox = document.getElementById('sameAsDelivery');
        const shippingSection = document.getElementById('shippingAddressSection');
        
        if (checkbox.checked) {
            shippingSection.style.display = 'none';
            // Copy delivery to shipping
            document.getElementById('shippingFirstName').value = document.getElementById('FirstName').value;
            document.getElementById('shippingLastName').value = document.getElementById('LastName').value;
            document.getElementById('shippingStreet').value = document.getElementById('street').value;
            document.getElementById('shippingCity').value = document.getElementById('city').value;
            document.getElementById('shippingState').value = document.getElementById('select').value;
            document.getElementById('shippingZip').value = document.getElementById('zip').value;
            document.getElementById('shippingCountry').value = document.getElementById('x_ship_to_country').value;
            document.getElementById('shippingPhone').value = document.getElementById('contact').value;
            document.getElementById('shippingCompany').value = document.getElementById('Company').value;
            
            // Also copy apartment if it exists
            const apartment = document.getElementById('apartment');
            if (apartment) {
                document.getElementById('shippingApartment').value = apartment.value;
            }
        } else {
            shippingSection.style.display = 'block';
        }
    }

    // Real-time sync when delivery address changes
    function syncToShipping() {
        const checkbox = document.getElementById('sameAsDelivery');
        if (checkbox.checked) {
            toggleShippingAddress();
        }
    }

    // Initialize shipping address sync
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize checkbox state - hide shipping section since it's checked by default
        toggleShippingAddress();
        
        // Add event listeners for real-time sync
        const deliveryFields = [
            'FirstName', 'LastName', 'Company', 'street', 'apartment', 
            'city', 'select', 'zip', 'x_ship_to_country', 'contact'
        ];
        
        deliveryFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', syncToShipping);
                field.addEventListener('change', syncToShipping);
            }
        });

        // Add event listeners for country selection changes
        const countryField = document.getElementById('x_ship_to_country');
        const shippingCountryField = document.getElementById('shippingCountry');
        
        if (countryField) {
            countryField.addEventListener('change', handleCountryChange);
            // Initialize with current selection on page load
            handleCountryChange();
        }
        if (shippingCountryField) {
            shippingCountryField.addEventListener('change', handleShippingCountryChange);
            // Initialize with current selection on page load
            handleShippingCountryChange();
        }
    });

    // Shipping address validation function
    function validateShippingAddress() {
        const currentLang = document.documentElement.lang || 'en';
        const checkbox = document.getElementById('sameAsDelivery');
        
        let requiredFields = [];
        let fieldNames = {};
        
        if (checkbox.checked) {
            // Check delivery address fields
            requiredFields = [
                'FirstName', 'LastName', 'street', 'apartment', 'city', 'select', 'zip', 'contact'
            ];
            fieldNames = {
                'FirstName': currentLang === 'ja' ? '名前' : 'First Name',
                'LastName': currentLang === 'ja' ? '姓' : 'Last Name', 
                'street': currentLang === 'ja' ? '区・町名' : 'District/Area',
                'apartment': currentLang === 'ja' ? '丁目・番地・号' : 'Street address & building number',
                'city': currentLang === 'ja' ? '市区町村' : 'City',
                'select': currentLang === 'ja' ? '都道府県' : 'State',
                'zip': currentLang === 'ja' ? '郵便番号' : 'Postal Code',
                'contact': currentLang === 'ja' ? '電話番号' : 'Phone'
            };
        } else {
            // Check shipping address fields
            requiredFields = [
                'shippingFirstName', 'shippingLastName', 'shippingStreet', 'shippingApartment',
                'shippingCity', 'shippingState', 'shippingZip', 'shippingPhone'
            ];
            fieldNames = {
                'shippingFirstName': currentLang === 'ja' ? '配送先名前' : 'Shipping First Name',
                'shippingLastName': currentLang === 'ja' ? '配送先姓' : 'Shipping Last Name',
                'shippingStreet': currentLang === 'ja' ? '配送先区・町名' : 'Shipping District/Area',
                'shippingApartment': currentLang === 'ja' ? '配送先丁目・番地・号' : 'Shipping Street address & building number',
                'shippingCity': currentLang === 'ja' ? '配送先市区町村' : 'Shipping City',
                'shippingState': currentLang === 'ja' ? '配送先都道府県' : 'Shipping State',
                'shippingZip': currentLang === 'ja' ? '配送先郵便番号' : 'Shipping Postal Code',
                'shippingPhone': currentLang === 'ja' ? '配送先電話番号' : 'Shipping Phone'
            };
        }
        
        // Check for empty required fields
        for (let fieldId of requiredFields) {
            const field = document.getElementById(fieldId);
            if (!field || !field.value.trim()) {
                const errorMessage = currentLang === 'ja' 
                    ? `${fieldNames[fieldId]}を入力してください`
                    : `Please enter ${fieldNames[fieldId]}`;
                    
                showPaymentError([{code: 'VALIDATION', text: errorMessage}]);
                
                // Focus on the empty field
                if (field) {
                    field.focus();
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        }
        
        return true;
    }

    // Payment processing function
    function sendPaymentDataToAnet() {
        // Prevent double submission
        var submitButton = document.querySelector('button[onclick="sendPaymentDataToAnet()"]');
        if (submitButton.disabled) {
            return;
        }
        submitButton.disabled = true;
        submitButton.textContent = submitButton.textContent + '...';
        
        // Clear any existing payment errors
        hidePaymentError();
        
        // Check contact information first
        const email = document.getElementById('mell-contact').value;
        if (!email || !email.includes('@')) {
            const currentLang = document.documentElement.lang || 'en';
            const errorMessage = currentLang === 'ja' 
                ? 'メールアドレスを入力してください'
                : 'Please enter a valid email address';
            showPaymentError([{code: 'VALIDATION', text: errorMessage}]);
            document.getElementById('mell-contact').focus();
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        // Check shipping address validation
        if (!validateShippingAddress()) {
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        // Basic form validation
        var cardNumber = document.getElementById("cardNumber").value.replace(/\s/g, '');
        var month = document.getElementById("expMonth").value;
        var year = document.getElementById("expYear").value;
        var cvv = document.getElementById("cardCode").value;
        
        if (!cardNumber || cardNumber.length < 13 || cardNumber.length > 19) {
            showPaymentError([{code: 'VALIDATION', text: 'Please enter a valid card number'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        if (!month || month.length !== 2 || parseInt(month) < 1 || parseInt(month) > 12) {
            showPaymentError([{code: 'VALIDATION', text: 'Please enter a valid month (01-12)'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        if (!year || year.length !== 2) {
            showPaymentError([{code: 'VALIDATION', text: 'Please enter a valid year (YY)'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        if (!cvv || cvv.length < 3 || cvv.length > 4) {
            showPaymentError([{code: 'VALIDATION', text: 'Please enter a valid security code'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        var authData = {};
        authData.clientKey = "<?php echo defined('AUTHORIZENET_CLIENT_KEY') ? AUTHORIZENET_CLIENT_KEY : ''; ?>";
        authData.apiLoginID = "<?php echo defined('AUTHORIZENET_LOGIN_ID') ? AUTHORIZENET_LOGIN_ID : ''; ?>";

        // デバッグ: 認証情報を確認
        console.log('Environment:', AUTHORIZENET_ENVIRONMENT);
        console.log('API Login ID:', authData.apiLoginID);
        console.log('Client Key length:', authData.clientKey.length);
        console.log('Client Key first 10 chars:', authData.clientKey.substring(0, 10));
        console.log('Client Key last 10 chars:', authData.clientKey.substring(authData.clientKey.length - 10));
        
        // 認証情報の整合性チェック
        if (authData.apiLoginID === '6T29MysXvMQW' && authData.clientKey.length === 80) {
            console.log('✅ Using PRODUCTION credentials');
        } else if (authData.apiLoginID === '5KP3u95bQpv' && authData.clientKey.length === 80) {
            console.log('✅ Using SANDBOX credentials');
        } else {
            console.error('❌ Credential mismatch detected');
            console.error('Expected LOGIN_ID for PRODUCTION: 6T29MysXvMQW');
            console.error('Actual LOGIN_ID:', authData.apiLoginID);
            console.error('Expected CLIENT_KEY length: 80');
            console.error('Actual CLIENT_KEY length:', authData.clientKey.length);
        }
        
        // 認証情報の検証
        if (!authData.apiLoginID || authData.apiLoginID === '') {
            console.error('ERROR: API Login ID is empty');
            showPaymentError([{code: 'CONFIG', text: 'API Login ID is not configured'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        if (!authData.clientKey || authData.clientKey === '' || authData.clientKey === 'YOUR_CLIENT_KEY') {
            console.error('ERROR: Client Key is empty or default');
            showPaymentError([{code: 'CONFIG', text: 'Client Key is not configured'}]);
            submitButton.disabled = false;
            submitButton.textContent = submitButton.textContent.replace('...', '');
            return;
        }
        
        // 環境とCLIENT_KEYの整合性チェック
        console.log('Checking environment consistency...');
        if (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') {
            if (authData.clientKey.includes('sandbox') || authData.clientKey.includes('test')) {
                console.error('ERROR: Using test CLIENT_KEY in production environment');
                showPaymentError([{code: 'ENV_MISMATCH', text: 'Environment mismatch: Test credentials in production'}]);
                submitButton.disabled = false;
                submitButton.textContent = submitButton.textContent.replace('...', '');
                return;
            }
        } else {
            console.log('Sandbox environment detected');
        }

var cardData = {};
// カード番号から非数字文字を除去
cardData.cardNumber = cardNumber.replace(/[^0-9]/g, '');

// 月が1桁の場合は0埋め（例：9 → 09）
cardData.month = month.toString().padStart(2, '0');

// 年の処理は現状で良い
cardData.year = year.length === 2 ? '20' + year : year;

// CVVも念のためサニタイズ
cardData.cardCode = cvv.toString().trim();

// Luhn算法による基本検証
function luhnCheck(cardNumber) {
    let sum = 0;
    let alternate = false;
    
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let n = parseInt(cardNumber.charAt(i), 10);
        if (alternate) {
            n *= 2;
            if (n > 9) {
                n = (n % 10) + 1;
            }
        }
        sum += n;
        alternate = !alternate;
    }
    
    return (sum % 10) === 0;
}

// デバッグ用（詳細な情報を出力）
console.log('Raw card number:', cardNumber);
console.log('Sanitized card number:', cardData.cardNumber);
console.log('Card number length:', cardData.cardNumber.length);
console.log('Luhn check result:', luhnCheck(cardData.cardNumber));
console.log('Month:', cardData.month);
console.log('Year:', cardData.year);
console.log('CVV length:', cardData.cardCode.length);
console.log('Card data:', cardData);

// Luhn検証失敗の場合の警告
if (!luhnCheck(cardData.cardNumber)) {
    console.warn('⚠️ WARNING: Card number failed Luhn algorithm check');
    console.warn('This may cause "invalid credit card number" error');
    showPaymentError([{code: 'LUHN_FAIL', text: 'クレジットカード番号が無効です。正しい番号を入力してください。'}]);
    submitButton.disabled = false;
    submitButton.textContent = submitButton.textContent.replace('...', '');
    return;
} else {
    console.log('✅ Card number passed Luhn algorithm check');
}

// PRODUCTION環境でのテストカード検証
if (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') {
    const testCardNumbers = [
        '4111111111111111', '4012888888881881', '4222222222222',
        '5555555555554444', '5105105105105100',
        '370000000000002', '378282246310005',
        '6011111111111117', '6011000990139424'
    ];
    
    if (testCardNumbers.includes(cardData.cardNumber)) {
        console.error('❌ ERROR: Test card number used in PRODUCTION environment');
        showPaymentError([{code: 'TEST_CARD_PROD', text: '本番環境ではテスト用カード番号は使用できません。実際のクレジットカード番号を入力してください。'}]);
        submitButton.disabled = false;
        submitButton.textContent = submitButton.textContent.replace('...', '');
        return;
    }
}

        var secureData = {};
        secureData.authData = authData;
        secureData.cardData = cardData;

        // Show loading screen just before API call (after all validations pass)
        showProcessingScreen();
        
        // Debug: Log data being sent to Accept.js
        console.log('Data being sent to Accept.js:', {
            authData: secureData.authData,
            cardData: {
                cardNumber: secureData.cardData.cardNumber.substring(0,4) + '****' + secureData.cardData.cardNumber.substring(secureData.cardData.cardNumber.length-4),
                month: secureData.cardData.month,
                year: secureData.cardData.year,
                cardCode: '***'
            }
        });

        Accept.dispatchData(secureData, responseHandler);

        function responseHandler(response) {
            // Debug: Log full response
            console.log('Accept.js Response:', response);
            console.log('Response messages:', response.messages);
            
            if (response.messages.resultCode === "Error") {
                // Hide loading screen on error
                hideProcessingScreen();
                
                // Re-enable button on error
                submitButton.disabled = false;
                submitButton.textContent = submitButton.textContent.replace('...', '');
                
                // Hide any existing errors first
                hidePaymentError();
                
                // Show error below PAY NOW button instead of alert
                showPaymentError(response.messages.message);
                
                // Enhanced error logging
                console.error('Accept.js Error Details:');
                var i = 0;
                while (i < response.messages.message.length) {
                    console.error('Error ' + (i+1) + ':', {
                        code: response.messages.message[i].code,
                        text: response.messages.message[i].text
                    });
                    i = i + 1;
                }
                
                // ログに詳細情報を記録
                console.error('Card data that caused error:', {
                    cardNumberLength: cardData.cardNumber.length,
                    cardNumberFirst4: cardData.cardNumber.substring(0, 4),
                    cardNumberLast4: cardData.cardNumber.substring(cardData.cardNumber.length - 4),
                    month: cardData.month,
                    year: cardData.year,
                    cvvLength: cardData.cardCode.length,
                    environment: AUTHORIZENET_ENVIRONMENT,
                    clientKeyLength: authData.clientKey.length,
                    luhnValid: luhnCheck(cardData.cardNumber)
                });
                
                // Accept.jsでのエラーの場合、サーバー側の処理は行わない
                console.error('Accept.js encryption failed - stopping here');
                return;
                
            } else {
                // Hide any existing errors on success
                hidePaymentError();
                console.log('Accept.js Success - opaqueData:', response.opaqueData);
                paymentFormUpdate(response.opaqueData);
            }
        }
    }

    function paymentFormUpdate(opaqueData) {
        document.getElementById("dataDescriptor").value = opaqueData.dataDescriptor;
        document.getElementById("dataValue").value = opaqueData.dataValue;

        // Clear sensitive data before submitting
        document.getElementById("cardNumber").value = "";
        document.getElementById("expMonth").value = "";
        document.getElementById("expYear").value = "";
        document.getElementById("cardCode").value = "";

        // Loading screen is already shown, just submit
        document.getElementById("paymentForm").submit();
    }

    function showProcessingScreen() {
        // Hide the main container
        const container = document.querySelector('.container');
        if (container) {
            container.style.display = 'none';
        }
        
        // Show processing overlay
        const processingOverlay = document.createElement('div');
        processingOverlay.id = 'processingOverlay';
        processingOverlay.innerHTML = `
            <div class="processing-content">
                <div class="processing-spinner"></div>
                <h2><?php echo $lang == 'ja' ? '決済処理中...' : 'Processing Payment...'; ?></h2>
                <p><?php echo $lang == 'ja' ? 'しばらくお待ちください。ページを閉じないでください。' : 'Please wait. Do not close this page.'; ?></p>
            </div>
        `;
        document.body.appendChild(processingOverlay);
    }

    function hideProcessingScreen() {
        // Remove processing overlay
        const processingOverlay = document.getElementById('processingOverlay');
        if (processingOverlay) {
            processingOverlay.remove();
        }
        
        // Show the main container again
        const container = document.querySelector('.container');
        if (container) {
            container.style.display = 'flex';
        }
    }

    // Postal code auto-complete function with improved error handling
    function searchAddress() {
        const zip = document.getElementById('zip').value;
        if (zip.length === 7) {
            // Format postal code: 123-4567
            const formattedZip = zip.substring(0, 3) + '-' + zip.substring(3);
            
            // Show loading indicator
            console.log('Searching address for postal code:', formattedZip);
            
            // Try Google Maps API first for English addresses, fallback to zipcloud if needed
            searchAddressGoogle(zip);
        } else if (zip.length > 0) {
            showFieldMessage('zip', 'error');
        }
    }
    
    // Test function for Google Maps API
    function testGoogleMapsAPI() {
        console.log('=== Google Maps API Test ===');
        console.log('API Key:', GOOGLE_MAPS_API_KEY ? 'Available' : 'Missing');
        console.log('API Key length:', GOOGLE_MAPS_API_KEY ? GOOGLE_MAPS_API_KEY.length : 0);
        
        const testZip = '100-0001'; // Tokyo Station area
        const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${testZip},Japan&language=en&region=JP&key=${GOOGLE_MAPS_API_KEY}`;
        
        console.log('Testing with URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                
                if (data.status === 'OK') {
                    alert('✅ Google Maps API Test SUCCESSFUL!\nCheck console for details.');
                } else {
                    alert(`❌ API Test FAILED\nStatus: ${data.status}\nError: ${data.error_message || 'Unknown error'}`);
                }
            })
            .catch(error => {
                console.error('Test failed:', error);
                alert(`❌ Test Request Failed: ${error.message}`);
            });
    }
    
    // Primary: Use Google Maps API for English addresses
    function searchAddressGoogle(zip) {
        const formattedZip = zip.substring(0, 3) + '-' + zip.substring(3);
        console.log('Trying Google Maps API for English address:', formattedZip);
        
        // Use language=en to get English responses
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${formattedZip},Japan&language=en&region=JP&key=${GOOGLE_MAPS_API_KEY}`)
            .then(response => {
                console.log('Google Maps response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Google Maps API response:', data);
                
                if (data.status === 'OK' && data.results && data.results.length > 0) {
                        const result = data.results[0];
                        const components = result.address_components;
                        
                        let city = '';
                        let state = '';
                        let street = '';
                        let sublocality = [];
                        
                        let premise = '';
                        let subpremise = '';
                        
                        components.forEach(component => {
                            const types = component.types;
                            console.log('Component:', component.long_name, 'Types:', types);
                            
                            if (types.includes('locality')) {
                                city = component.long_name;
                            } else if (types.includes('administrative_area_level_1')) {
                                state = component.long_name;
                            } else if (types.includes('sublocality_level_1')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_2')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_3')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_4')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('premise')) {
                                premise = component.long_name;
                            } else if (types.includes('subpremise')) {
                                subpremise = component.long_name;
                            }
                        });
                        
                        // Build street address from sublocality components (most specific first)
                        street = sublocality.join(', ');
                        
                        // Convert English district names to Japanese-style romanization
                        function convertToJapaneseStyle(text) {
                            if (!text) return text;
                            return text
                                .replace(/\s+Ward\b/g, '-ku')
                                .replace(/\s+City\b/g, '-shi')
                                .replace(/\s+Town\b/g, '-cho')
                                .replace(/\s+Village\b/g, '-mura');
                        }
                        
                        street = convertToJapaneseStyle(street);
                        city = convertToJapaneseStyle(city);
                        
                        // Combine premise and subpremise for apartment field
                        let apartment = '';
                        if (premise && subpremise) {
                            apartment = `${premise}-${subpremise}`;
                        } else if (premise) {
                            apartment = premise;
                        } else if (subpremise) {
                            apartment = subpremise;
                        }
                        
                        console.log('Parsed address:', { city, state, street, apartment });
                        
                        // Check if we have meaningful address data
                        // Google Maps may return OK status but only country data for invalid postal codes
                        const hasValidAddressData = city && state && (street || sublocality.length > 0);
                        const hasOnlyCountryData = components.length <= 2 && 
                            components.some(c => c.types.includes('country')) &&
                            !components.some(c => c.types.includes('locality') || c.types.includes('administrative_area_level_1'));
                        
                        if (!hasValidAddressData || hasOnlyCountryData) {
                            console.warn('Google Maps returned incomplete address data (invalid postal code):', { city, state, street, components: components.length });
                            showFieldMessage('zip', 'address_not_found');
                            return;
                        }
                        
                        document.getElementById('city').value = city;
                        document.getElementById('select').value = state;
                        document.getElementById('street').value = street;
                        document.getElementById('apartment').value = apartment;
                        document.getElementById('x_ship_to_country').value = 'Japan';
                        
                        syncToShipping();
                    } else if (data.status === 'REQUEST_DENIED' || data.status === 'OVER_QUERY_LIMIT') {
                        // Fallback to free Japanese API if Google API fails
                        console.warn('Google API issue, falling back to zipcloud');
                        searchAddressFallback(zip);
                    } else {
                        showFieldMessage('zip', 'address_not_found');
                    }
                })
                .catch(error => {
                    console.error('Google Maps API error:', error);
                    // Fallback to free API
                    searchAddressFallback(zip);
                });
        }
    
    
    // Primary function using Japanese postal code API
    function searchAddressFallback(zip) {
        console.log('Trying zipcloud API for postal code:', zip);
        
        fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zip}`, {
            method: 'GET',
            mode: 'cors'
        })
            .then(response => {
                console.log('Zipcloud response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Zipcloud API response:', data);
                
                if (data.results && data.results.length > 0) {
                    const result = data.results[0];
                    
                    // Prefecture mapping for English conversion
                    const prefectureMap = {
                        '北海道': 'Hokkaido', '青森県': 'Aomori', '岩手県': 'Iwate', '宮城県': 'Miyagi',
                        '秋田県': 'Akita', '山形県': 'Yamagata', '福島県': 'Fukushima', '茨城県': 'Ibaraki',
                        '栃木県': 'Tochigi', '群馬県': 'Gunma', '埼玉県': 'Saitama', '千葉県': 'Chiba',
                        '東京都': 'Tokyo', '神奈川県': 'Kanagawa', '新潟県': 'Niigata', '富山県': 'Toyama',
                        '石川県': 'Ishikawa', '福井県': 'Fukui', '山梨県': 'Yamanashi', '長野県': 'Nagano',
                        '岐阜県': 'Gifu', '静岡県': 'Shizuoka', '愛知県': 'Aichi', '三重県': 'Mie',
                        '滋賀県': 'Shiga', '京都府': 'Kyoto', '大阪府': 'Osaka', '兵庫県': 'Hyogo',
                        '奈良県': 'Nara', '和歌山県': 'Wakayama', '鳥取県': 'Tottori', '島根県': 'Shimane',
                        '岡山県': 'Okayama', '広島県': 'Hiroshima', '山口県': 'Yamaguchi', '徳島県': 'Tokushima',
                        '香川県': 'Kagawa', '愛媛県': 'Ehime', '高知県': 'Kochi', '福岡県': 'Fukuoka',
                        '佐賀県': 'Saga', '長崎県': 'Nagasaki', '熊本県': 'Kumamoto', '大分県': 'Oita',
                        '宮崎県': 'Miyazaki', '鹿児島県': 'Kagoshima', '沖縄県': 'Okinawa'
                    };
                    
                    const prefecture = prefectureMap[result.address1] || result.address1;
                    
                    // City mapping for major cities to English
                    const cityMap = {
                        '札幌市': 'Sapporo', '仙台市': 'Sendai', '千葉市': 'Chiba', '横浜市': 'Yokohama',
                        '川崎市': 'Kawasaki', '相模原市': 'Sagamihara', '新潟市': 'Niigata', '静岡市': 'Shizuoka',
                        '浜松市': 'Hamamatsu', '名古屋市': 'Nagoya', '京都市': 'Kyoto', '大阪市': 'Osaka',
                        '堺市': 'Sakai', '神戸市': 'Kobe', '岡山市': 'Okayama', '広島市': 'Hiroshima',
                        '北九州市': 'Kitakyushu', '福岡市': 'Fukuoka', '熊本市': 'Kumamoto'
                    };
                    
                    // Enhanced Japanese to English romanization for cities
                    function romanizeCity(text) {
                        if (!text) return '';
                        
                        const commonWords = {
                            '中央': 'Chuo', '東': 'Higashi', '西': 'Nishi', '南': 'Minami', '北': 'Kita',
                            '新': 'Shin', '本': 'Hon', '元': 'Moto', '上': 'Kami', '下': 'Shimo',
                            '大': 'Dai', '小': 'Ko', '高': 'Taka', '山': 'Yama', '川': 'Kawa',
                            '田': 'Ta', '野': 'No', '原': 'Hara', '池': 'Ike', '橋': 'Hashi',
                            '港': 'Minato', '駅': 'Eki', '区': '-ku', '市': '-shi', '町': '-cho',
                            '村': '-mura', '丁目': '-chome', '番地': '-banchi', '号': '-go'
                        };
                        
                        let romanized = text;
                        Object.entries(commonWords).forEach(([jp, en]) => {
                            romanized = romanized.replace(new RegExp(jp, 'g'), en);
                        });
                        
                        return romanized;
                    }
                    
                    const city = cityMap[result.address2] || romanizeCity(result.address2) || '';
                    function parseJapaneseAddress(address) {
                        if (!address) return { area: '', number: '' };
                        
                        // Pattern to match Japanese address with numbers (e.g., "銀座1丁目2番3号")
                        const numberPattern = /([0-9]+[-−][0-9]+[-−][0-9]+|[0-9]+丁目[0-9]+番[0-9]+号|[0-9]+[−-][0-9]+|[0-9]+丁目|[0-9]+番地?[0-9]*号?)/;
                        const match = address.match(numberPattern);
                        
                        if (match) {
                            const number = match[0];
                            const area = address.replace(number, '').trim();
                            return { area: area, number: number };
                        }
                        
                        return { area: address, number: '' };
                    }
                    
                    const addressParts = parseJapaneseAddress(result.address3);
                    const street = romanizeCity(addressParts.area) || '';
                    const apartment = addressParts.number || '';
                    
                    document.getElementById('city').value = city;
                    document.getElementById('select').value = prefecture;
                    document.getElementById('street').value = street;
                    document.getElementById('apartment').value = apartment;
                    document.getElementById('x_ship_to_country').value = 'Japan';
                    
                    syncToShipping();
                } else {
                    console.warn('No results found in zipcloud API');
                    showFieldMessage('zip', 'address_not_found');
                }
            })
            .catch(error => {
                console.error('Zipcloud API error:', error);
                if (error.message.includes('CORS') || error.message.includes('Network')) {
                    showManualEntryMessage();
                } else {
                    showFieldMessage('zip', 'api_unavailable');
                }
            });
    }
    
    // Helper function to show field messages with language support
    function showFieldMessage(fieldId, messageType) {
        console.log('showFieldMessage called:', fieldId, messageType);
        const field = document.getElementById(fieldId);
        if (!field) {
            console.error('Field not found:', fieldId);
            return;
        }
        
        // Remove existing messages for this field
        const existingMessage = field.parentNode.querySelector('.field-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Get current language
        const currentLang = document.documentElement.lang || 'en';
        console.log('Current language:', currentLang);
        
        // Define messages
        const messages = {
            'zip_error': {
                'en': 'Please enter a 7-digit postal code',
                'ja': '郵便番号は7桁で入力してください'
            },
            'api_unavailable': {
                'en': 'Postal code search unavailable. Please enter address manually.',
                'ja': '郵便番号検索が利用できません。住所を手動で入力してください。'
            },
            'address_not_found': {
                'en': 'Address not found for this postal code',
                'ja': '住所がありません'
            }
        };
        
        // Determine message content
        let messageContent = '';
        let messageClass = 'field-message';
        
        if (messageType === 'error') {
            messageContent = messages['zip_error'][currentLang];
            messageClass += ' error';
        } else if (messageType === 'api_unavailable') {
            messageContent = messages['api_unavailable'][currentLang];
            messageClass += ' warning';
        } else if (messageType === 'address_not_found') {
            messageContent = messages['address_not_found'][currentLang];
            messageClass += ' warning';
        }
        
        console.log('Message content:', messageContent);
        console.log('Message class:', messageClass);
        
        if (messageContent) {
            const messageDiv = document.createElement('div');
            messageDiv.className = messageClass;
            messageDiv.textContent = messageContent;
            
            console.log('Created message div:', messageDiv);
            console.log('Parent node:', field.parentNode);
            
            // Insert before the field
            field.parentNode.insertBefore(messageDiv, field);
            console.log('Message inserted into DOM');
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                    console.log('Message auto-removed');
                }
            }, 5000);
        } else {
            console.warn('No message content to display');
        }
    }
    
    // Helper function to show manual entry message
    function showManualEntryMessage() {
        showFieldMessage('zip', 'api_unavailable');
    }
    
    // Test function for message display
    function testMessageDisplay() {
        console.log('Testing message display...');
        showFieldMessage('zip', 'address_not_found');
    }

    // Payment error display function with language support
    function showPaymentError(errorMessages) {
        const container = document.getElementById('paymentErrorContainer');
        if (!container) {
            console.error('Payment error container not found');
            return;
        }

        // Get current language
        const currentLang = document.documentElement.lang || 'en';
        
        // Clear existing error
        container.innerHTML = '';
        
        // Create error div
        const errorDiv = document.createElement('div');
        errorDiv.className = 'payment-error';
        
        // Error title based on language
        const errorTitle = currentLang === 'ja' ? '決済エラー' : 'Payment Error';
        
        let errorContent = `<strong>${errorTitle}</strong>`;
        
        // Add error messages
        if (Array.isArray(errorMessages)) {
            errorMessages.forEach(message => {
                errorContent += `<div>Error ${message.code}: ${message.text}</div>`;
            });
        } else if (typeof errorMessages === 'string') {
            errorContent += `<div>${errorMessages}</div>`;
        }
        
        errorDiv.innerHTML = errorContent;
        container.appendChild(errorDiv);
        
        // Show the container
        container.style.display = 'block';
        
        console.log('Payment error displayed:', errorMessages);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            hidePaymentError();
        }, 10000);
    }

    // Hide payment error function
    function hidePaymentError() {
        const container = document.getElementById('paymentErrorContainer');
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    }

    function searchShippingAddress() {
        const zip = document.getElementById('shippingZip').value;
        if (zip.length === 7) {
            const formattedZip = zip.substring(0, 3) + '-' + zip.substring(3);
            
            // Primary: Google Maps Geocoding API
            fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${formattedZip}+Japan&language=en&key=${GOOGLE_MAPS_API_KEY}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK' && data.results && data.results.length > 0) {
                        const result = data.results[0];
                        const components = result.address_components;
                        
                        let city = '';
                        let state = '';
                        let street = '';
                        let sublocality = [];
                        
                        components.forEach(component => {
                            const types = component.types;
                            if (types.includes('locality')) {
                                city = component.long_name;
                            } else if (types.includes('administrative_area_level_1')) {
                                state = component.long_name;
                            } else if (types.includes('sublocality_level_1')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_2')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_3')) {
                                sublocality.push(component.long_name);
                            } else if (types.includes('sublocality_level_4')) {
                                sublocality.push(component.long_name);
                            }
                        });
                        
                        street = sublocality.reverse().join(', ');
                        
                        // Convert English district names to Japanese-style romanization
                        function convertToJapaneseStyle(text) {
                            if (!text) return text;
                            return text
                                .replace(/\s+Ward\b/g, '-ku')
                                .replace(/\s+City\b/g, '-shi')
                                .replace(/\s+Town\b/g, '-cho')
                                .replace(/\s+Village\b/g, '-mura');
                        }
                        
                        street = convertToJapaneseStyle(street);
                        city = convertToJapaneseStyle(city);
                        
                        document.getElementById('shippingCity').value = city;
                        document.getElementById('shippingState').value = state;
                        document.getElementById('shippingStreet').value = street;
                        document.getElementById('shippingCountry').value = 'Japan';
                    } else if (data.status === 'REQUEST_DENIED' || data.status === 'OVER_QUERY_LIMIT') {
                        console.warn('Google API issue, falling back to zipcloud');
                        searchShippingAddressFallback(zip);
                    } else {
                        showFieldMessage('shippingZip', 'address_not_found');
                    }
                })
                .catch(error => {
                    console.error('Google Maps API error:', error);
                    searchShippingAddressFallback(zip);
                });
        }
    }
    
    // Fallback for shipping address
    function searchShippingAddressFallback(zip) {
        fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zip}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    const result = data.results[0];
                    
                    const prefectureMap = {
                        '北海道': 'Hokkaido', '青森県': 'Aomori', '岩手県': 'Iwate', '宮城県': 'Miyagi',
                        '秋田県': 'Akita', '山形県': 'Yamagata', '福島県': 'Fukushima', '茨城県': 'Ibaraki',
                        '栃木県': 'Tochigi', '群馬県': 'Gunma', '埼玉県': 'Saitama', '千葉県': 'Chiba',
                        '東京都': 'Tokyo', '神奈川県': 'Kanagawa', '新潟県': 'Niigata', '富山県': 'Toyama',
                        '石川県': 'Ishikawa', '福井県': 'Fukui', '山梨県': 'Yamanashi', '長野県': 'Nagano',
                        '岐阜県': 'Gifu', '静岡県': 'Shizuoka', '愛知県': 'Aichi', '三重県': 'Mie',
                        '滋賀県': 'Shiga', '京都府': 'Kyoto', '大阪府': 'Osaka', '兵庫県': 'Hyogo',
                        '奈良県': 'Nara', '和歌山県': 'Wakayama', '鳥取県': 'Tottori', '島根県': 'Shimane',
                        '岡山県': 'Okayama', '広島県': 'Hiroshima', '山口県': 'Yamaguchi', '徳島県': 'Tokushima',
                        '香川県': 'Kagawa', '愛媛県': 'Ehime', '高知県': 'Kochi', '福岡県': 'Fukuoka',
                        '佐賀県': 'Saga', '長崎県': 'Nagasaki', '熊本県': 'Kumamoto', '大分県': 'Oita',
                        '宮崎県': 'Miyazaki', '鹿児島県': 'Kagoshima', '沖縄県': 'Okinawa'
                    };
                    
                    const prefecture = prefectureMap[result.address1] || result.address1;
                    
                    // City mapping for major cities to English
                    const cityMap = {
                        '札幌市': 'Sapporo', '仙台市': 'Sendai', '千葉市': 'Chiba', '横浜市': 'Yokohama',
                        '川崎市': 'Kawasaki', '相模原市': 'Sagamihara', '新潟市': 'Niigata', '静岡市': 'Shizuoka',
                        '浜松市': 'Hamamatsu', '名古屋市': 'Nagoya', '京都市': 'Kyoto', '大阪市': 'Osaka',
                        '堺市': 'Sakai', '神戸市': 'Kobe', '岡山市': 'Okayama', '広島市': 'Hiroshima',
                        '北九州市': 'Kitakyushu', '福岡市': 'Fukuoka', '熊本市': 'Kumamoto'
                    };
                    
                    // Enhanced Japanese to English romanization for cities
                    function romanizeCity(text) {
                        if (!text) return '';
                        
                        const commonWords = {
                            '中央': 'Chuo', '東': 'Higashi', '西': 'Nishi', '南': 'Minami', '北': 'Kita',
                            '新': 'Shin', '本': 'Hon', '元': 'Moto', '上': 'Kami', '下': 'Shimo',
                            '大': 'Dai', '小': 'Ko', '高': 'Taka', '山': 'Yama', '川': 'Kawa',
                            '田': 'Ta', '野': 'No', '原': 'Hara', '池': 'Ike', '橋': 'Hashi',
                            '港': 'Minato', '駅': 'Eki', '区': '-ku', '市': '-shi', '町': '-cho',
                            '村': '-mura', '丁目': '-chome', '番地': '-banchi', '号': '-go'
                        };
                        
                        let romanized = text;
                        Object.entries(commonWords).forEach(([jp, en]) => {
                            romanized = romanized.replace(new RegExp(jp, 'g'), en);
                        });
                        
                        return romanized;
                    }
                    
                    const city = cityMap[result.address2] || romanizeCity(result.address2) || '';
                    function parseJapaneseAddressShipping(address) {
                        if (!address) return { area: '', number: '' };
                        
                        // Pattern to match Japanese address with numbers
                        const numberPattern = /([0-9]+[-−][0-9]+[-−][0-9]+|[0-9]+丁目[0-9]+番[0-9]+号|[0-9]+[−-][0-9]+|[0-9]+丁目|[0-9]+番地?[0-9]*号?)/;
                        const match = address.match(numberPattern);
                        
                        if (match) {
                            const number = match[0];
                            const area = address.replace(number, '').trim();
                            return { area: area, number: number };
                        }
                        
                        return { area: address, number: '' };
                    }
                    
                    const shippingAddressParts = parseJapaneseAddressShipping(result.address3);
                    const street = romanizeCity(shippingAddressParts.area) || '';
                    const shippingApartment = shippingAddressParts.number || '';
                    
                    document.getElementById('shippingCity').value = city;
                    document.getElementById('shippingState').value = prefecture;
                    document.getElementById('shippingStreet').value = street;
                    document.getElementById('shippingApartment').value = shippingApartment;
                    document.getElementById('shippingCountry').value = 'Japan';
                }
            })
            .catch(error => {
                console.error('Fallback API error:', error);
                alert('郵便番号の検索に失敗しました / Failed to search postal code');
            });
    }

    // Credit card formatting and validation functions
    function formatCardNumber(input) {
        // Remove all non-digit characters
        let value = input.value.replace(/\D/g, '');
        
        // Add spaces every 4 digits
        value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        
        input.value = value;
    }

    function detectCardType(cardNumber) {
        const icon = document.getElementById('cardTypeIcon');
        const number = cardNumber.replace(/\s/g, '');
        
        // Remove all existing classes
        icon.className = 'card-type-icon';
        
        // Card type detection for Authorize.Net supported cards only
        if (number.match(/^4/)) {
            // Visa
            icon.classList.add('visa');
        } else if (number.match(/^5[1-5]/) || number.match(/^2[2-7]/)) {
            // Mastercard (including new 2-series BINs)
            icon.classList.add('mastercard');
        } else if (number.match(/^3[47]/)) {
            // American Express
            icon.classList.add('amex');
        } else if (number.match(/^6011|^622[1-9]|^62[3-6]|^627[0-2]|^6277[0-8]|^6278[0-9]|^627[9]|^64[4-9]|^65/)) {
            // Discover
            icon.classList.add('discover');
        } else if (number.match(/^35(2[89]|[3-8][0-9])/)) {
            // JCB
            icon.classList.add('jcb');
        } else if (number.match(/^3[0689]|^30[0-5]/)) {
            // Diners Club
            icon.classList.add('diners');
        }
    }

    function formatExpiry(input) {
        // Only allow digits
        input.value = input.value.replace(/\D/g, '');
        
        // Auto-move to next field for month
        if (input.id === 'expMonth' && input.value.length === 2) {
            document.getElementById('expYear').focus();
        }
    }

    function formatCVV(input) {
        // Only allow digits
        input.value = input.value.replace(/\D/g, '');
    }

    function formatCardName(input) {
        // Allow only letters and spaces, capitalize
        input.value = input.value.replace(/[^a-zA-Z\s]/g, '').toUpperCase();
    }

    function showCVVHelp() {
        const tooltip = document.getElementById('cvvTooltip');
        tooltip.classList.add('show');
    }

    function hideCVVHelp() {
        const tooltip = document.getElementById('cvvTooltip');
        tooltip.classList.remove('show');
    }

    // Function to handle delivery country change
    function handleCountryChange() {
        const countryField = document.getElementById('x_ship_to_country');
        const phoneField = document.getElementById('contact');
        
        if (countryField && phoneField) {
            const selectedCountry = countryField.value;
            updatePhoneWithCountryCode(phoneField, selectedCountry);
            
            // Sync to shipping if checkbox is checked
            syncToShipping();
        }
    }

    // Function to handle shipping country change
    function handleShippingCountryChange() {
        const shippingCountryField = document.getElementById('shippingCountry');
        const shippingPhoneField = document.getElementById('shippingPhone');
        
        if (shippingCountryField && shippingPhoneField) {
            const selectedCountry = shippingCountryField.value;
            updatePhoneWithCountryCode(shippingPhoneField, selectedCountry);
        }
    }

    // Function to update phone number with country code
    function updatePhoneWithCountryCode(phoneField, selectedCountry) {
        if (!phoneField) return;
        
        let currentValue = phoneField.value.trim();
        
        // Remove existing country codes
        currentValue = currentValue.replace(/^\+81\s*/, ''); // Remove +81
        currentValue = currentValue.replace(/^\+1\s*/, '');  // Remove +1
        
        // Add appropriate country code based on selection
        if (selectedCountry === 'Japan') {
            phoneField.value = '+81 ' + currentValue;
        } else if (selectedCountry === 'United States' || selectedCountry === 'Canada') {
            phoneField.value = '+1 ' + currentValue;
        } else {
            phoneField.value = currentValue;
        }
    }

    </script>
</body>
</html>