<?php

namespace Alexplusde\Wildcard;

use FriendsOfRedaxo\QuickNavigation\Button\ButtonRegistry;
use rex;
use rex_addon;
use rex_config;
use rex_csrf_token;
use rex_extension;
use rex_package;
use rex_url;
use rex_view;
use rex_yform_manager_dataset;
use rex_yform_manager_table;

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_wildcard',
        Wildcard::class,
    );
}

if (rex::isBackend() && rex::isDebugMode() && rex_config::get('wildcard', 'sync', true)) {
    // FragmentScanner::scan();
    Sync::fileToDb();
    // Sync::dbToFile();
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
                'Funktion ',
                'custom',
                static function ($a) {
                    if ('project' != $a['list']->getValue('package') || '' == $a['list']->getValue('package')) {
                        return '';
                    }
                    return $a['subject'];
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


if (rex::isBackend() && \rex_addon::get('wildcard') && \rex_addon::get('wildcard')->isAvailable() && !rex::isSafeMode()) {
    $addon = rex_addon::get('wildcard');
    $page = $addon->getProperty('page');

    if(!rex::getConsole()) {
        $_csrf_key = rex_yform_manager_table::get('rex_wildcard')->getCSRFKey();
        
        $token = rex_csrf_token::factory($_csrf_key)->getUrlParams();

        $params = [];
        $params['table_name'] = 'rex_wildcard'; // Tabellenname anpassen
        $params['rex_yform_manager_popup'] = '0';
        $params['_csrf_token'] = $token['_csrf_token'];
        $params['func'] = 'add';

        $href = rex_url::backendPage('wildcard/entry', $params);

        $page['title'] .= ' <a class="label label-primary tex-primary" style="position: absolute; right: 18px; top: 10px; padding: 0.2em 0.6em 0.3em; border-radius: 3px; color: white; display: inline; width: auto;" href="' . $href . '">+</a>';
        $addon->setProperty('page', $page);
    }
}
