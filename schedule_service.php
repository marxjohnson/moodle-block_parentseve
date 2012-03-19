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
 * AJAX script to return the availablibilty of a teacher
 *
 * Accepts a teacher ID and a parents evening ID, and returns a JSON
 * array of appointments, indicating whether the teacher is avilable
 * or booked for each slot.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

define(AJAX_SCRIPT, true);
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');

$config = get_config('block/parentseve');

if (!$config->allowanon) {
    require_login($SITE);
} else {
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
}

$parentseveid = required_param('parentseveid', PARAM_INT); // Parents evening ID
$teacherid = required_param('teacher', PARAM_INT);

if (!$parentseve = $DB->get_record('parentseve', array('id' => $parentseveid))) {
    header('HTTP/1.1 404 Not Found');
    die(get_string('parentsevenotfound', 'block_parentseve'));
}

if ($parentseve->appointmentlength == 0) {
    header('HTTP/1.1 500 Internal Server Error');
    die(get_string('appointmentlengthzero', 'block_parentseve'));
}

// In order to avoid a loop of DB calls, fetch all the relevant appointments then put them
// into an array which php can manipulate a lot quicker
$appcron = array();
$params = array('teacherid' => $teacherid, 'parentseveid' => $parentseve->id);
if ($appointments = $DB->get_records('parentseve_app', $params, '', 'id, apptime')) {
    foreach ($appointments as $appointment) {
        $appcron[$appointment->apptime]=true;
    }
}

$slots = array();
$start = $parentseve->timestart;
$end = $parentseve->timeend;
$length = $parentseve->appointmentlength;
for ($time = $start; $time < $end; $time += $length) {
    $slot = new stdClass;
    $slot->displaytime = date('G:i', $time);
    if (!empty($appcron[$time])) {
        $slot->busy = true;
    } else {
        $slot->busy = false;
    }
    $slot->time = $time;
    $slots[] = $slot;
}

echo json_encode((object)array('slots' => $slots));
