<?php

/* Tablesets aktualisieren */
$addon = rex_addon::get('wildcard');

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    // Laden Sie die JSON-Datei
    $tableset = rex_file::get(rex_path::addon($addon->getName(), 'install/rex_wildcard.tableset.json'));

    // Konvertieren Sie die JSON-Datei in ein PHP-Array
    $data = json_decode($tableset, true);

    // Durchsuchen Sie das Array nach dem Feld mit dem Namen "wildcard" und der Priorität 2
    foreach ($data['rex_wildcard']['fields'] as $index => $field) {
        if ('wildcard' === $field['name'] && 2 === $field['prio']) {
            foreach (rex_clang::getAll() as $clang) {
                // Erstellen Sie das neue Feld
                $newField = [
                    'table_name' => 'rex_wildcard',
                    'prio' => 3,
                    'type_id' => 'value',
                    'type_name' => 'textarea',
                    'db_type' => 'text',
                    'list_hidden' => 0,
                    'search' => 1,
                    'name' => 'text_' . $clang->getCode(),
                    'label' => 'translate:wildcard_text_' . $clang->getCode(),
                    'not_required' => '',
                    'attributes' => '',
                    'default' => '',
                    'no_db' => '0',
                    'notice' => '',
                ];

                // Fügen Sie das neue Feld direkt nach dem gefundenen Feld ein
                array_splice($data['rex_wildcard']['fields'], $index + 1, 0, [$newField]);
            }
            // Sobald das neue Feld hinzugefügt wurde, beenden Sie die Schleife
            break;
        }
    }

    // Konvertieren Sie das Array zurück in JSON
    $tableset = json_encode($data, JSON_PRETTY_PRINT);

    rex_yform_manager_table_api::importTablesets($tableset);
    rex_yform_manager_table::deleteCache();
}

/* Zusätzliche Eigenschaften an der Tabelle direkt setzen, z.B. Index-Felder zur Performance-Optimierung */

$table = rex_sql_table::get(rex::getTable('wildcard'));
$table = $table->ensurePrimaryIdColumn();
$table = $table->ensureColumn(new rex_sql_column('package', 'varchar(191)', false, 'project'));
$table = $table->ensureColumn(new rex_sql_column('wildcard', 'varchar(191)', false, ''));
foreach (rex_clang::getAll() as $clang) {
    $table = $table->ensureColumn(new rex_sql_column('text_' . $clang->getId(), 'text', true));
    $table = $table->ensureColumn(new rex_sql_column('text_' . rex_string::normalize($clang->getCode()), 'text', true));
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
