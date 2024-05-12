<?php

/* Tablesets aktualisieren */
$addon = rex_addon::get('wildcard');

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(rex_path::addon($addon->getName(), 'install/rex_wildcard.tableset.json')));
    rex_yform_manager_table::deleteCache();
}

rex_sql_table::get(rex::getTable('wildcard'))
->ensurePrimaryIdColumn()
->ensureColumn(new rex_sql_column('package', 'varchar(191)', false, 'project'))
->ensureColumn(new rex_sql_column('wildcard', 'varchar(191)', false, ''))
->ensureColumn(new rex_sql_column('text_1', 'text', true))
->ensureColumn(new rex_sql_column('createdate', 'datetime'))
->ensureColumn(new rex_sql_column('createuser', 'varchar(191)', false, ''))
->ensureColumn(new rex_sql_column('updatedate', 'datetime'))
->ensureColumn(new rex_sql_column('updateuser', 'varchar(191)', false, ''))
->ensureIndex(new rex_sql_index('package_wildcard', ['package', 'wildcard'], rex_sql_index::UNIQUE))
->ensureIndex(new rex_sql_index('wildcard', ['wildcard']))
->ensureIndex(new rex_sql_index('package', ['package']))
->ensure();
