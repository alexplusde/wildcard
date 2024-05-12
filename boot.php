<?php

namespace Alexplusde\Wildcard;

if (\rex_addon::get('yform')->isAvailable() && !\rex::isSafeMode()) {
    \rex_yform_manager_dataset::setModelClass(
        'rex_wildcard',
        Wildcard::class,
    );
}

require_once __DIR__ . '/functions/wildcard.php';

if (\rex::isFrontend()) {
    \rex_extension::register('OUTPUT_FILTER', "Alexplusde\Wildcard\Wildcard::replaceWildcards", \rex_extension::NORMAL);
}
