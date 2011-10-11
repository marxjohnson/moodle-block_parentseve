<?php
/**
 * Defines the class for the block
 *
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */

require_once($CFG->dirroot.'/blocks/parentseve/lib.php');

/**
 * The class definition for the block
 */
class block_parentseve extends block_list {

    /**
     * Initalise the block - set the title and version
     */
    function init() {
        $this->title = get_string('parentseve', 'block_parentseve');
    }

    /**
     * Display the block
     *
     * Displays a block containing a list of links to the current parents' evenings
     * If the user has parentseve:manage, also displays a link to the admin interface.
     */
    function get_content() {
        global $DB, $OUTPUT, $USER;

        if ($this->content !== null) {
          return $this->content;
        }

        $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = ' ';

        if (has_capability('block/parentseve:manage', $context)) {
            $params = array('id' => $this->instance->id);
            $url = new moodle_url('/blocks/parentseve/manage.php', $params);
            $strmanage = get_string('manageparentseve','block_parentseve');
            $this->content->items[] = html_writer::link($url, $strmanage);
            $this->content->icons[] = '';
        }

        if (has_capability('block/parentseve:book', $context)) {
            $parentseves = $DB->get_records_select('parentseve', 'timeend > ?', array(time()));
            foreach($parentseves as $parentseve) {
                $params = array('id' => $this->instance->id, 'parentseve' => $parentseve->id);
                $url = new moodle_url('/blocks/parentseve/book.php', $params);
                $startdate = date('D jS M Y', $parentseve->timestart);
                $this->content->items[] = html_writer::link($url, $startdate);
                $this->content->icons[] = $OUTPUT->pix_icon('i/item', 'item');
                if (parentseve_isteacher($USER->id, $parentseve) || has_capability('block/parentseve:viewall', $context)) {
                    $params = array('id' => $this->instance->id, 'parentseve' => $parentseve->id);
                    $url = new moodle_url('/blocks/parentseve/schedule.php', $params);
                    $strviewapps = get_string('viewapps', 'block_parentseve');
                    $this->content->items[] = '&nbsp;&nbsp;&nbsp;&ndash; '.html_writer::link($url, $strviewapps);
                    $this->content->icons[] = '';
                }

            }
        }

        return $this->content;

    }

    function applicable_formats() {
        return array('site-index' => true);
    }

}

?>
