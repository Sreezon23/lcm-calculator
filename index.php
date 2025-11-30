<?php
header('Content-Type: text/plain');

function gcd($a, $b) {
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}

function lcm($x, $y) {
    if ($x === 0 || $y === 0) {
        return 0;
    }
    return abs($x * $y) / gcd(abs($x), abs($y));
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
    $x = intval($x);
    $y = intval($y);
    $result = lcm($x, $y);
    echo (string)intval($result);
}
?>
