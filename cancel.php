<?php
require_once('../../config.php');
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/parentseve:cancel', $context);
$app_sql = 'SELECT a.id, a.parentseveid, a.apptime, t.firstname, t.lastname
            FROM '.$CFG->prefix.'parentseve_app AS a
                JOIN '.$CFG->prefix.'user AS t ON a.teacherid = t.id
            WHERE a.id = '.$id;
$app = get_record_sql($app_sql);
$parentseve = get_record('parentseve', 'id', $app->parentseveid);
if ($app) {
    if ($confirm) {    
        delete_records('parentseve_app', 'id', $id);
        redirect($CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id);
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
        <input type="submit" value="'.get_string('yes').'" />
        </form>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id.'">
        <input type="submit" value="'.get_string('no').'" />
        </form>'
        ;
    }
} else {
    print_error('noappointment', 'block_parentseve');
}

print_footer();
?>
