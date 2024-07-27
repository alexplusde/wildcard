<?php

namespace Alexplusde\Wildcard;

use rex_file;
use rex_path;

use function dirname;

use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;

class Sync
{
    /* Synchronisiere die Wildcard-Dateien mit der Datenbank. */
    private static function getWildcardFiles(?string $packageName = null): array
    {
        if ($packageName) {
            return [rex_path::addon($packageName) . 'wildcard' . DIRECTORY_SEPARATOR . 'translations.json'];
        }
        return glob(rex_path::src('addons') . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'wildcard' . DIRECTORY_SEPARATOR . 'translations.json');
    }

    public static function fileToDb()
    {
        // Hole alle Wildcard-Dateien.
        $wildcardFiles = self::getWildcardFiles();

        foreach ($wildcardFiles as $wildcardFile) {
            // Lese die Datei ein.
            $wildcardData = json_decode(rex_file::get($wildcardFile), true);
            $packageName = basename(dirname($wildcardFile, 2));

            // Trage alle Übersetzungen in die Datenbank ein.
            if (!isset($wildcardData['wildcards'])) {
                continue;
            }

            foreach ($wildcardData['wildcards'] as $wildcard => $data) {
                if (!$wildcard || !$data) {
                    continue;
                }

                $wildcard_dataset = wildcard::findByWildcard($packageName, $wildcard);
                if ($wildcard_dataset && ($wildcard_dataset->getUpdatedate() <= $data['timestamp'])) {
                    // continue;
                }
                if (!$wildcard_dataset) {
                    /** @var Wildcard $yform_wildcard */
                    $wildcard_dataset = Wildcard::create();
                    $wildcard_dataset->setValue('package', $packageName);
                    $wildcard_dataset->setValue('wildcard', $wildcard);
                    $wildcard_dataset->setValue('createuser', '🎴');
                }
                if (isset($data['translations'])) {
                    foreach ($data['translations'] as $lang => $text) {
                        $wildcard_dataset->setValue('text_' . $lang, $text);
                    }
                }
                $wildcard_dataset->setValue('updatedate', $data['timestamp']);
                $wildcard_dataset->save();
            }
        }
    }

    public static function dbToFile()
    {
        // Hole alle Wildcard-Dateien.
        $wildcardFiles = self::getWildcardFiles();

        foreach ($wildcardFiles as $wildcardFile) {
            $packageName = basename(dirname($wildcardFile, 2));
            $wildcardData = ['wildcards' => []];
            $wildcards = Wildcard::query()->where('package', $packageName)->find();
            foreach ($wildcards as $wildcard) {
                $wildcardData['wildcards'][$wildcard->getValue('wildcard')] = [
                    'timestamp' => $wildcard->getUpdatedate(),
                    'translations' => ['de_DE' => $wildcard->getValue('text_de_DE'),
                        'en_GB' => $wildcard->getValue('text_en_GB')],
                ];
            }
            rex_file::put($wildcardFile, json_encode($wildcardData, JSON_PRETTY_PRINT));
        }
    }
}
