<?php

namespace Alexplusde\Wildcard;

use rex_file;
use rex_path;

class FragmentScanner
{
    /**
     * @var string
     */
    private $packageDir;
    /**
     * @var string
     */
    private $wildcardFile;

    /**
     * @var array
     */
    private $wildcardContent;

    /**
     * FragmentScanner constructor.
     *
     * @param string $packageName
     */
    public function __construct(string $packageDir)
    {
        $this->packageDir = $packageDir;
        $this->wildcardFile = $packageDir . 'wildcard'.\DIRECTORY_SEPARATOR.'translations.json';
        $this->wildcardContent = $this->getWildcardContent();
    }

    /**
     * Liefert alle Fragment-Dateien, die Platzhalter enthalten könnten.
     *
     * @return array
     */
    public function getFiles(): array
    {
        return array_merge(
            glob($this->packageDir . 'fragments' . \DIRECTORY_SEPARATOR . '*.php', GLOB_BRACE),
            glob($this->packageDir . 'fragments' . \DIRECTORY_SEPARATOR . '**' . \DIRECTORY_SEPARATOR . '*.php', GLOB_BRACE)
        );
    }

    /**
     * Erstellt die Wildcard-Datei, wenn sie nicht existiert.
     */
    private function ensureWildcardFileExists(): void
    {
        if (!file_exists($this->wildcardFile)) {
            rex_file::put($this->wildcardFile, json_encode(['wildcards' => []], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Gibt den Inhalt der Wildcard-Datei als PHP-Array zurück.
     *
     * @return array
     */
    private function getWildcardContent(): ?array
    {
        return json_decode(rex_file::get($this->wildcardFile), true);
    }

    /**
     * Überprüft, ob der Key bereits in Wildcard vorhanden ist.
     *
     * @param string $key
     * @return bool
     */
    public function keyExists(string $key): bool
    {
        if(!isset($this->wildcardContent['wildcards'])) {
            return false;
        }
        return array_key_exists($key, $this->wildcardContent['wildcards']);
    }

    /**
     * Fügt den Key zu den Wildcards hinzu.
     *
     * @param string $key
     */
    public function addKey(string $key): void
    {
        if (!$this->keyExists($key)) {
            $this->wildcardContent['wildcards'][$key] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'translations' => []
            ];
        }
    }

    /**
     * Schreibt die Wildcard-Datei in das zugehörige Addon-Verzeichnis.
     */
    public function writeWildcardFile(): void
    {
        $this->ensureWildcardFileExists();
        rex_file::put($this->wildcardFile, json_encode($this->wildcardContent, JSON_PRETTY_PRINT));
    }

    /**
     * Führt die Scan-Funktion für alle Addons oder ein spezifisches Addon aus und aktualisiert die Wildcard-Dateien.
     *
     * @param string $dir
     */
    public static function scan(string $packageName = null): void
    {
        $dirs = glob(rex_path::addon('*'));
        if($packageName) {
            $dirs = [rex_path::addon($packageName)];
        }

        foreach ($dirs as $dir) {
            $scanner = new FragmentScanner($dir);
            $files = $scanner->getFiles();
            foreach ($files as $file) {
                $content = rex_file::get($file);
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
