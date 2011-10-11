<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');

$config = get_config('block/parentseve');

if(!$config->allowanon) {
    require_login($SITE);
} else {
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
}

$parentseveid = required_param('parentseveid', PARAM_INT); // Parents evening ID
$teacherid = required_param('teacher', PARAM_INT);

if (!$parentseve = $DB->get_record('parentseve', array('id' => $parentseveid))) {
    error('Parents evening ID was incorrect');
}

if ($parentseve->appointmentlength == 0) {
    print_error('appointmentlengthzero','termreview');
}

//In order to avoid a loop of DB calls, fetch all the relevant appointments then put them into an array which php can manipulate a lot quicker
$appcron = array();
$params = array('teacherid' => $teacherid, 'parentseveid' => $parentseve->id);
if ($appointments = $DB->get_records('parentseve_app', $params, '', 'id, apptime')){
    foreach ($appointments as $appointment) {
        $appcron[$appointment->apptime]=true;
    }
}

$slots = array();
for ($time = $parentseve->timestart; $time < $parentseve->timeend; $time += $parentseve->appointmentlength) {
    $slot = new stdClass;
    $slot->displaytime = date('G:i',$time);
    if (!empty($appcron[$time])) {
        $slot->busy = true;
    } else {
        $slot->busy = false;
    }
    $slot->time = $time;
    $slots[] = $slot;
}

echo json_encode((object)array('slots' => $slots));
