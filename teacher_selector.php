<?php
require_once($CFG->dirroot.'/user/selector/lib.php');

class parentseve_teacher_selector extends user_selector_base {
    protected $parentseve;

    public function __construct($name, $parentseve, $options = array()) {
        parent::__construct($name, $options);
        $this->parentseve = $parentseve;
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['file'] = 'blocks/parentseve/teacher_selector.php';
        return $options;
    }

    protected function get_current_teacher_ids() {
        global $DB;
        $teachers = $DB->get_records('parentseve_teacher', array('parentseveid' => $this->parentseve->id), '', 'userid, id, parentseveid');
        if ($teachers) {
            return array_keys($teachers);
        }
        return false;
    }

    protected function where_sql($current_teacherids) {
        global $DB;
        if ($current_teacherids) {
            list($in_sql, $params) = $DB->get_in_or_equal($current_teacherids, SQL_PARAMS_QM, 'param', false);
            $where = 'id '.$in_sql;
        } else {
            $where = '';
            $params = array();
        }

        return array($where, $params);
    }

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
class parentseve_selected_teacher_selector extends parentseve_teacher_selector {

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
