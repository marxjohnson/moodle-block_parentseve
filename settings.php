<?php
$settings->add(new admin_setting_configcheckbox('allowanon', get_string('allowanon', 'block_parentseve'), get_string('allowanon_explain', 'block_parentseve'), '1'));
$settings->settings->allowanon->plugin='block/parentseve';
?>
