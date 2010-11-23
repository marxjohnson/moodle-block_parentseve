<?php
/**
 * Cancels an appointment
 * 
 * Allows users with parentseve:cancel to cancel an appointment that's been made.
 * Displays a confirmation form which submits back to this page. If deletion is confirmed,
 * the record for the appointment is deleted.
 * 
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int id The ID of the appointment being deleted
 * @param bool confirm Whether cancellation has been confirmed
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
$id = required_param('id', PARAM_INT);
$appointment = required_param('appointment', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$context = get_context_instance(CONTEXT_BLOCK, $id);
require_capability('block/parentseve:cancel', $context);
$app_sql = 'SELECT a.id, a.parentseveid, a.apptime, t.firstname, t.lastname
            FROM '.$CFG->prefix.'parentseve_app AS a
                JOIN '.$CFG->prefix.'user AS t ON a.teacherid = t.id
            WHERE a.id = '.$appointment;
$app = get_record_sql($app_sql);
$parentseve = get_record('parentseve', 'id', $app->parentseveid);
if ($app) {
    if ($confirm) {    
        delete_records('parentseve_app', 'id', $appointment);
        redirect($CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id.'&parentseve='.$parentseve->id);
    } else {
        $navlinks = array();
        if(has_capability('block/parentseve:manage', $context)) {
            $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id, 'type' => 'activity');
        } else {
            $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
        }
        if(has_capability('block/parentseve:viewall', $context) || parentseve_isteacher($USER->id, $parentseve)) {
            $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id.'&parentseve='.$parentseve->id, 'type' => 'activityinstance');
        } else {
            $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');    
        }
        $navlinks[] = array('name' => get_string('cancel'), 'link' => '', 'type' => 'activityinstance');
        $navigation = build_navigation($navlinks);
        
        print_header_simple(get_string('cancel'), '', $navigation, "", "", true, '');
        $a->teacher = fullname($app);
        $a->time = date('H:i', $app->apptime);
        $a->date = date('d/M/Y', $app->apptime);
        echo '<p>'.get_string('appointmentcancel', 'block_parentseve', $a).'</p>
        <p>'.get_string('confirmcancel', 'block_parentseve').'</p>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/cancel.php">
        <input type="hidden" name="confirm" value="1" />
        <input type="hidden" name="id" value="'.$id.'" />
        <input type="hidden" name="appointment" value="'.$appointment.'" />
        <input type="submit" value="'.get_string('yes').'" />
        </form>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id.'&parentseve='.$parentseve->id.'">
        <input type="submit" value="'.get_string('no').'" />
        </form>'
        ;
    }
} else {
    print_error('noappointment', 'block_parentseve');
}

print_footer();
?>
