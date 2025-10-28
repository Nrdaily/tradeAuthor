
<?php
// This script creates simple SVG flags - run it once to generate the flag files
$flags = [
    'us' => ['name' => 'United States', 'colors' => ['#B22234', '#3C3B6E', '#FFFFFF']],
    'es' => ['name' => 'Spain', 'colors' => ['#AA151B', '#F1BF00', '#AA151B']],
    'fr' => ['name' => 'France', 'colors' => ['#0055A4', '#FFFFFF', '#EF4135']],
    'de' => ['name' => 'Germany', 'colors' => ['#000000', '#DD0000', '#FFCE00']],
    'cn' => ['name' => 'China', 'colors' => ['#DE2910', '#FFDE00']],
    'ae' => ['name' => 'United Arab Emirates', 'colors' => ['#00732F', '#FFFFFF', '#000000', '#FF0000']]
];

// Create flags directory if it doesn't exist
if (!is_dir(__DIR__)) {
    mkdir(__DIR__, 0755, true);
}

foreach ($flags as $code => $flag) {
    $svg = generateFlagSVG($code, $flag);
    file_put_contents(__DIR__ . "/{$code}.svg", $svg);
    echo "Created {$code}.svg\n";
}

function generateFlagSVG($code, $flag) {
    $width = 20;
    $height = 15;
    
    switch($code) {
        case 'us':
            return generateUSFlag($width, $height);
        case 'es':
            return generateSpainFlag($width, $height);
        case 'fr':
            return generateFranceFlag($width, $height);
        case 'de':
            return generateGermanyFlag($width, $height);
        case 'cn':
            return generateChinaFlag($width, $height);
        case 'ae':
            return generateUAEFlag($width, $height);
        default:
            return generateDefaultFlag($width, $height, $flag['colors']);
    }
}

function generateUSFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<!-- Stripes -->';
    $svg .= '<rect width="20" height="1.15" fill="#B22234"/>';
    $svg .= '<rect y="1.15" width="20" height="1.15" fill="#FFFFFF"/>';
    $svg .= '<rect y="2.3" width="20" height="1.15" fill="#B22234"/>';
    $svg .= '<rect y="3.45" width="20" height="1.15" fill="#FFFFFF"/>';
    $svg .= '<rect y="4.6" width="20" height="1.15" fill="#B22234"/>';
    $svg .= '<rect y="5.75" width="20" height="1.15" fill="#FFFFFF"/>';
    $svg .= '<rect y="6.9" width="20" height="1.15" fill="#B22234"/>';
    $svg .= '<!-- Canton -->';
    $svg .= '<rect width="8" height="4.6" fill="#3C3B6E"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateSpainFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="20" height="5" y="0" fill="#AA151B"/>';
    $svg .= '<rect width="20" height="5" y="5" fill="#F1BF00"/>';
    $svg .= '<rect width="20" height="5" y="10" fill="#AA151B"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateFranceFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="6.66" height="15" x="0" fill="#0055A4"/>';
    $svg .= '<rect width="6.66" height="15" x="6.66" fill="#FFFFFF"/>';
    $svg .= '<rect width="6.66" height="15" x="13.33" fill="#EF4135"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateGermanyFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="20" height="5" y="0" fill="#000000"/>';
    $svg .= '<rect width="20" height="5" y="5" fill="#DD0000"/>';
    $svg .= '<rect width="20" height="5" y="10" fill="#FFCE00"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateChinaFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="20" height="15" fill="#DE2910"/>';
    $svg .= '<!-- Simplified star representation -->';
    $svg .= '<path d="M5,7.5 L4,5 L6,5 Z M5,3 L5,5 L7,4 Z M8,3 L7,5 L9,5 Z M10,4 L8,6 L10,7 Z M10,8 L8,9 L10,10 Z M8,11 L7,10 L9,10 Z M5,11 L5,10 L7,11 Z M3,10 L4,10 L5,11 Z M3,7 L2,8 L4,8 Z M3,5 L2,5 L4,6 Z" fill="#FFDE00"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateUAEFlag($width, $height) {
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="5" height="15" fill="#00732F"/>';
    $svg .= '<rect width="15" height="5" x="5" fill="#FFFFFF"/>';
    $svg .= '<rect width="15" height="5" x="5" y="5" fill="#000000"/>';
    $svg .= '<rect width="15" height="5" x="5" y="10" fill="#FF0000"/>';
    $svg .= '</svg>';
    return $svg;
}

function generateDefaultFlag($width, $height, $colors) {
    $colorStops = '';
    $step = 100 / (count($colors) - 1);
    foreach ($colors as $index => $color) {
        $offset = $index * $step;
        $colorStops .= '<stop offset="' . $offset . '%" stop-color="' . $color . '"/>';
    }
    
    $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<defs>';
    $svg .= '<linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">';
    $svg .= $colorStops;
    $svg .= '</linearGradient>';
    $svg .= '</defs>';
    $svg .= '<rect width="20" height="15" fill="url(#gradient)"/>';
    $svg .= '</svg>';
    
    return $svg;
}
?>