<?php  //$Id$

// This file keeps track of upgrades to
// the online_users block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_parentseve_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2010030502) { //New version in version.php
    
    /// Define table parentseve_teacher to be created
        $table = new XMLDBTable('parentseve_teacher');

    /// Adding fields to table parentseve_teacher
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('parentseveid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table parentseve_teacher
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for parentseve_teacher
        $result = $result && create_table($table);

    /// Move the data from comma-separated lists to the new table
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

    /// Define field teachers to be dropped from parentseve
        $table = new XMLDBTable('parentseve');
        $field = new XMLDBField('teachers');

    /// Launch drop field teachers
        $result = $result && drop_field($table, $field);
    }

    return $result;
}

?>