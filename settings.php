<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


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
