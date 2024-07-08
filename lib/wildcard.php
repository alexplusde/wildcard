<?php

namespace Alexplusde\Wildcard;

use rex;
use rex_clang;
use rex_config;
use rex_extension_point;
use rex_package;
use rex_sql_table;
use rex_user;
use rex_yform_manager_dataset;

class Wildcard extends rex_yform_manager_dataset
{
    public static function getCurrentFieldName($field = 'text', $separator = '_')
    {
        return $field . $separator . rex_clang::getCurrentId();
    }

    public static function findByWildcard(string $package, string $wildcard): ?self
    {
        return self::query()
             ->where('wildcard', $wildcard)
             ->where('package', $package)
             ->findOne();
    }

    public static function findWildcard(string $wildcard, mixed $clang_code = null)
    {
        $clang_code ??= rex_clang::getCurrent()->getCode();
        $wildcard = self::query()
            ->where('wildcard', $wildcard)
            ->orderByRaw("CASE WHEN package = 'project' OR package = '' THEN 0 ELSE 1 END, name ASC")
            ->findOne();
        if ($wildcard) {
            return $wildcard->getText($clang_code);
        }
        return '';
    }

    public static function parse(string $text, ?int $clang_code = null)
    {
        $open_tag = self::getOpenTag();
        $close_tag = self::getCloseTag();
        $clang_code ??= rex_clang::getCurrent()->getCode();
        $wildcards = self::query()
        ->orderByRaw("CASE WHEN package = 'project' OR package = '' THEN 0 ELSE 1 END, wildcard")
        ->find();

        foreach ($wildcards as $wildcard) {
            $text = str_replace($open_tag . $wildcard->getWildcard() . $close_tag, $wildcard->getText($clang_code), $text);
        }
        return $text;
    }

    public static function replaceWildcards(rex_extension_point $ep): void
    {
        $ep->setSubject(self::parse($ep->getSubject(), null));
    }

    public static function getOpenTag(): string
    {
        return rex_config::get('wildcard', 'open_tag', '{{ ');
    }

    public static function getCloseTag(): string
    {
        return rex_config::get('wildcard', 'close_tag', ' }}');
    }

    /* Allgemeine YOrm-Methoden */

    /* Package (AddOn) */
    /** @api */
    public function getPackage(): ?string
    {
        return $this->getValue('package');
    }

    /** @api */
    public function setPackage(mixed $value): self
    {
        $this->setValue('package', $value);
        return $this;
    }

    /* Platzhalter */
    /** @api */
    public function getWildcard(): ?string
    {
        return $this->getValue('wildcard');
    }

    /** @api */
    public function setWildcard(mixed $value): self
    {
        $this->setValue('wildcard', $value);
        return $this;
    }

    /* Sprachersetzung */
    /** @api */
    public function getText(mixed $clang_code = null): ?string
    {
        if ($clang_code) {
            return $this->getValue('text_' . $clang_code);
        }
        return $this->getValue('text_' . rex_clang::getCurrent()->getCode());
    }

    /** @api */
    public function setText(string $wildcard, array $text = []): self
    {
        foreach ($text as $clang_code => $value) {
            $this->setValue('text_' . $clang_code, $value);
        }
        return $this;
    }

    /* Erstellt am... */
    /** @api */
    public function getCreatedate(): ?string
    {
        return $this->getValue('createdate');
    }

    /** @api */
    public function setCreatedate(string $value): self
    {
        $this->setValue('createdate', $value);
        return $this;
    }

    /* Erstellt von... */
    /** @api */
    public function getCreateuser(): ?rex_user
    {
        return rex_user::get($this->getValue('createuser'));
    }

    /** @api */
    public function setCreateuser(mixed $value): self
    {
        $this->setValue('createuser', $value);
        return $this;
    }

    /* Zuletzt geändert am... */
    /** @api */
    public function getUpdatedate(): ?string
    {
        return $this->getValue('updatedate');
    }

    /** @api */
    public function setUpdatedate(string $value): self
    {
        $this->setValue('updatedate', $value);
        return $this;
    }

    /* Zuletzt geändert von... */
    /** @api */
    public function getUpdateuser(): ?rex_user
    {
        return rex_user::get($this->getValue('updateuser'));
    }

    /** @api */
    public function setUpdateuser(mixed $value): self
    {
        $this->setValue('updateuser', $value);
        return $this;
    }

    /* Extension Points */

    public static function removeClangColumn(rex_extension_point $ep): void
    {
        $table = rex_sql_table::get(rex::getTable('wildcard'));
        $table->removeColumn($ep->getParam('clang')->getCode());
        $table->ensure();
    }

    public static function addClangColumn(rex_extension_point $ep): void
    {
        $table = rex_sql_table::get(rex::getTable('wildcard'));
        $table->ensureColumn($ep->getParam('clang')->getCode());
        $table->ensure();
    }

    public static function packageChoices(): array
    {
        $choices = [];
        foreach (rex_package::getRegisteredPackages() as $package) {
            $choices[$package->getName()] = $package->getName();
        }
        return $choices;
    }
}
