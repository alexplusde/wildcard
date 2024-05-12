<?php

use Alexplusde\Wildcard\Wildcard;

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_wildcard',
        Wildcard::class,
    );
}
