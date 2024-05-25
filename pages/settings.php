<?php

// echo rex_view::title(rex_i18n::msg('wildcard_title'));

$addon = rex_addon::get('wildcard');

$form = rex_config_form::factory($addon->getName());

$field = $form->addInputField('text', 'opentag', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('wildcard_config_opentag_label'));
$field->setNotice(rex_i18n::msg('wildcard_config_opentag_notice'));

$field = $form->addInputField('text', 'closetag', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('wildcard_config_closetag_label'));
$field->setNotice(rex_i18n::msg('wildcard_config_closetag_notice'));

$field = $form->addSelectField('sync');
$field->setLabel(rex_i18n::msg('wildcard_config_sync_label'));
$field->setNotice(rex_i18n::msg('wildcard_config_sync_notice'));
$select = $field->getSelect();
$select->addOption(rex_i18n::msg('wildcard_config_sync_true'), true);
$select->addOption(rex_i18n::msg('wildcard_config_sync_false'), false);


$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $addon->i18n('wildcard_config'), false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
