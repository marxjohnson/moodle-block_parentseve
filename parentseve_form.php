<?php
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

require_once ($CFG->libdir.'/formslib.php');

/**
 * Defines the configuration form
 * 
 */
class parentseve_form extends moodleform {
    
    /**
     * Defines the form elements
     */
    function definition() {
        $mform    =& $this->_form;
        $mform->addElement('header', 'parentseveheader', get_string('createnew', 'block_parentseve'));
        $mform->addElement('date_time_selector', 'timestart', get_string('timestart', 'block_parentseve'));
        $mform->addElement('date_time_selector', 'timeend', get_string('timeend', 'block_parentseve'));
        $mform->addElement('text','appointmentlength',get_string('appointmentlength', 'block_parentseve')); // will have to take this in minutes until figure out duration element type (moodle 2.0)
        $mform->addElement('htmleditor', 'info', get_string('parentseveinfo', 'block_parentseve'),'rows="10" cols="25"');

        $this->add_action_buttons(false);
    }
}

?>