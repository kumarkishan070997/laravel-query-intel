<?php

namespace Kishan\QueryIntel\Support;

class QueryNormalizer
{
    public static function normalize(string $sql): string
    {
        $sql = strtolower($sql);
        $sql = preg_replace('/\s+/', ' ', $sql);
        $sql = preg_replace("/'(.*?)'/", '?', $sql);
        $sql = preg_replace('/\b\d+\b/', '?', $sql);

        return trim($sql);
    }
}
