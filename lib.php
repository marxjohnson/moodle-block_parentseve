<?php
/**
 * Defines functions for use in the block.
 *
 * Defines {@see parenteseve_print_schedule()}, {@see parentseve_get_teachers()},
 * {@see parentseve_isteacher()} and {@see parentseve_search_filter()}.
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */

/**
* Prints a schedule for the teacher specified
*
* Prints out a {@see flexible_table} containing a list of all possible appointments for a teacher,
* with student and parents names for those appointments that have been booked.
*
* @param object $teacher the user object for the teacher
* @param object $parentseve The record for the parents' evening
* @return boolean was a schedule printed sucessfully? Will return false if teacher has no appointments booked
*/
function parentseve_get_schedule($teacher,$parentseve,$id) {
    global $DB;

    $sql = 'SELECT *
            FROM {parentseve_app}
            WHERE teacherid = ?
                AND parentseveid = ?
            ORDER BY apptime';
    return $DB->get_records_sql($sql, array($teacher->id, $parentseve->id));
}


/**
 * Get list of teachers for a particular parent's evening
 *
 * If the parents' evening exists and has some teachers defined, returns the user IDs of all the teachers
 * for the parents' evening.
 *
 * @param object $parentseve The record for the requires parents' evening
 * @return array array of user objects containing only ids, firstnames and lastnames
 */

function parentseve_get_teachers($parentseve) {
    global $DB;

    $select = 'SELECT u.* ';
    $from = 'FROM {parentseve_teacher} AS t
            JOIN {user} AS u ON t.userid = u.id ';
    $where = 'WHERE t.parentseveid = ? ';
    $order = 'ORDER BY firstname, lastname ASC';
    $params = array($parentseve->id);
    if($teachers = $DB->get_records_sql($select.$from.$where.$order, $params)) {
        return $teachers;
    } else {
        return array();
    }

}

/**
 * is the supplied user on the list of teachers for a particular parents evening?
 *
 * @param int $userid the id of the user to check
 * @param object $parentseve the record for the specified parents' evening
 * @return bool is the user a teacher on the list?
 */

function parentseve_isteacher($userid, $parentseve) {
    global $DB;
    return $DB->get_record('parentseve_teacher', array('parentseveid' => $parentseve->id, 'userid' => $userid));
}

/**
 * Does a given string exist in a user's name
 *
 * Used in {@see edit.php} to filter search results for the teacher selection form.
 * Checks to see if the search text occurs within the user's full name (case insensitively).
 *
 * @param object $user The user object containing at least a firstname and lastname attribute
 * @global $searchtext
 * @return bool True if the search text occurs in the user's name, otherwise false.
 *
 */
function parentseve_search_filter($user) {
    global $searchtext;
    return stristr(fullname($user), $searchtext);
}

?>
