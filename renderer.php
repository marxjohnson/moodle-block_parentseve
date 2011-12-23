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
 * Defines the renderer for the Parents' Eve block
 *
 * Defines {@see block_parentseve_renderer}
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */

/**
 * Renderer for Parents' Evening block
 */
class block_parentseve_renderer extends plugin_renderer_base {

    /**
     * Displays user selectors for adding teachers to the current parents' evening
     *
     * @param $potential parentseve_teacher_selector
     * @param $selected parentseve_teacher_selector
     * @return string HTML form containing the selectors
     */
    function teacher_selector ($potential, $selected) {

        $output = '';
        $table = new html_table('teacher_selector');
        $row = new html_table_row();
        $row->cells[] = $selected->display(true);
        $cell = html_writer::empty_tag('input', array('class' => 'add_button', 'name' => 'add', 'type' => 'submit', 'value' => $this->output->larrow().' '.get_string('add')));
        $cell .= html_writer::empty_tag('input', array('class' => 'remove_button', 'name' => 'remove', 'type' => 'submit', 'value' => get_string('remove').' '.$this->output->rarrow()));
        $row->cells[] = $cell;
        $row->cells[] = $potential->display(true);
        $table->data[] = $row;

        $output = html_writer::start_tag('form', array('action' => $this->page->url->out(false), 'method' => 'post'));
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * A table of booked appointments, optionally with links to cancel
     *
     * @param $id int instance ID, for cancel links
     * @param $parentseve object The parents's evening record
     * @param $appointments array The appointments to be displayed
     * @param $cancel bool Display cancel links?
     * @return string HTML table containing the booked appointments
     */
    function schedule_table($id, $parentseve, $appointments = array(), $cancel = false) {

        $output = '';

        $table = new html_table();
        $table->head = array(get_string('apptime','block_parentseve'),
                        get_string('parentname','block_parentseve'),
                        get_string('studentname','block_parentseve'));
        if ($cancel) {
            $table->head[] = '';
        }

        $appcron = array();
        if (!empty($appointments)) {
            foreach($appointments as $appointment){
                $appcron[$appointment->apptime]['parentname'] = $appointment->parentname;
                $appcron[$appointment->apptime]['studentname'] = $appointment->studentname;
                $appcron[$appointment->apptime]['id'] = $appointment->id;
            }
        }

        for($time = $parentseve->timestart; $time < $parentseve->timeend; $time += $parentseve->appointmentlength) {

            $row = array();
            $row[] = date('G:i',$time);
            $row[] = '';
            $row[] = '';
            $row[] = '';

            if(!empty($appcron[$time])) {
                $row[1] = $appcron[$time]['parentname'];
                $row[2] = $appcron[$time]['studentname'];
                if ($cancel) {
                    $cancelurl = new moodle_url('/blocks/parentseve/cancel.php', array('id' => $id, 'appointment' => $appcron[$time]['id']));
                    $row[3] = html_writer::link($cancelurl, get_string('cancel'));
                }
            }

            $table->data[] = $row;
        }

        $output .= html_writer::table($table);

        return $output;
    }

    /**
     * A link to the booking form
     *
     * @param $id int The instance ID
     * @param $parentseve object The Parents' Evening record
     * @return string HTML link
     */
    function booking_link($id, $parentseve) {
        $url = new moodle_url('/blocks/parentseve/book.php', array('id' => $id, 'parentseve' => $parentseve->id));
        return html_writer::link($url, get_string('bookapps','block_parentseve'));
    }

    /**
     * A link to display all schedules
     *
     * @param $id int The instance ID
     * @param $parentseve object The Parents' Evening record
     * @return string HTML link
     */
    function allschedules_link($id, $parentseve) {
        $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $id, 'parentseve' => $parentseve->id));
        return html_writer::link($url, get_string('allschedules','block_parentseve'));
    }

    /**
     * A link to display just the current user's schedule
     *
     * @param $id int The instance ID
     * @param $parentseve object The Parents' Evening record
     * @return string HTML link
     */
    function myschedule_link($id, $parentseve) {
        $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $id, 'parentseve' => $parentseve->id, 'my' => 1));
        return html_writer::link($url, get_string('justmyschedule','block_parentseve'));
    }

    /**
     * Additional information about the parents' evening
     *
     * @param $starttime int The start time for the Parents' Evening
     * @param $info string The information to be displayed
     * @return string HTML paragraph containing the info
     */
    function booking_info($starttime, $info) {
        $formatteddate = (object)array('date' => date('l jS F Y', $starttime));
        $output = get_string('parentseveon', 'block_parentseve', $formatteddate);
        $output .= html_writer::tag('p', $info, array('class' => 'info'));
        return $output;
    }

    /**
     * The skeleton booking form, to be filled out by AJAX when the newapp button is clicked
     *
     * @param $url moodle_url Action URL for the form
     * @return string HTML form containing skeleton for the booking form
     */
    function booking_form($url) {
        $output = '';
        $output .= html_writer::start_tag('form', array('method' => 'post', 'action' => $url->out(false), 'id' => 'parentseve_form'));

        $names = html_writer::label(get_string('parentname','block_parentseve'), 'parentname');
        $names .= html_writer::empty_tag('input', array('type' => 'text', 'name' => 'parentname'));
        $names .= html_writer::label(get_string('studentname','block_parentseve'), 'studentname');
        $names .= html_writer::empty_tag('input', array('type' => 'text', 'name' => 'studentname'));

        $output .= $this->output->container($names, 'names');
        $buttons = html_writer::tag('button', get_string('newapp','block_parentseve'), array('type' => 'button', 'id' => 'newapp_button'));
        $buttons .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('confirmapps','block_parentseve')));
        $output .= $this->output->container($buttons, 'parentseve_buttons');
        $output .= $this->output->container('<!--AJAX will put the schedules in here-->', '', 'parentseve_appointments');
        $output .= $this->output->container('', '', 'clearfix');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Notification of success/failure creating bookings
     *
     * @param $successes array Appointments successfully booked
     * @param $failures array Appointments that couldn't be booked
     * @param $url moodle_url URL to link to
     * @return string HTML paragraphs containing notifications
     */
    function booking_response($successes, $failures, $url) {
        $output = '';
        $items = array();
        foreach ($successes as $success) {
            $args = (object)array(
                'teacher' => $success->teacher,
                'apptime' => date('G:i',$success->apptime)
            );
            $items[] = get_string('appbooked','block_parentseve', $args);
        }
        foreach ($failures as $failure) {
            $args = (object)array(
                'teacher' => $failure->teacher,
                'apptime' => date('G:i',$failure->apptime)
            );
            $items[] = get_string('appnotbooked','block_parentseve', $args);
        }
        $output .= html_writer::alist($items);
        $output .= html_writer::tag('p', get_string('success', 'block_parentseve', count($successes)));

        if (count($failures)) {
            $output .= html_writer::tag('p', get_string('fail', 'block_parentseve', count($failures)));
        }

        $output .= $this->output->heading(get_string('printsave','block_parentseve'), 4);
        $output .= html_writer::link($url, get_string('backtoappointments', 'block_parentseve'));

        return $output;

    }

    /**
     * Warning to IE<8 users that the booking form wont work
     *
     * @param $altmethod string Alternative method of booking appointments
     * @return string HTML div containing the warning and alternative method
     */
    function ie_warning($altmethod) {
        $strwarning = get_string('iewarning', 'block_parentseve');
        $stralt = get_string('iealternatively', 'block_parentseve').$altmethod;

        return $this->output->box($strwarning.' '.$stralt, 'generalbox iewarning');
    }
}
