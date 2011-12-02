<?php
/**
 * Defines global configuration for the block
 * 
 * Defines the 'allowanon' setting to be displayed on the block's global configuration screen.
 * 
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */
$settings->add(new admin_setting_configcheckbox('allowanon', get_string('allowanon', 'block_parentseve'), get_string('allowanon_explain', 'block_parentseve'), '1'));
$settings->add(new admin_setting_configtext('altmethod', get_string('altmethod', 'block_parentseve'),get_string('altmethodlong', 'block_parentseve'),''));
$settings->settings->allowanon->plugin='block/parentseve';
$settings->settings->altmethod->plugin='block/parentseve';
?>
