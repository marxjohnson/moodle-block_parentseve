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
 * Defines capabilities for Parents' Evening block
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth <mike@mike-worth.com>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

$capabilities = array(

    'block/parentseve:manage' => array(
        // Manage edit, create and delete parents evenings
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    ),

    'block/parentseve:book' => array(
        // Create a booking (if not set to allow anon bookings)
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_ALLOW
        )
    ),

    'block/parentseve:cancel' => array(
        // Cancel bookings
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),

    'block/parentseve:viewall' => array(
        // View all bookings
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW
        )
    )
);
