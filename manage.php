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
 * Manage parents' evenings
 *
 * Displays a list of parents' evenings with dates and time,
 * along with links to edit, delete, and create them.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */

require_once('../../config.php');
require_once($CFG->libdir.'/tablelib.php');

require_login($SITE);
$id = required_param('id', PARAM_INT);
$context = get_context_instance(CONTEXT_BLOCK, $id);
$PAGE->set_url('/blocks/parenteve/manage.php', array('id' => $id));
require_capability('block/parentseve:manage', $context);

$parentseves = $DB->get_records('parentseve', null, 'timestart DESC');

$navlinks = array();
$navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
$navigation = build_navigation($navlinks);

$table = new flexible_table('parentseves');
$table->define_columns(array('date', 'timestart', 'timeend', 'teachers', 'edit', 'delete'));
$table->column_class('edit', 'function');
$table->column_class('delete', 'function');
$table->define_headers(array(get_string('date', 'block_parentseve'),
                            get_string('timestart', 'block_parentseve'),
                            get_string('timeend', 'block_parentseve'),
                            '', '', ''));
$table->define_baseurl($PAGE->url);
$table->initialbars(true);
$table->set_attribute('id', 'parentseves');
$table->set_attribute('class', 'generaltable generalbox');
$table->setup();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageparentseve', 'block_parentseve'), 2);
$url = new moodle_url('/blocks/parentseve/edit.php', array('id' => $id));
echo $OUTPUT->container(html_writer::link($url, get_string('createnew', 'block_parentseve')));

foreach ($parentseves as $parentseve) {
    $row = array();
    $params = array('id' => $id, 'parentseve' => $parentseve->id);
    $url = new moodle_url('/blocks/parentseve/schedule.php', $params);
    $row[] = html_writer::link($url, date('d/m/Y', $parentseve->timestart));
    $row[] = date('H:i', $parentseve->timestart);
    $row[] = date('H:i', $parentseve->timeend);
    $url = new moodle_url('/blocks/parentseve/teachers.php', $params);
    $row[] = html_writer::link($url, get_string('manageteachers', 'block_parentseve'));
    $url = new moodle_url('/blocks/parentseve/edit.php', $params);
    $row[] = html_writer::link($url, get_string('edit'));
    $url = new moodle_url('/blocks/parentseve/delete.php', $params);
    $row[] = html_writer::link($url, get_string('delete'));
    $table->add_data($row);
}
$table->finish_output();
echo $OUTPUT->footer();
