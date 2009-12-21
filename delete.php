<?php
/**
 * Deletes a parents' evening
 * 
 * Much like {@see cancel.php}, this displays a confirmation form which, once submitted,
 * will delete and entire parents' evening and associated appointments.
 * 
 * @package block_parenteseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int id The ID of the parents' evening record to delete
 * @param int confirm Whether the deletion has been confirmed 
 */

require_once('../../config.php');
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/parentseve:manage', $context);
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$parentseve = get_record('parentseve', 'id', $id);
if ($parentseve) {
    if ($confirm) {    
        delete_records('parentseve_app', 'parentseveid', $id);
        delete_records('parentseve', 'id', $id);
        redirect($CFG->wwwroot.'/blocks/parentseve/manage.php');
    } else {
        $navlinks = array();
        if(has_capability('block/parentseve:manage', $context)) {
            $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php', 'type' => 'activity'); 
        } else {
            $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
        }
        if(has_capability('block/parentseve:viewall', $context) || parentseve_isteacher($USER->id, $parentseve)) {
            $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id, 'type' => 'activityinstance');   
        } else {
            $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');    
        }
        $navlinks[] = array('name' => get_string('delete'), 'link' => '', 'type' => 'activityinstance');
        $navigation = build_navigation($navlinks);
        
        print_header_simple(get_string('delete'), '', $navigation, "", "", true, '');
        $a->date = date('d/M/Y', $parentseve->timestart);
        $a->time = date('H:i', $parentseve->timestart);
        echo '<p>'.get_string('parentsevedelete', 'block_parentseve', $a).'</p>
        <p>'.get_string('confirmdelete', 'block_parentseve').'</p>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/delete.php">
        <input type="hidden" name="confirm" value="1" />
        <input type="hidden" name="id" value="'.$id.'" />
        <input type="submit" value="'.get_string('yes').'" />
        </form>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/manage.php">
        <input type="submit" value="'.get_string('no').'" />
        </form>'
        ;
    }
} else {
    print_error('noparentseve', 'block_parentseve');
}

print_footer();
?>