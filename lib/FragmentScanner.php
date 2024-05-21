<?php

namespace Alexplusde\Wildcard;

use function array_key_exists;

use const GLOB_BRACE;
use const GLOB_ONLYDIR;
use const JSON_PRETTY_PRINT;

class FragmentsScanner
{
    /** @var string */
    private $addonDir;

    /** @var string */
    private $wildcardFile;

    /** @var array */
    private $wildcardContent;

    /**
     * FragmentsScanner constructor.
     */
    public function __construct(string $addonDir)
    {
        $this->addonDir = $addonDir;
        $this->wildcardFile = $this->addonDir . '/wildcard/translations.json';
        $this->ensureWildcardFileExists();
        $this->wildcardContent = $this->getWildcardContent();
    }

    /**
     * Liefert alle Fragment-Dateien, die Platzhalter enthalten könnten.
     */
    public function getFiles(): array
    {
        return glob($this->addonDir . '/fragments/**/*.php', GLOB_BRACE);
    }

    /**
     * Erstellt die Wildcard-Datei, wenn sie nicht existiert.
     */
    private function ensureWildcardFileExists(): void
    {
        if (!file_exists($this->wildcardFile)) {
            file_put_contents($this->wildcardFile, json_encode(['wildcards' => []], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Gibt den Inhalt der Wildcard-Datei als PHP-Array zurück.
     */
    private function getWildcardContent(): array
    {
        return json_decode(file_get_contents($this->wildcardFile), true);
    }

    /**
     * Überprüft, ob der Key bereits in Wildcard vorhanden ist.
     */
    public function keyExists(string $key): bool
    {
        return array_key_exists($key, $this->wildcardContent['wildcards']);
    }

    /**
     * Fügt den Key zu den Wildcards hinzu.
     */
    public function addKey(string $key): void
    {
        if (!$this->keyExists($key)) {
            $this->wildcardContent['wildcards'][$key] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'translations' => [],
            ];
        }
    }

    /**
     * Schreibt die Wildcard-Datei in das zugehörige Addon-Verzeichnis.
     */
    public function writeWildcardFile(): void
    {
        file_put_contents($this->wildcardFile, json_encode($this->wildcardContent, JSON_PRETTY_PRINT));
    }

    /**
     * Führt die Scan-Funktion für alle Addons oder ein spezifisches Addon aus und aktualisiert die Wildcard-Dateien.
     */
    public static function scan(string $dir = '/www/redaxo/src/addons/*'): void
    {
        $directories = glob($dir, GLOB_ONLYDIR | GLOB_BRACE);

        foreach ($directories as $dir) {
            $scanner = new self($dir);
            $files = $scanner->getFiles();
            foreach ($files as $file) {
                $content = file_get_contents($file);
                preg_match_all('/{{(.*?)}}/', $content, $matches);
                $keys = $matches[1];
                foreach ($keys as $key) {
                    $scanner->addKey($key);
                }
            }
            $scanner->writeWildcardFile();
        }
    }
}
