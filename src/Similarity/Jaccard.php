<?php

namespace PhpLsh\Similarity;

final class Jaccard
{
    public static function similarity(array $a, array $b): float
    {
        $intersect = array_unique(array_intersect($a, $b));
        $union = array_unique(array_merge($a, $b));

        return count($intersect) / count($union);
    }

    public static function distance(array $a, array $b): float
    {
        return 1 - self::similarity($a, $b);
    }
}
