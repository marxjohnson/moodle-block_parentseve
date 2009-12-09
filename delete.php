<?php
require_once('../../config.php');
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/parentseve:manage', $systemcontext);
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
        $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
        $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');
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