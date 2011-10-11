<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/teacher_selector.php');

$id = required_param('id', PARAM_INT);
$parentseve = required_param('parentseve', PARAM_INT);
$add = optional_param('add', '', PARAM_TEXT);
$remove = optional_param('remove', '', PARAM_TEXT);

if (!$parentseve = $DB->get_record('parentseve', array('id' => $parentseve))) {
    print_error('noparentseve', 'block_parentseve');
}

require_login($SITE);
$context = get_context_instance(CONTEXT_BLOCK, $id);

require_capability('block/parentseve:manage', $context);
$PAGE->set_url('/blocks/parentseve/teachers.php', array('id' => $id, 'parentseve' => $parentseve->id));

if(has_capability('block/parentseve:manage', $context)) {
    $url = new moodle_url('/blocks/parentseve/manage.php', array('id' => $id));
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'), $url);
} else {
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'));
}
if(has_capability('block/parentseve:viewall', $context) || parentseve_isteacher($USER->id, $parentseve)) {
    $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $parentseve->id));
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart), $url);
} else {
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart));
}
$PAGE->navbar->add(get_string('config', 'block_parentseve'));

$output = $PAGE->get_renderer('block_parentseve');

$potential_selector = new parentseve_teacher_selector('potential_selector', $parentseve);
$selected_selector = new parentseve_selected_teacher_selector('selected_selector', $parentseve);

if(!empty($add)) {
    $newteachers = $potential_selector->get_selected_users();
    foreach ($newteachers as $id => $newteacher) {
        $teacher = new stdClass;
        $teacher->parentseveid = $parentseve->id;
        $teacher->userid = $id;
        $teacher->id = $DB->insert_record('parentseve_teacher', $teacher);
    }
}

if(!empty($remove)) {
    $oldteachers = $selected_selector->get_selected_users();
    foreach ($oldteachers as $id => $oldteacher) {
        if($teacher = $DB->get_record('parentseve_teacher', array('parentseveid' => $parentseve->id, 'userid' => $id))) {
            $DB->delete_records('parentseve_teacher', array('id' => $teacher->id));
        }
    }

}


$content = $output->teacher_selector($potential_selector, $selected_selector);

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
?>
