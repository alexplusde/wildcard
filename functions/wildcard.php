<?php

use Alexplusde\Wildcard;

function wildcard(string $wildcard, ?int $clang_id = null)
{
    return Wildcard::findWildcard($wildcard, $clang_id);
}

function ParseWildcards(string $text, ?int $clang_id = null)
{
    return Wildcard::parse($text, $clang_id);
}
