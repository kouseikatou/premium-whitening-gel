// Japanese to English address romanization utility
function createRomanizationSystem() {
    // Comprehensive city mapping to English
    const cityMap = {
        // Major cities
        '札幌市': 'Sapporo', '仙台市': 'Sendai', '千葉市': 'Chiba', '横浜市': 'Yokohama',
        '川崎市': 'Kawasaki', '相模原市': 'Sagamihara', '新潟市': 'Niigata', '静岡市': 'Shizuoka',
        '浜松市': 'Hamamatsu', '名古屋市': 'Nagoya', '京都市': 'Kyoto', '大阪市': 'Osaka',
        '堺市': 'Sakai', '神戸市': 'Kobe', '岡山市': 'Okayama', '広島市': 'Hiroshima',
        '北九州市': 'Kitakyushu', '福岡市': 'Fukuoka', '熊本市': 'Kumamoto',
        // Additional major cities
        '青森市': 'Aomori', '盛岡市': 'Morioka', '秋田市': 'Akita', '山形市': 'Yamagata',
        '福島市': 'Fukushima', '水戸市': 'Mito', '宇都宮市': 'Utsunomiya', '前橋市': 'Maebashi',
        'さいたま市': 'Saitama', '甲府市': 'Kofu', '長野市': 'Nagano', '岐阜市': 'Gifu',
        '津市': 'Tsu', '大津市': 'Otsu', '奈良市': 'Nara', '和歌山市': 'Wakayama',
        '鳥取市': 'Tottori', '松江市': 'Matsue', '徳島市': 'Tokushima', '高松市': 'Takamatsu',
        '松山市': 'Matsuyama', '高知市': 'Kochi', '佐賀市': 'Saga', '長崎市': 'Nagasaki',
        '大分市': 'Oita', '宮崎市': 'Miyazaki', '鹿児島市': 'Kagoshima', '那覇市': 'Naha'
    };

    // Prefecture mapping
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

    // Function to romanize Japanese text
    function romanizeJapanese(text) {
        if (!text) return '';
        
        // Common district/area suffixes mapping
        const suffixMap = {
            '区': '-ku', '市': '-shi', '町': '-cho', '村': '-mura', '丁目': '-chome',
            '番地': '-banchi', '号': '-go', '条': '-jo', '丁': '-cho'
        };
        
        // Common area name mappings
        const areaMap = {
            '中央': 'Chuo', '東': 'Higashi', '西': 'Nishi', '南': 'Minami', '北': 'Kita',
            '新': 'Shin', '本': 'Hon', '元': 'Moto', '上': 'Kami', '下': 'Shimo',
            '中': 'Naka', '外': 'Soto', '内': 'Uchi', '大': 'Dai', '小': 'Ko',
            '高': 'Taka', '山': 'Yama', '川': 'Kawa', '田': 'Ta', '野': 'No',
            '原': 'Hara', '池': 'Ike', '橋': 'Hashi', '港': 'Minato', '駅': 'Eki'
        };
        
        let romanized = text;
        
        // Replace area names first (order matters)
        Object.entries(areaMap).forEach(([jp, en]) => {
            romanized = romanized.replace(new RegExp(jp, 'g'), en);
        });
        
        // Replace suffixes
        Object.entries(suffixMap).forEach(([jp, en]) => {
            romanized = romanized.replace(new RegExp(jp, 'g'), en);
        });
        
        return romanized;
    }

    return {
        cityMap,
        prefectureMap,
        romanizeJapanese
    };
}

// Export for use in index.php
window.RomanizationSystem = createRomanizationSystem();