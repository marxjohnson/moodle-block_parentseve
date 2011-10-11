<?php
/**
 * A page for managers to configure parents eves
 *
 * Displays a list of users on the system for selection as teachers for this parent's evening,
 * using {@see parentseve_teacher_form()}, and a form for configuration of date, time and
 * appointment length using {@see parentseve_form}.
 *
 * @author Mike Worth, Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009 Taunton's College
 * @package block_parentseve
 * @param id int The ID of an existing parents' evening for editing
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$parentseve = optional_param('parentseve', 0, PARAM_INT);
/**
 * @var string $searchtext The text to filter the list of potential teachers by
 */
$searchtext = optional_param('searchtext', '', PARAM_TEXT);
$removeusers = optional_param('removeselect', array(), PARAM_CLEAN);
$add = optional_param('add', null, PARAM_TEXT);
$remove = optional_param('remove', null, PARAM_TEXT);
$addusers = optional_param('addselect', array(), PARAM_CLEAN);
$removeusers = optional_param('removeselect', array(), PARAM_CLEAN);
$teachers = optional_param('teachers', '', PARAM_TEXT);
$showall = optional_param('showall', false, PARAM_TEXT);
if (!empty($teachers)) {
    $teachers = explode(',', $teachers);
} else {
    $teachers = array();
}
if ($showall) {
    $searchtext = '';
}
require_login($SITE);
$PAGE->set_url('/blocks/parentseve/edit.php', array('id' => $id));

require_once($CFG->dirroot.'/blocks/parentseve/lib.php');

$context = get_context_instance(CONTEXT_BLOCK, $id);
require_capability('block/parentseve:manage', $context);

$parentseve = $DB->get_record('parentseve', array('id' => $parentseve));

/// Print the page header
$navlinks = array();
$navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id, 'type' => 'activity');

if ($parentseve) {
    $navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => $CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id, 'type' => 'activityinstance');
} else {
    $navlinks[] = array('name' => get_string('newparentseve', 'block_parentseve'), 'link' => '', 'type' => 'activityinstance');
}
$navlinks[] = array('name' => get_string('config', 'block_parentseve'), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);


/* require_js(array('yui_yahoo',
                'yui_event',
                'yui_connection',
                'yui_dom',
                'yui_selector',
                $CFG->wwwroot.'/blocks/parentseve/js/lib.js.php'));
 */
require_once $CFG->dirroot.'/blocks/parentseve/parentseve_form.php';

$mform = new parentseve_form();

if ($parentseve) {
    $formdata->parentseve = $parentseve->id;
    $formdata->timestart = $parentseve->timestart;
    $formdata->timeend = $parentseve->timeend;
    $formdata->appointmentlength = $parentseve->appointmentlength/60;
    $formdata->info = $parentseve->info;
} else {
    $formdata = new stdClass;
}

$formdata->id = $id;
$mform->set_data($formdata);

if ($newdata = $mform->get_data()) {
    $newdata->appointmentlength = $newdata->appointmentlength*60;
    unset($newdata->MAX_FILE_SIZE);
    if ($parentseve) {

        // if the evening has been moved to a different day, update any appointments that have already been booked
        if ($parentseve->timestart != $newdata->timestart
            && $parentseve->timeend != $newdata->timeend
            && date('YMd', $parentseve->timestart) == date('YMd', $parentseve->timeend)
            && date('YMd', $newdata->timestart) == date('YMd', $newdata->timeend)
            && date('YMd', $parentseve->timestart) != date('YMd', $newdata->timestart)
            && date('YMd', $parentseve->timeend) != date('YMd', $newdata->timeend)
            ) {

            if($appointments = get_records('parentseve_app', 'parentseveid', $parentseve->id)) {
                foreach($appointments as $appointment) {
                    $time = $appointment->apptime - $parentseve->timestart;
                    $newtime = $newdata->timestart+$time;
                    set_field('parentseve_app', 'apptime', $newtime, 'id', $appointment->id);
                }
            }

        }

        $parentseve->timestart = $newdata->timestart;
        $parentseve->timeend = $newdata->timeend;
        $parentseve->appointmentlength = $newdata->appointmentlength;
        $parentseve->info = $newdata->info;
        $DB->update_record('parentseve',$parentseve);
        redirect($CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id);
    } else {
        $DB->insert_record('parentseve',$newdata);
        redirect($CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id);
    }
}

echo $OUTPUT->header();
$mform->display();


/// Finish the page
echo $OUTPUT->footer();
?>
