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
 * Defines forms for {@see edit.php}
 *
 * Defines {@see parentseve_form} and {@see parenteseve_teacher_form()} for displaying
 * forms in edit.php, used for creating and editing of parents' evenings.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines the configuration form
 *
 */
class parentseve_form extends moodleform {

    /**
     * Defines the form elements
     */
    public function definition() {
        global $DB;
        $mform    =& $this->_form;
        $mform->addElement('header',
                           'parentseveheader',
                           get_string('createnew', 'block_parentseve'));
        $mform->addElement('date_time_selector',
                           'timestart',
                           get_string('timestart', 'block_parentseve'));
        $mform->addElement('date_time_selector',
                           'timeend',
                           get_string('timeend', 'block_parentseve'));
        $mform->addElement('text',
                           'appointmentlength',
                           get_string('appointmentlength', 'block_parentseve'));
        $mform->addElement('htmleditor',
                           'info',
                           get_string('parentseveinfo', 'block_parentseve'),
                           'rows="10" cols="25"');
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'parentseve');
        $this->add_action_buttons(false);
    }
}
