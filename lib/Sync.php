<?php

namespace Alexplusde\Wildcard;

use rex_config;
use rex_path;

class Sync
{
    /* Synchronisiere die Wildcard-Dateien mit der Datenbank. */
    private static function getWildcardFiles(string $packageName = null) :array
    {
        if($packageName) {
            return [rex_path::addon($packageName) . 'wildcard'. \DIRECTORY_SEPARATOR .'translations.json'];
        }
        return glob(rex_path::src('addons') . \DIRECTORY_SEPARATOR . '*' . \DIRECTORY_SEPARATOR . 'wildcard'. \DIRECTORY_SEPARATOR .'translations.json');
    }
    public static function fileToDb()
    {
        // Hole alle Wildcard-Dateien.
        $wildcardFiles = self::getWildcardFiles();

        foreach ($wildcardFiles as $wildcardFile) {
            // Lese die Datei ein.
            $wildcardData = json_decode(\rex_file::get($wildcardFile), true);
            $packageName = basename(dirname(dirname($wildcardFile)));

            // Trage alle Übersetzungen in die Datenbank ein.
            if(!isset($wildcardData['wildcards'])) {
                continue;
            }

            foreach($wildcardData['wildcards'] as $wildcard => $data) {

                if(!$wildcard || !$data) {
                    continue;
                }

                $wildcard_dataset = wildcard::findByWildcard($packageName, $wildcard);
                if($wildcard_dataset && ($wildcard_dataset->getUpdatedate() <= $data['timestamp'])) {
                    // continue;
                }
                if(!$wildcard_dataset) {
                    /** @var Wildcard $yform_wildcard */
                    $wildcard_dataset = Wildcard::create();
                    $wildcard_dataset->setValue('package', $packageName);
                    $wildcard_dataset->setValue('wildcard', $wildcard);
                    $wildcard_dataset->setValue('createuser', '🎴');
                }
                if(isset($data['de_DE'])) {
                    dd($data['de_DE']);
                    $wildcard_dataset->setValue('text_de_DE', $data['de_DE']);
                }
                $wildcard_dataset->setValue('updatedate', $data['timestamp']);
                $wildcard_dataset->save();

            }
            
        }
    }
}
