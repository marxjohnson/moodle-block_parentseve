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
require_login($SITE);

$id = required_param('id', PARAM_INT);
$parentseve = required_param('parentseve', PARAM_INT);
$justmyschedule = optional_param('my', 0, PARAM_BOOL);

if (!$parentseve = $DB->get_record('parentseve', array('id' => $parentseve))) {
    print_error('noparentseve', 'block_parentseve');
}

$context = get_context_instance(CONTEXT_BLOCK, $id);
if(!has_capability('block/parentseve:manage', $context) && $parentseve->timeend < time()) {
    print_error('oldparentseve', 'block_parentseve');
}

/// Print the page header
$PAGE->set_url('/block/parentseve/schedule.php', array('id' => 39389, 'parentseve' => 10));
if(has_capability('block/parentseve:manage', $context)) {
    $url = new moodle_url('/blocks/parentseve/manage.php', array('id' => $id));
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'), $url);
} else {
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'));
}
$PAGE->navbar->add(date('l jS M Y', $parentseve->timestart));
$output = $PAGE->get_renderer('block_parentseve');

add_to_log(0, 'parentseve', 'View schedule', $PAGE->url, $id);

$content = $output->booking_link($id, $parentseve);

$is_teacher = parentseve_isteacher($USER->id,$parentseve);
$cancancel = has_capability('block/parentseve:cancel', $context);

if(has_capability('block/parentseve:viewall', $context) || $is_teacher) {

    if ($justmyschedule) {

         // Show link to user's own schedule
        $content .= $output->allschedules_link($id, $parentseve);
        $schedule = parentseve_get_schedule($USER, $parentseve, $id);
        $content .= $output->schedule_table($id, $parentseve, $schedule, $cancancel);
    } else {
        if ($is_teacher) {
            // Show link to user's own schedule
            $content .= $output->myschedule_link($id, $parentseve);
        }

        //show all teachers' schedules
        $teachers = parentseve_get_teachers($parentseve);
        foreach($teachers as $teacher) {
            $schedule = parentseve_get_schedule($teacher, $parentseve, $id);
            $headingtext = get_string('schedulefor', 'block_parentseve', $teacher->firstname.' '.$teacher->lastname);
            $content .= $OUTPUT->heading($headingtext, 3, 'parentseve_schedule_header');
            $content .= $output->schedule_table($id, $parentseve, $schedule, $cancancel);
        }
    }

} else {
    print_error('nopermissions');
}

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
