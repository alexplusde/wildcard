<?php

namespace Alexplusde\Wildcard;

use rex_config;

class Sync
{
    public static function sync()
    {
        /* Prüfe, ob die Datei neuer ist als der Zeitstempel in der Datenbank */
        if (date(rex_config::get('wildcard', 'syncdatestamp') >= date())) {
        }
    }
}
