<?php
/**
 * A page for managers to configure parents eves
 *
 * @author Mike Worth, Mark Johnson
 * @copyright Copyright &copy; 2009 Taunton's College
 * @package parentseve
 **/
    require_once('../../config.php');

    $id = optional_param('id', 0, PARAM_INT);
    $searchtext = optional_param('searchtext', '', PARAM_TEXT);

    require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
    require_login();

    $parentseve = get_record('parentseve', 'id', $id);    
    $context = get_context_instance(CONTEXT_SYSTEM);
    require_capability('block/parentseve:manage', $context);
    /// Print the page header
    $navlinks = array();
    $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php', 'type' => 'activity'); 

    if ($parentseve) {
        $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id, 'type' => 'activityinstance');
    } else {
        $navlinks[] = array('name' => get_string('newparentseve', 'block_parentseve'), 'link' => '', 'type' => 'activityinstance');
    }
    $navlinks[] = array('name' => get_string('config', 'block_parentseve'), 'link' => '', 'type' => 'activityinstance');

    $navigation = build_navigation($navlinks);
  
    $context = get_context_instance(CONTEXT_SYSTEM);
    require_capability('block/parentseve:manage', $context);


     require_js(array('yui_yahoo',
                    'yui_event',
                    'yui_connection',
                    'yui_dom',
                    'yui_selector',
                    $CFG->wwwroot.'/blocks/parentseve/js/lib.js.php'));

    require_once $CFG->dirroot.'/blocks/parentseve/parentseve_form.php';

    $mform = new parentseve_form();

    // Get all users with a role assigned
    $sql = 'SELECT u.*
            FROM mdl_user AS u
                JOIN mdl_role_assignments AS a ON a.userid = u.id
            ORDER BY firstname ASC';
    $unselectedusers = get_records_sql($sql);

    if ($parentseve) {
        $parentseve->appointmentlength = $parentseve->appointmentlength/60;
        $selectedusers = parentseve_get_teachers($parentseve);
        $formdata->teachers = implode(',', array_keys($selectedusers));
        $mform->set_data($parentseve);
    } else {
    	$selectedusers = array();
    }

    $unselectedusers = array_diff_key($unselectedusers, $selectedusers);

    if ($newdata = $mform->get_data()) {
        $newdata->appointmentlength = $newdata->appointmentlength*60;
        unset($newdata->MAX_FILE_SIZE);
        if (!empty($parentseve)) {
            $newdata->id = $parentseve->id;
            update_record('parentseve',$newdata);
            redirect($CFG->wwwroot.'/blocks/parentseve/manage.php');
        } else {
            insert_record('parentseve',$newdata);
            redirect($CFG->wwwroot.'/blocks/parentseve/manage.php');
        }
    }
    
    print_header_simple(get_string('parentseveconfig','block_parentseve'), '', $navigation, "", "", true, '');
    parentseve_teacher_form($selectedusers, $unselectedusers);
    $mform->display();

/// Finish the page
    print_footer();
?>
