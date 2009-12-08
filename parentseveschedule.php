<?php
/**
 * A page to display schedules for a parents eve to teachers and managers, and allow anyone else to make appontments
 *
 * @author Mike Worth, Mark Johnson
 * @copyright Copyright &copy; 2009 Taunton's College
 * @package parentseve
 **/

    require_once('../../config.php');
    require_once($CFG->wwwroot.'/blocks/parentseve/lib.php');

    $bookappointments = optional_param('book',false, PARAM_BOOL);

    $parentname = optional_param('parentname', null, PARAM_TEXT);
    $studentname = optional_param('studentname', null, PARAM_TEXT);
    $newappointments = optional_param('appointment', null, PARAM_TEXT);
    $newappointmentteachers = optional_param('appointmentteacher', null, PARAM_TEXT);

    if (!$parentseve = get_record('parentseve', 'id', $parentreview->id)) {
        print_error('noparentseve', 'block_parentseve');
    }

    $context = get_context_instance(CONTEXT_SYSTEM);
    if(!has_capability('mod/parentseve:manage', $context) && $parentseve->timeend < time()) {
        print_error('oldparentseve', 'block_parentseve');
    }

/// Print the page header

    $navlinks = array();
    $navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
    $navlinks[] = array('name' => date($parentseve->timestart, 'l jS M Y'), 'link' => '', 'type' => 'activityinstance');
    $navigation = build_navigation($navlinks);

    print_header_simple(get_string('parentseveschedule','block_parentseve'), '', $navigation, "", "", true,
                  '');

    //Display tabs
    termreview_printtabs($cmid, $termreview, $parentreview, 3);
    if (!$CFG->termreview_enableparentseves) {
        print_error('parentsevesdisabled','parentseve');
    } elseif(!$bookappointments  && empty($parentname) and has_capability('blocks/parentseve:viewallparentseve', $context)) {
        //show all teachers' schedules
        add_to_log(0, 'parentseve', 'View schedule', 'parentseveschedule.php?id='.$cmid, $cmid);

        echo '<a href="'.$CFG->wwwroot.'/mod/termreview/parentseveschedule.php?book=1&cmid='.$cmid.'">'.get_string('bookapps','block_parentseve').'</a>';

        $teachers = parentseve_get_teachers($parentseve->id);
        foreach($teachers as $teacher){
            parentseve_print_schedule($teacher,$parentseve);
        }
    //is this user on the list of users who can termreview:write in any context?
    } elseif (!$bookappointments  && empty($parentname) and parentseve_isteacher($USER->id,$parentseve->id)) {
        //show current users schedule
        add_to_log(0, 'parentseve', 'View schedule', 'parentseveschedule.php?id='.$cmid, $cmid);

        echo '<p><a href="'.$CFG->wwwroot.'/mod/termreview/parentseveschedule.php?book=1&cmid='.$cmid.'">'.get_string('bookapps','block_parentseve').'</a></p>';

        if(!termreview_print_pe_schedule($USER,$parentseve)){
            print_string('emptyschedule','block_parentseve',$USER->firstname.' '.$USER->lastname);
        }
    } else {
        //can only book appointments, not view
        add_to_log(0, 'termreview', 'View schedule', 'parentseveschedule.php?id='.$cmid, $cmid);

        if(empty($parentname)) {

            echo get_string('parentseveon', 'block_parentseve', array('date'=>date('l jS F Y',$parentseve->timestart))).'<p class="termreview_pe_info">'.$parentseve->info.'</p>
                <noscript>This page requires javascript to be enabled</noscript><!--TODO:lang-->
                <script type="text/javascript" src="'.$CFG->wwwroot.'/mod/termreview/parentseveschedule.js.php?peid='.$parentseve->id.'">

                </script>
                <form method="post" action="parentseveschedule.php" id="termreview_pe_form" onSubmit="return termreview_pe_validate(this)" ><input name="cmid" type="hidden" value="'.$cmid.'"><div id="termreview_pe_names">'.get_string('parentname','block_parentseve').'<input type="text" name="parentname">'.get_string('studentname','block_parentseve').'<input type="text" name="studentname"></div>
                <div id="termreview_pe_buttons"><button type="button" onClick="newAppointment()">'.get_string('newapp','block_parentseve').'</button>
                <input type="submit" value="'.get_string('confirmapps','block_parentseve').'"></div><div id="termreview_pe_appointments"><!--AJAX will put the schedules in here--></div><div style="clear:both;"></div></form>';
        } else {
            $success = 0;
            $fail = 0;
            //must have submitted the booking form
            foreach ($newappointments as $key => $newappointment) {
                if ($teacher = get_record('user','id',$newappointmentteachers[$key])) {
                    $appointment = new object();
                    $appointment->parentseveid = $parentseve->id;
                    $appointment->teacherid = $newappointmentteachers[$key];
                    $appointment->apptime = $newappointment;
                    $appointment->parentname = $parentname;
                    $appointment->studentname = $studentname;
                    if(get_record('termreview_parentseve_app','teacherid',$appointment->teacherid,'apptime',$appointment->apptime)==false and insert_record('termreview_parentseve_app',$appointment)) {
                        echo get_string('appbooked','block_parentseve',array('teacher'=>$teacher->firstname.' '.$teacher->lastname,'apptime'=>date('G:i',$appointment->apptime))).'<br>';
                        $success++;
                    } else {
                        echo get_string('appnotbooked','block_parentseve',array('teacher'=>$teacher->firstname.' '.$teacher->lastname,'apptime'=>date('G:i',$appointment->apptime))).'<br>';
                        $fail++;
                    }
                }
            }
            echo '<br>'.get_string('success','block_parentseve',$success);
            if ($fail>0) echo '<br>'.get_string('fail','block_parentseve',$fail);
            echo '<h4>'.get_string('printsave','block_parentseve').'</h4>';
            echo '<p><a href="'.$CFG->wwwroot.'/mod/termreview/parentseveschedule.php?cmid='.$cmid.'">'.get_string('backtoappointments', 'block_parentseve').'</a></p>';
        }
    }

/// Finish the page
    print_footer();
?>
