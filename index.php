<?php
header('Content-Type: text/plain');

function stripLeadingZeros($s) {
    $s = ltrim($s, '0');
    return $s === '' ? '0' : $s;
}

function compareStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    $la = strlen($a);
    $lb = strlen($b);

    if ($la < $lb) return -1;
    if ($la > $lb) return 1;

    if ($a === $b) return 0;
    return $a < $b ? -1 : 1;
}

function addStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    $i = strlen($a) - 1;
    $j = strlen($b) - 1;
    $carry = 0;
    $res = '';

    while ($i >= 0 || $j >= 0 || $carry > 0) {
        $da = $i >= 0 ? ord($a[$i]) - 48 : 0;
        $db = $j >= 0 ? ord($b[$j]) - 48 : 0;

        $sum = $da + $db + $carry;
        $digit = $sum % 10;
        $carry = ($sum - $digit) / 10;

        $res = chr($digit + 48) . $res;

        $i--;
        $j--;
    }

    return stripLeadingZeros($res);
}

function subtractStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    $cmp = compareStrings($a, $b);
    if ($cmp === 0) {
        return '0';
    }
    if ($cmp < 0) {
        throw new RuntimeException('subtractStrings requires a >= b');
    }

    $i = strlen($a) - 1;
    $j = strlen($b) - 1;
    $borrow = 0;
    $res = '';

    while ($i >= 0) {
        $da = ord($a[$i]) - 48 - $borrow;
        $db = $j >= 0 ? ord($b[$j]) - 48 : 0;

        if ($da < $db) {
            $da += 10;
            $borrow = 1;
        } else {
            $borrow = 0;
        }

        $digit = $da - $db;
        $res = chr($digit + 48) . $res;

        $i--;
        $j--;
    }

    return stripLeadingZeros($res);
}

function multiplyByDigit($a, $digit) {
    $a = stripLeadingZeros($a);

    if ($digit === 0 || $a === '0') return '0';
    if ($digit === 1) return $a;

    $i = strlen($a) - 1;
    $carry = 0;
    $res = '';

    while ($i >= 0 || $carry > 0) {
        $da = $i >= 0 ? ord($a[$i]) - 48 : 0;
        $prod = $da * $digit + $carry;

        $digitRes = $prod % 10;
        $carry = ($prod - $digitRes) / 10;

        $res = chr($digitRes + 48) . $res;
        $i--;
    }

    return stripLeadingZeros($res);
}

function multiplyStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    if ($a === '0' || $b === '0') return '0';

    if (strlen($b) > strlen($a)) {
        $tmp = $a;
        $a = $b;
        $b = $tmp;
    }

    $res = '0';
    $lenB = strlen($b);

    for ($i = $lenB - 1, $zeros = 0; $i >= 0; $i--, $zeros++) {
        $digit = ord($b[$i]) - 48;
        $partial = multiplyByDigit($a, $digit);
        if ($partial !== '0') {
            $partial .= str_repeat('0', $zeros);
        }
        $res = addStrings($res, $partial);
    }

    return stripLeadingZeros($res);
}

function divmodStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    if ($b === '0') {
        throw new RuntimeException('Division by zero');
    }

    if (compareStrings($a, $b) < 0) {
        return ['0', $a];
    }

    $quotient = '';
    $remainder = '0';
    $lenA = strlen($a);

    for ($i = 0; $i < $lenA; $i++) {
        $remainder = stripLeadingZeros($remainder . $a[$i]);
        $digit = 0;

        for ($d = 0; $d <= 9; $d++) {
            $prod = multiplyByDigit($b, $d);
            if (compareStrings($prod, $remainder) <= 0) {
                $digit = $d;
            } else {
                break;
            }
        }

        if ($digit > 0) {
            $prod = multiplyByDigit($b, $digit);
            $remainder = subtractStrings($remainder, $prod);
        }

        $quotient .= chr($digit + 48);
    }

    $quotient = stripLeadingZeros($quotient);
    $remainder = stripLeadingZeros($remainder);

    return [$quotient, $remainder];
}

function gcdStrings($a, $b) {
    $a = stripLeadingZeros($a);
    $b = stripLeadingZeros($b);

    while ($b !== '0') {
        list(, $r) = divmodStrings($a, $b);
        $a = $b;
        $b = $r;
    }

    return $a;
}

function lcmStrings($x, $y) {
    $x = stripLeadingZeros($x);
    $y = stripLeadingZeros($y);

    if ($x === '0' || $y === '0') {
        return '0';
    }

    $g = gcdStrings($x, $y);
    list($q, ) = divmodStrings($x, $g);
    $result = multiplyStrings($q, $y);

    return stripLeadingZeros($result);
}

function isNonNegativeInteger($value) {
    if (!is_string($value) && !is_int($value)) {
        return false;
    }

    $str = (string)$value;

    if ($str === '') {
        return false;
    }

    if (!ctype_digit($str)) {
        return false;
    }

    return true;
}

$x = isset($_GET['x']) ? $_GET['x'] : null;
$y = isset($_GET['y']) ? $_GET['y'] : null;

if ($x === null || $y === null || !isNonNegativeInteger($x) || !isNonNegativeInteger($y)) {
    echo "NaN";
    exit;
}

$x = (string)$x;
$y = (string)$y;

$result = lcmStrings($x, $y);
echo $result;
exit;
?>
