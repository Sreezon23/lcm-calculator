<?php
header('Content-Type: text/plain');

function gcd($a, $b) {
    $a = (int)$a;
    $b = (int)$b;
    
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return abs($a);
}

function lcm($x, $y) {
    $x = (int)$x;
    $y = (int)$y;
    
    if ($x === 0 || $y === 0) {
        return 0;
    }
    
    $g = gcd($x, $y);
    return abs(($x / $g) * $y);
}

function isNonNegativeInteger($value) {
    if (!is_string($value) && !is_int($value)) {
        return false;
    }
    $str = (string)$value;
    if (!ctype_digit($str)) {
        return false;
    }
    return true;
}

$x = isset($_GET['x']) ? $_GET['x'] : null;
$y = isset($_GET['y']) ? $_GET['y'] : null;

if ($x === null || $y === null || !isNonNegativeInteger($x) || !isNonNegativeInteger($y)) {
    echo "NaN";
} else {
    $result = lcm($x, $y);
    echo (string)(int)$result;
}
exit;
?>
