
<?php
// language.php - Language configuration and functions

// Available languages with base64 encoded SVG flags
$available_languages = [
    'en' => [
        'name' => 'English', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIxLjE1IiBmaWxsPSIjQjIyMjM0Ii8+PHJlY3QgeT0iMS4xNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjEuMTUiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB5PSIyLjMiIHdpZHRoPSIyMCIgaGVpZ2h0PSIxLjE1IiBmaWxsPSIjQjIyMjM0Ii8+PHJlY3QgeT0iMy40NSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjEuMTUiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB5PSI0LjYiIHdpZHRoPSIyMCIgaGVpZ2h0PSIxLjE1IiBmaWxsPSIjQjIyMjM0Ii8+PHJlY3QgeT0iNS43NSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjEuMTUiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB5PSI2LjkiIHdpZHRoPSIyMCIgaGVpZ2h0PSIxLjE1IiBmaWxsPSIjQjIyMjM0Ii8+PHJlY3Qgd2lkdGg9IjgiIGhlaWdodD0iNC42IiBmaWxsPSIjM0MzQjZFIi8+PC9zdmc+',
        'file' => 'en.json'
    ],
    'es' => [
        'name' => 'Español', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSI1IiB5PSIwIiBmaWxsPSIjQUExNTFCIi8+PHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjUiIHk9IjUiIGZpbGw9IiNGMUJGMDkiLz48cmVjdCB3aWR0aD0iMjAiIGhlaWdodD0iNSIgeT0iMTAiIGZpbGw9IiNBQTE1MUIiLz48L3N2Zz4=',
        'file' => 'es.json'
    ],
    'fr' => [
        'name' => 'Français', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSI2LjY2IiBoZWlnaHQ9IjE1IiB4PSIwIiBmaWxsPSIjMDA1NUE0Ii8+PHJlY3Qgd2lkdGg9IjYuNjYiIGhlaWdodD0iMTUiIHg9IjYuNjYiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB3aWR0aD0iNi42NiIgaGVpZ2h0PSIxNSIgeD0iMTMuMzMiIGZpbGw9IiNFRjQxMzUiLz48L3N2Zz4=',
        'file' => 'fr.json'
    ],
    'de' => [
        'name' => 'Deutsch', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSI1IiB5PSIwIiBmaWxsPSIjMDAwMDAwIi8+PHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjUiIHk9IjUiIGZpbGw9IiNERDAwMDAiLz48cmVjdCB3aWR0aD0iMjAiIGhlaWdodD0iNSIgeT0iMTAiIGZpbGw9IiNGRkNFMDAiLz48L3N2Zz4=',
        'file' => 'de.json'
    ],
    'zh' => [
        'name' => '中文', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIxNSIgZmlsbD0iI0RFMjkxMCIvPjxwYXRoIGQ9Ik01LDcuNSBMNCw1IEw2LDUgWiBNNSwzIEw1LDUgTDcsNCBaIE04LDMgTDcsNSBMOSw1IFogTTEwLDQgTDgsNiBMMTAsNyBaIE0xMCw4IEw4LDkgTDEwLDEwIFogTTgsMTEgTDcsMTAgTDksMTAgWiBNNSwxMSBMNSwxMCBMNywxMSBaIE0zLDEwIEw0LDEwIEw1LDExIFogTTMsNyBMMiw4IEw0LDggWiBNMyw1IEwyLDUgTDQsNiBaIiBmaWxsPSIjRkZERTAwIi8+PC9zdmc+',
        'file' => 'zh.json'
    ],
    'ar' => [
        'name' => 'العربية', 
        'flag' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMC9zdmciPjxyZWN0IHdpZHRoPSI1IiBoZWlnaHQ9IjE1IiBmaWxsPSIjMDA3MzJGIi8+PHJlY3Qgd2lkdGg9IjE1IiBoZWlnaHQ9IjUiIHg9IjUiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB3aWR0aD0iMTUiIGhlaWdodD0iNSIgeD0iNSIgeT0iNSIgZmlsbD0iIzAwMDAwMCIvPjxyZWN0IHdpZHRoPSIxNSIgaGVpZ2h0PSI1IiB4PSI1IiB5PSIxMCIgZmlsbD0iI0ZGMDAwMCIvPjwvc3ZnPg==',
        'file' => 'ar.json'
    ]
];

// Get current language
function getCurrentLanguage() {
    return $_SESSION['language'] ?? 'en';
}

// Set language
function setLanguage($language) {
    global $available_languages;
    
    if (array_key_exists($language, $available_languages)) {
        $_SESSION['language'] = $language;
        return true;
    }
    
    return false;
}

// Load translations
function loadTranslations($language) {
    $file_path = __DIR__ . "/../../assets/lang/{$language}.json";
    
    if (file_exists($file_path)) {
        $json = file_get_contents($file_path);
        return json_decode($json, true);
    }
    
    // Fallback to English
    if ($language !== 'en') {
        $file_path = __DIR__ . "/../../assets/lang/en.json";
        if (file_exists($file_path)) {
            $json = file_get_contents($file_path);
            return json_decode($json, true);
        }
    }
    
    return [];
}

// Translate function
function t($key, $default = '') {
    static $translations = null;
    
    if ($translations === null) {
        $language = getCurrentLanguage();
        $translations = loadTranslations($language);
    }
    
    return $translations[$key] ?? $default ?: $key;
}

// Echo translation
function _e($key, $default = '') {
    echo t($key, $default);
}
?>