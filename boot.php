<?php

namespace Alexplusde\Wildcard;

use rex;
use rex_addon;
use rex_extension;
use rex_yform_manager_dataset;

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_wildcard',
        Wildcard::class,
    );
}

require_once __DIR__ . '/functions/wildcard.php';

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', 'Alexplusde\Wildcard\Wildcard::replaceWildcards', rex_extension::NORMAL);
}

if(rex::isBackend() && rex::getUser()) {
    \rex_extension::register('CLANG_ADDED', 'Alexplusde\Wildcard\Wildcard::addClangColumn');
    \rex_extension::register('CLANG_DELETED', 'Alexplusde\Wildcard\Wildcard::removeClangColumn');
}
