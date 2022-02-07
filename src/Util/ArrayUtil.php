<?php

namespace PhpLsh\Util;

final class ArrayUtil
{
    public static function pairs(array $array): array
    {
        if ($array === []) {
            return [];
        }

        $values = array_values($array);
        $n = count($array);
        $pairs = [];
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $pairs[] = [$values[$i], $values[$j]];
            }
        }

        return $pairs;
    }
}
