<?php
/**
 * Display the appointment booking form
 *
 * Displays a page containing fields for the student's and parent's name,
 * along with a button to add a new appointment. The button uses an AJAX call to {@see book_ss.php}
 * to display a list of teachers and times for each requested appointment.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009 Taunton's College, Southampton, UK
 * @param int id The ID of the parents' evening
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
$id = optional_param('id', 0, PARAM_INT);
$parentseve = required_param('parentseve', PARAM_INT);
$config = get_config('block/parentseve');
$context = get_context_instance(CONTEXT_BLOCK, $id);

if(!$config->allowanon) {
    require_login($SITE);
    require_capability('block/parentseve:book', $context);
} else {
    $PAGE->set_context($context);
}

if (!$parentseve = $DB->get_record('parentseve', array('id' => $parentseve))) {
    print_error('noparentseve', 'block_parentseve');
}

if(!has_capability('block/parentseve:manage', $context) && $parentseve->timeend < time()) {
    print_error('oldparentseve', 'block_parentseve');
}

$parentname = optional_param('parentname', null, PARAM_TEXT);
$studentname = optional_param('studentname', null, PARAM_TEXT);
$newappointments = optional_param('appointment', null, PARAM_TEXT);
$newappointmentteachers = optional_param('appointmentteacher', null, PARAM_TEXT);

$PAGE->set_url('/blocks/parentseve/book.php', array('id' => $id, 'parentseve' => $parentseve->id));
if(has_capability('block/parentseve:manage', $context)) {
    $url = new moodle_url('/blocks/parentseve/manage.php', array('id' => $id));
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'), $url);
} else {
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'));
}
if(has_capability('block/parentseve:viewall', $context) || parentseve_isteacher($USER->id, $parentseve)) {
    $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $id, 'parentseve' => $parentseve->id));
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart), $url);
} else {
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart));
}
$PAGE->navbar->add(get_string('book', 'block_parentseve'));

$output = $PAGE->get_renderer('block_parentseve');

$jsmodule = array(
    'name'     => 'block_parentseve',
    'fullpath' => '/blocks/parentseve/module.js',
    'requires' => array('base', 'io', 'node', 'json', 'selector-css3'),
    'strings' => array(
        array('teacher', 'block_parentseve'),
        array('noappointments', 'block_parentseve'),
        array('noparentname', 'block_parentseve'),
        array('nostudentname', 'block_parentseve'),
        array('noappointmentwith', 'block_parentseve'),
        array('mustcorrect', 'block_parentseve'),
        array('cancel', 'moodle'),
        array('busy', 'block_parentseve'),
        array('selectteacher', 'block_parentseve')
    )
);
$teachers = parentseve_get_teachers($parentseve);
$PAGE->requires->js_init_call('M.block_parentseve.init', array($teachers, $parentseve->id), false, $jsmodule);

$content = $OUTPUT->heading(get_string('bookapps','block_parentseve'), 1);

if (empty($parentname)) {

    add_to_log(0, 'parentseve', 'View booking form', $PAGE->url->out(), $id);
    $content .= $output->booking_info($parentseve->timestart, $parentseve->info);
    $content .= $output->booking_form($PAGE->url);

} else {
    add_to_log(0, 'parentseve', 'Submit booking', $PAGE->url->out(false), $id);
    $successes = array();
    $failures = array();

    //must have submitted the booking form
    foreach ($newappointments as $key => $newappointment) {
        if ($teacher = $DB->get_record('user', array('id' => $newappointmentteachers[$key]))) {
            $appointment = new object();
            $appointment->parentseveid = $parentseve->id;
            $appointment->teacherid = $newappointmentteachers[$key];
            $appointment->apptime = $newappointment;
            $appointment->parentname = $parentname;
            $appointment->studentname = $studentname;
            if(!$DB->record_exists('parentseve_app', array('teacherid' => $appointment->teacherid, 'apptime' => $appointment->apptime))) {
                if ($DB->insert_record('parentseve_app', $appointment)) {
                    $appointment->teacher = fullname($teacher);
                    $successes[] = $appointment;
                } else {
                    $appointment->teacher = fullname($teacher);
                    $failures[] = $appointment;
                }
            } else {
                $appointment->teacher = fullname($teacher);
                $failures[] = $appointment;
            }
        }
    }

    $content .= $output->booking_response($successes, $failures, $PAGE->url);

}

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
?>
