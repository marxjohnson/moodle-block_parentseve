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
 * Cancels an appointment
 *
 * Allows users with parentseve:cancel to cancel an appointment that's been made.
 * Displays a confirmation form which submits back to this page. If deletion is confirmed,
 * the record for the appointment is deleted.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int id The ID of the appointment being deleted
 * @param bool confirm Whether cancellation has been confirmed
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
$id = required_param('id', PARAM_INT);
$appointment = required_param('appointment', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$context = get_context_instance(CONTEXT_BLOCK, $id);

require_login($SITE);
require_capability('block/parentseve:cancel', $context);

$app_sql = 'SELECT a.id, a.parentseveid, a.apptime, t.firstname, t.lastname
            FROM {parentseve_app} AS a
                JOIN {user} AS t ON a.teacherid = t.id
            WHERE a.id = ?';

$app = $DB->get_record_sql($app_sql, array($appointment));
$parentseve = $DB->get_record('parentseve', array('id' => $app->parentseveid));

$PAGE->set_url('/blocks/parentseve/book.php', array('id' => $id, 'parentseve' => $parentseve->id));

$content = '';
if ($app) {
    if ($confirm) {
        $DB->delete_records('parentseve_app', array('id' => $appointment));
        $redirecturl = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $id, 'parentseve' => $parentseve->id));
        redirect($redirecturl);
    } else {
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
        $PAGE->navbar->add(get_string('cancel'));

        $a->teacher = fullname($app);
        $a->time = date('H:i', $app->apptime);
        $a->date = date('d/M/Y', $app->apptime);

        $content .= $OUTPUT->heading(get_string('appointmentcancel', 'block_parentseve', $a), 3);
        $confirmparams = array(
            'confirm' => true,
            'id' => $id,
            'appointment' => $appointment
        );
        $confirmurl = new moodle_url('/blocks/parentseve/cancel.php', $confirmparams);
        $confirmbutton = new single_button($confirmurl, get_string('yes'));

        $cancelparams = array(
            'id' => $id,
            'parentseve' => $parentseve->id
        );
        $cancelurl = new moodle_url('/blocks/parentseve/schedule.php', $cancelparams);
        $cancelbutton = new single_button($cancelurl, get_string('no'), 'get');

        $content .= $OUTPUT->confirm(get_string('confirmcancel', 'block_parentseve'), $confirmbutton, $cancelbutton);
    }
} else {
    print_error('noappointment', 'block_parentseve');
}

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
