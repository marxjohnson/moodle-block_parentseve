<?php
/**
 * A page to display schedules for a parents eve to teachers and managers, 
 * and allow anyone else to make appontments.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int $id The ID of the parent's evening
 **/

    require_once('../../config.php');
    require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
    require $CFG->libdir.'/tablelib.php';

    $id = required_param('id', PARAM_INT);
    $parentseve = required_param('parentseve', PARAM_INT);
    $justmyschedule = optional_param('my', 0, PARAM_BOOL);

    if (!$parentseve = get_record('parentseve', 'id', $parentseve)) {
        print_error('noparentseve', 'block_parentseve');
    }

    $context = get_context_instance(CONTEXT_BLOCK, $id);
    if(!has_capability('block/parentseve:manage', $context) && $parentseve->timeend < time()) {
        print_error('oldparentseve', 'block_parentseve');
    }

/// Print the page header

    $navlinks = array();
    if(has_capability('block/parentseve:manage', $context)) {
        $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id, 'type' => 'activity');
    } else {
        $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
    }
    $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');
    $navigation = build_navigation($navlinks);

    print_header_simple(get_string('parentseveschedule','block_parentseve'), '', $navigation, "", "", true, '');

        add_to_log(0, 'parentseve', 'View schedule', $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id, $id);

        echo '<a href="'.$CFG->wwwroot.'/blocks/parentseve/book.php?id='.$id.'&amp;parentseve='.$parentseve->id.'">'.get_string('bookapps','block_parentseve').'</a>';
    
    $is_teacher = parentseve_isteacher($USER->id,$parentseve);
    if(has_capability('block/parentseve:viewall', $context) || $is_teacher) {
     
        if ($justmyschedule) {
            
             // Show link to user's own schedule
                echo '<p><a href="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id.'&amp;parentseve='.$parentseve->id.'">';
                    print_string('allschedules', 'block_parentseve');
                echo '</a></p>';
            
            parentseve_print_schedule($USER,$parentseve, $id);
        } else {
            if ($is_teacher) {
                // Show link to user's own schedule
                echo '<p><a href="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$id.'&amp;parentseve='.$parentseve->id.'&amp;my=1">';
                    print_string('justmyschedule', 'block_parentseve');
                echo '</a></p>';
            }
        
            //show all teachers' schedules
            $teachers = parentseve_get_teachers($parentseve);
            foreach($teachers as $teacher) {
                parentseve_print_schedule($teacher,$parentseve, $id);
            }
        }

    } else {
        print_error('nopermissions');
    }

/// Finish the page
    print_footer();
?>
