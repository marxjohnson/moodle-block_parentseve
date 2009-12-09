<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
$id = required_param('id', PARAM_INT);   

if (!$parentseve = get_record('parentseve', 'id', $id)) {
    print_error('noparentseve', 'block_parentseve');
}

$context = get_context_instance(CONTEXT_SYSTEM);
if(!has_capability('block/parentseve:manage', $context) && $parentseve->timeend < time()) {
    print_error('oldparentseve', 'block_parentseve');
}

$parentname = optional_param('parentname', null, PARAM_TEXT);
$studentname = optional_param('studentname', null, PARAM_TEXT);
$newappointments = optional_param('appointment', null, PARAM_TEXT);
$newappointmentteachers = optional_param('appointmentteacher', null, PARAM_TEXT);

$navlinks = array();
$navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
$navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

print_header_simple(get_string('bookapps','block_parentseve'), '', $navigation, "", "", true, '');


if(empty($parentname)) {

    add_to_log(0, 'parentseve', 'View booking form', $CFG->wwwroot.'/blocks/parentseve/book.php?id='.$id, $id);
    echo get_string('parentseveon', 'block_parentseve', array('date'=>date('l jS F Y',$parentseve->timestart))).'<p class="_info">'.$parentseve->info.'</p>
        <noscript>This page requires javascript to be enabled</noscript><!--TODO:lang-->
        <script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/parentseve/book.js.php?id='.$parentseve->id.'">

        </script>
        <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/book.php" id="parentseve_form" onSubmit="return parentseve_validate(this)" >
            <input name="id" type="hidden" value="'.$id.'">
            <div id="_names">'
                .get_string('parentname','block_parentseve').'<input type="text" name="parentname">'
                .get_string('studentname','block_parentseve').'<input type="text" name="studentname">
            </div>
        <div id="parentseve_buttons"><button type="button" onClick="newAppointment()">'.get_string('newapp','block_parentseve').'</button>
        <input type="submit" value="'.get_string('confirmapps','block_parentseve').'"></div><div id="parentseve_appointments"><!--AJAX will put the schedules in here--></div><div style="clear:both;"></div></form>';
} else {
    add_to_log(0, 'parentseve', 'Submit booking', $CFG->wwwroot.'/blocks/parentseve/book.php?id='.$id, $id);
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
            if(!get_record('parentseve_app', 'teacherid', $appointment->teacherid, 'apptime', $appointment->apptime)) {
                insert_record('parentseve_app', $appointment);
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
    echo '<p><a href="'.$CFG->wwwroot.'/blocks/parentseve/book.php?id='.$id.'">'.get_string('backtoappointments', 'block_parentseve').'</a></p>';
}
print_footer();
?>