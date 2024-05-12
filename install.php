<?php

/* Tablesets aktualisieren */
$addon = rex_addon::get('wildcard');

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(rex_path::addon($addon->getName(), 'install/rex_wildcard.tableset.json')));
    rex_yform_manager_table::deleteCache();
}

$table = rex_sql_table::get(rex::getTable('wildcard'));
$table = $table->ensurePrimaryIdColumn();
$table = $table->ensureColumn(new rex_sql_column('package', 'varchar(191)', false, 'project'));
$table = $table->ensureColumn(new rex_sql_column('wildcard', 'varchar(191)', false, ''));
foreach(rex_clang::getAll() as $clang) {
    $table = $table->ensureColumn(new rex_sql_column('text_'.$clang->getId(), 'text', true));
    $table = $table->ensureColumn(new rex_sql_column('text_'.rex_normalize($clang->getCode()), 'text', true));
}
$table = $table->ensureColumn(new rex_sql_column('createdate', 'datetime'));
$table = $table->ensureColumn(new rex_sql_column('createuser', 'varchar(191)', false, ''));
$table = $table->ensureColumn(new rex_sql_column('updatedate', 'datetime'));
$table = $table->ensureColumn(new rex_sql_column('updateuser', 'varchar(191)', false, ''));
$table = $table->ensureIndex(new rex_sql_index('package_wildcard', ['package', 'wildcard'], rex_sql_index::UNIQUE));
$table = $table->ensureIndex(new rex_sql_index('wildcard', ['wildcard']));
$table = $table->ensureIndex(new rex_sql_index('package', ['package']));
$table->ensure();

/* Zeitstempel, um sich zu merken, wann zuletzt Dateien aus dem Filesystem synchronisiert wurden */
rex_config::set('wildcard', 'syncdatestamp', date('Y-m-d H:i:s'));
