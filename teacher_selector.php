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
 * Defines the user selectors for selecting users as teachers for a parents' evening
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 **/

require_once($CFG->dirroot.'/user/selector/lib.php');

/**
 * User Selector for selecting users to be teachers
 *
 * @uses user_selector_base
 */
class parentseve_teacher_selector extends user_selector_base {
    protected $parentseve;

    /**
     * Constructor, sets name, options and stores Parents' Evening record
     *
     * @param $name string Unique name for the selector
     * @param $parentseve object The database record for the Parents' Evening
     * @param $options array Additional options for the selector
     */
    public function __construct($name, $parentseve, $options = array()) {
        parent::__construct($name, $options);
        $this->parentseve = $parentseve;
    }

    /**
     * Defines the file option for AJAX calls and reutrns options
     *
     * @return array All options defined for the selector, plus the file option
     */
    protected function get_options() {
        $options = parent::get_options();
        $options['file'] = 'blocks/parentseve/teacher_selector.php';
        return $options;
    }

    /**
     * Gets IDs for everyone who's currently a teacher for the given parents' evening
     *
     * @global $DB
     * @return array|bool of user IDs, or false of there aren't any teachers yet
     */
    protected function get_current_teacher_ids() {
        global $DB;
        $teachers = $DB->get_records('parentseve_teacher',
                                     array('parentseveid' => $this->parentseve->id),
                                     '',
                                     'userid, id, parentseveid');
        if ($teachers) {
            return array_keys($teachers);
        }
        return false;
    }

    /**
     * Builds where clause for selecting users who are not currently selected as teachers
     *
     * @param $current_teacherids array The IDs of users who are already selected as teachers
     * @return array Where clause and parameters
     */
    protected function where_sql($current_teacherids) {
        global $DB;
        if ($current_teacherids) {
            list($in_sql, $params) = $DB->get_in_or_equal($current_teacherids,
                                                          SQL_PARAMS_QM,
                                                          'param',
                                                          false);
            $where = 'id '.$in_sql;
        } else {
            $where = '';
            $params = array();
        }

        return array($where, $params);
    }

    /**
     * Finds the users to be displayed in the list
     *
     * Gets all non-deleted users found by {@see where_sql} filtered by the search terms
     *
     * @param $search The search term entered in the form
     * @return array Multi-dimentional array of headings and users
     */
    public function find_users($search) {
        global $DB;
        $where = '';
        $params = array();
        $current_teacherids = $this->get_current_teacher_ids();
        list($where, $params) = $this->where_sql($current_teacherids);

        if ($search) {
            if (!empty($where)) {
                $where .= ' AND ';
            }
            $where .= '('.$DB->sql_like($DB->sql_concat('firstname', '" "', 'lastname'), '?');
            $where .= ' OR '.$DB->sql_like('email', '?').')';
            $params[] = '%'.$search.'%';
            $params[] = '%'.$search.'%';
        }
        if (!empty($where)) {
            $where .= ' AND ';
        }
        $where .= 'deleted = ?';
        $params[] = 0;
        return array(get_string('users') => $DB->get_records_select('user', $where, $params));
    }
}

/**
 * User selector for selecting (and removing) existing teachers from at Parents' Evening
 *
 * @uses parentseve_teacher_selector
 */
class parentseve_selected_teacher_selector extends parentseve_teacher_selector {

    /**
     * Builds where clause to select just existing teachers
     *
     * @param $current_teachersids array IDs of users who are already teachers
     * @return array Where clause and parameters
     */
    protected function where_sql($current_teacherids) {
        global $DB;
        if ($current_teacherids) {
            list($in_sql, $params) = $DB->get_in_or_equal($current_teacherids);
            $where = 'id '.$in_sql;
        } else {
            $where = 'id IS NULL';
            $params = array();
        }

        return array($where, $params);
    }
}
