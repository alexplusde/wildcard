<?php

use Alexplusde\Wildcard;

function wildcard(string $wildcard, mixed $clang_code = null)
{
    return Wildcard::findWildcard($wildcard, $clang_code);
}

function ParseWildcards(string $text, mixed $clang_code = null)
{
    return Wildcard::parse($text, $clang_code);
}
