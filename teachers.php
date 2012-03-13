<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays form for selecting users as teachers for the current parents' evening
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
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
$urlparams = array('id' => $id, 'parentseve' => $parentseve->id);
$PAGE->set_url('/blocks/parentseve/teachers.php', $urlparams);

if (has_capability('block/parentseve:manage', $context)) {
    $url = new moodle_url('/blocks/parentseve/manage.php', array('id' => $id));
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'), $url);
} else {
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'));
}
$viewall = has_capability('block/parentseve:viewall', $context);
$isteacher = parentseve_isteacher($USER->id, $parentseve);
if ($viewall || $isteacher) {
    $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $parentseve->id));
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart), $url);
} else {
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart));
}
$PAGE->navbar->add(get_string('config', 'block_parentseve'));

$output = $PAGE->get_renderer('block_parentseve');

$potential_selector = new parentseve_teacher_selector('potential_selector', $parentseve);
$selected_selector = new parentseve_selected_teacher_selector('selected_selector', $parentseve);

if (!empty($add)) {
    $newteachers = $potential_selector->get_selected_users();
    foreach ($newteachers as $id => $newteacher) {
        $teacher = new stdClass;
        $teacher->parentseveid = $parentseve->id;
        $teacher->userid = $id;
        $teacher->id = $DB->insert_record('parentseve_teacher', $teacher);
    }
}

if (!empty($remove)) {
    $oldteachers = $selected_selector->get_selected_users();
    foreach ($oldteachers as $id => $oldteacher) {
        $teacherparams = array('parentseveid' => $parentseve->id, 'userid' => $id);
        if ($teacher = $DB->get_record('parentseve_teacher', $teacherparams)) {
            $DB->delete_records('parentseve_teacher', array('id' => $teacher->id));
        }
    }

}


$content = $output->teacher_selector($potential_selector, $selected_selector);

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
