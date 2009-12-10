<?php
/**
 * A page to display schedules for a parents eve to teachers and managers, and allow anyone else to make appontments
 *
 * @author Mike Worth, Mark Johnson
 * @copyright Copyright &copy; 2009 Taunton's College
 * @package parentseve
 **/

    require_once('../../config.php');
    require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
    require $CFG->libdir.'/tablelib.php';

    $id = required_param('id', PARAM_INT);   

    if (!$parentseve = get_record('parentseve', 'id', $id)) {
        print_error('noparentseve', 'block_parentseve');
    }

    $context = get_context_instance(CONTEXT_SYSTEM);
    if(!has_capability('block/parentseve:manage', $context) && $parentseve->timeend < time()) {
        print_error('oldparentseve', 'block_parentseve');
    }

/// Print the page header

    $navlinks = array();
    if(has_capability('block/parentseve:manage', $context)) {
        $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php', 'type' => 'activity'); 
    } else {
        $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
    }
    $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');
    $navigation = build_navigation($navlinks);

    print_header_simple(get_string('parentseveschedule','block_parentseve'), '', $navigation, "", "", true, '');

        add_to_log(0, 'parentseve', 'View schedule', $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id, $id);

        echo '<a href="'.$CFG->wwwroot.'/blocks/parentseve/book.php?id='.$id.'">'.get_string('bookapps','block_parentseve').'</a>';

    //Display tabs
    if(has_capability('block/parentseve:viewall', $context)) {
        //show all teachers' schedules

        $teachers = parentseve_get_teachers($parentseve);
        foreach($teachers as $teacher){
            parentseve_print_schedule($teacher,$parentseve);
        }

    } else if (parentseve_isteacher($USER->id,$parentseve)) {
        //show current users schedule
        
        if(!parentseve_print_schedule($USER,$parentseve)){
            print_string('emptyschedule','block_parentseve',$USER->firstname.' '.$USER->lastname);
        }
        
    } else {
        print_error('nopermissions');
    }

/// Finish the page
    print_footer();
?>
