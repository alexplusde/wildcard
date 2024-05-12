<?php

namespace Alexplusde\Wildcard;

use rex_clang;
use rex_yform_manager_dataset;
use rex_user;

class Wildcard extends rex_yform_manager_dataset {

    public static function getCurrentFieldName($field = "text", $separator = '_')
    {
        return $field . $separator . rex_clang::getCurrentId();
    }

    public static function findWildcard(string $wildcard, int $clang_id = null)
    {
        $clang_id = $clang_id ?? rex_clang::getCurrentId();
        $wildcard = self::query()
            ->where('wildcard', $wildcard)
            ->findOne();
        if($wildcard) {
            return $wildcard->getText($clang_id);
        }
        return "";
    }

    public static function parse(string $text, int $clang_id = null)
    {
        $open_tag = self::getOpenTag();
        $close_tag = self::getCloseTag();
        $clang_id = $clang_id ?? rex_clang::getCurrentId();
        $wildcards = self::query()
            ->find();
        foreach ($wildcards as $wildcard) {
            $text = str_replace($open_tag . $wildcard->getWildcard() . $close_tag, $wildcard->getText($clang_id), $text);
        }
        return $text;
    }

    public static function replaceWildcards(\rex_extension_point $ep): void
    {
        $ep->setSubject(Wildcard::parse($ep->getSubject(), null));
    }

    public static function getOpenTag() :string
    {
        return \rex_config::get('wildcard', 'open_tag', '{{ ');
    }

    public static function getCloseTag() :string
    {
        return \rex_config::get('wildcard', 'close_tag', ' }}');
    }

    /* Allgemeine YOrm-Methoden */
    
    /* Package (AddOn) */
    /** @api */
    public function getPackage() : ?string {
        return $this->getValue("package");
    }
    /** @api */
    public function setPackage(mixed $value) : self {
        $this->setValue("package", $value);
        return $this;
    }

    /* Platzhalter */
    /** @api */
    public function getWildcard() : ?string {
        return $this->getValue("wildcard");
    }
    /** @api */
    public function setWildcard(mixed $value) : self {
        $this->setValue("wildcard", $value);
        return $this;
    }

    /* Sprachersetzung */
    /** @api */
    public function getText($clang_id = null) : ?string {
        if($clang_id) {
            return $this->getValue("text_" . $clang_id);
        }
        return $this->getValue("text_".rex_clang::getCurrentId());
    }

    /** @api */
    public function setText(string $wildcard, array $text = []) : self {
        foreach($text as $clang_id => $value) {
            $this->setValue("text_" . $clang_id, $value);
        }
        return $this;
    }
            
    /* Erstellt am... */
    /** @api */
    public function getCreatedate() : ?string {
        return $this->getValue("createdate");
    }
    /** @api */
    public function setCreatedate(string $value) : self {
        $this->setValue("createdate", $value);
        return $this;
    }

    /* Erstellt von... */
    /** @api */
    public function getCreateuser() : ?rex_user {
        return rex_user::get($this->getValue("createuser"));
    }
    /** @api */
    public function setCreateuser(mixed $value) : self {
        $this->setValue("createuser", $value);
        return $this;
    }

    /* Zuletzt geändert am... */
    /** @api */
    public function getUpdatedate() : ?string {
        return $this->getValue("updatedate");
    }
    /** @api */
    public function setUpdatedate(string $value) : self {
        $this->setValue("updatedate", $value);
        return $this;
    }

    /* Zuletzt geändert von... */
    /** @api */
    public function getUpdateuser() : ?rex_user {
        return rex_user::get($this->getValue("updateuser"));
    }
    /** @api */
    public function setUpdateuser(mixed $value) : self {
        $this->setValue("updateuser", $value);
        return $this;
    }

    /* Extension Points */

    public static function clangDeleted(\rex_extension_point $ep)
    {
        $deleteLang = \rex_sql::factory();
        $deleteLang->setQuery('DELETE FROM '.\rex::getTable('sprog_wildcard').' WHERE clang_id=?', [$ep->getParam('clang')->getId()]);
    }


}
