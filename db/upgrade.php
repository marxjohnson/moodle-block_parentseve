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
 * Defines upgrade function for parents' evening block
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth <mike@mike-worth.com>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

function xmldb_block_parentseve_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2010030502) { //New version in version.php

        // Define table parentseve_teacher to be created
        $table = new XMLDBTable('parentseve_teacher');

        // Adding fields to table parentseve_teacher
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('parentseveid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

        // Adding keys to table parentseve_teacher
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Launch create table for parentseve_teacher
        $result = $result && create_table($table);

        // Move the data from comma-separated lists to the new table
        $parentseves = get_records('parentseve');
        foreach ($parentseves as $parentseve) {
            $teachers = explode(',', $parentseve->teachers);
            foreach ($teachers as $teacher) {
                $teacherrecord = new stdClass;
                $teacherrecord->parentseveid = $parentseve->id;
                $teacherrecord->userid = $teacher;
                $result = $result && insert_record('parentseve_teacher', $teacherrecord);
            }
        }

        // Define field teachers to be dropped from parentseve
        $table = new XMLDBTable('parentseve');
        $field = new XMLDBField('teachers');

        // Launch drop field teachers
        $result = $result && drop_field($table, $field);
    }

    return $result;
}
