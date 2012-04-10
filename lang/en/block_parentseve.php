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
 * Defines lang strings for Parents' Evening block
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth <mike@mike-worth.com>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

$string['allowanon'] = 'Allow anonymous users';
$string['allschedules'] = 'Show All Appointments';
$string['allowanon_explain'] = 'If this setting is checked, anyone who knows the URL can book an appointment (so parents don\'t have to have an account). Otherwise, only users with parentseve:book can access the page.';
$string['altmethod'] = 'Alternate Booking Method';
$string['altmethodlong'] = 'An alternative booking method (such as a number to call) if the form
    isn\'t working for some reason.  If the form fails to load what you enter here will be
    displayed along with a generic error message';
$string['appbooked'] = 'Appointment with: {$a->teacher} At: {$a->apptime} booked';
$string['appnotbooked'] = 'There was an error, appointment with: {$a->teacher} At: {$a->apptime} not booked';
$string['apptime'] = 'Appointment time';
$string['appointmentcancel'] = 'You have requested to cancel an appointment with {$a->teacher} at {$a->time} on {$a->date}.';
$string['appointmentlength'] = 'Appointment length /min';
$string['appointmentlengthzero'] = 'The parent\'s evening selected has no Appointment Length';
$string['backtoappointments'] = 'Back to Appointments';
$string['blockname'] = 'Parents\' Evening';
$string['bookapps'] = 'Book Parents Evening Appointments';
$string['book'] = 'Book Appointments';
$string['busy'] = 'Busy';
$string['createnew'] = 'Create New Parents\' Evening';
$string['config'] = 'Configure';
$string['confirmapps'] = 'Confirm Appointments (without this your appointments will not be booked)';
$string['confirmcancel'] = 'Are you sure you want to cancel this appointment? This cannot be undone!';
$string['confirmdelete'] = 'Are you sure you want to delete this Parents\' Evening? Clicking "Yes" will also delete the appointments for that Parents\' Evening, and this cannot be undone!';
$string['date'] = 'Date';
$string['emptyschedule'] = 'There are currently no appointments booked';
$string['manageteachers'] = 'Manage Teachers';
$string['fail'] = '{$a} appointments have failed to be made';
$string['formfailed'] = 'Unfortunately, the booking form is experiencing problems at the moment.';
$string['importteachers'] = 'Import teachers from';
$string['iealternatively'] = 'Alternatively, ';
$string['iewarning'] = 'You appear to be using an old version of Internet Explorer, or be running a newer version in "Compatibility View".
    This page may not work correctly with old versions of Internet Explorer. Please upgrade to the latest version, or switch to a modern
    browser such as <a href="http://getfirefox.com">Firefox</a>.';
$string['justmyschedule'] = 'Just show my appointments';
$string['manageparentseve'] = 'Manage Parents\' Evenings';
$string['mustcorrect'] = 'You must correct this before you can book appointments';
$string['newapp'] = 'Add a New Appointment';
$string['newparentseve'] = 'New Parents\' Evening';
$string['noappointment'] = 'The specified appointment does not exist. It may already have been cancelled.';
$string['noappointments'] = 'You have not booked any appointments, press \'Add a New Appointment\' then select a teacher and time';
$string['noappointmentwith'] = 'There is no time entered for the appointment with ';
$string['noparentseve'] = 'This Parents\' Evening does not exist';
$string['noparentseves'] = 'No Parents\' Evenings have been created';
$string['noparentname'] = 'You have not entered a parent\'s name';
$string['nostudentname'] = 'You have not entered a student\'s name';
$string['oldparentseve'] = 'This parents evening has already taken place.';
$string['parentname'] = 'Parent\'s name';
$string['parentseve'] = 'Parents\' Evening';
$string['parentseve:manage'] = 'Manage Parents\' Evenings';
$string['parentseve:book'] = 'Book Parents\' Evening Appointments';
$string['parentseve:cancel'] = 'Cancel Parents\' Evening Appointments';
$string['parentseveconfig'] = 'Parents Evening Config';
$string['parentsevedelete'] = 'You have requested to delete the Parents\' Evening starting at {$a->time} on {$a->date}';
$string['parentseveinfo'] = 'Additional information';
$string['parentseveon'] = 'Parents Evening on {$a->date}';
$string['parentseveschedule'] = 'Parents Evening Appointments';
$string['parentseveteachers'] = 'Parents Evening Teachers';
$string['parentsevesdisabled'] = 'Parents evening features are currently disabled';
$string['parentsevenotfound'] = 'No Parents\' evening was found for the given ID';
$string['parentname'] = 'Parent\'s name';
$string['pluginname'] = 'Parents\' Evening Appointments';
$string['printsave'] = 'Please print/save this page for your records; your identity has not been verified, therefore you will not be able to get this information in the future.';
$string['selectteacher'] = 'Select a Teacher...';
$string['schedulefor'] = 'Parents evening schedule for {$a}';
$string['studentname'] = 'Student\'s name';
$string['success'] = '{$a} appontments have been successfully made';
$string['teacher'] = 'Teacher';
$string['timeend'] = 'End time';
$string['timestart'] = 'Start time';
$string['viewapps'] = 'View Appointments';
