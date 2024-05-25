<?php

namespace Alexplusde\Wildcard;

use FriendsOfRedaxo\QuickNavigation\Button\ButtonRegistry;
use rex;
use rex_addon;
use rex_config;
use rex_extension;
use rex_package;
use rex_view;
use rex_yform_manager_dataset;

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_wildcard',
        Wildcard::class,
    );
}

if (rex::isBackend() && rex::isDebugMode() && rex_addon::get('developer') && rex_addon::get('developer')->isAvailable()) {
    FragmentScanner::scan('sh_neues');
    Sync::fileToDb();
}

require_once __DIR__ . '/functions/wildcard.php';

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', 'Alexplusde\Wildcard\Wildcard::replaceWildcards', rex_extension::NORMAL);
}

if (rex::isBackend() && rex::getUser()) {
    rex_extension::register('CLANG_ADDED', 'Alexplusde\Wildcard\Wildcard::addClangColumn');
    rex_extension::register('CLANG_DELETED', 'Alexplusde\Wildcard\Wildcard::removeClangColumn');
}

/* Darstellung im Backend der Datalist ändern */
if (rex::isBackend()) {
    rex_extension::register('YFORM_DATA_LIST', static function ($ep) {
        if ('rex_wildcard' == $ep->getParam('table')->getTableName()) {
            $list = $ep->getSubject();

            $list->setColumnFormat(
                'package',
                'custom',
                static function ($a) {
                    /* get the icon of the package.yml of the addon */
                    $packageIcon = rex_package::get($a['list']->getValue('package'))->getProperty('page')['icon'] ?? 'rex-icon-package';
                    return '<div class="text-nowrap"><i class="rex-icon ' . $packageIcon . '"></i>&nbsp;' . $a['list']->getValue('package') . '</div>';
                },
            );
            $list->setColumnFormat(
                'wildcard',
                'custom',
                static function ($a) {
                    $value = rex_config::get('wildcard', 'opentag') . $a['list']->getValue('wildcard') . rex_config::get('wildcard', 'closetag');
                    return '<div class="text-nowrap" data-wildcard-copy="' . $value . '" role="button"> <i class="rex-icon fa-clone"></i> <code> ' . $a['list']->getValue('wildcard') . '</code></div>';
                },
            );
        }
    });
}

/* Javascript-Asset laden */
if (rex::isBackend() && rex::getUser()) {
    rex_view::addJsFile($this->getAssetsUrl('js/backend.js'));
}

/* Wenn quick_navigation installiert, dann */
ButtonRegistry::registerButton(new QuickNavigationButton(), 5);

if (rex::isDebugMode() && rex_addon::get('developer')->isAvailable()) {
    FragmentsScanner::scan();
}
