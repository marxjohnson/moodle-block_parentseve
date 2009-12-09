<?php

require_once($CFG->dirroot.'/blocks/parentseve/lib.php');

class block_parentseve extends block_list {

    function init() {
        $this->title = get_string('parentseve', 'block_parentseve');
        $this->version = 2009120900;
    }

    function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
        global $CFG, $USER;
        $context = get_context_instance(CONTEXT_SYSTEM);
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = ' ';

        if (has_capability('block/parentseve:manage', $context)) {            
        	$this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/manage.php">'.get_string('manageparentseve','block_parentseve').'</a>';
            $this->content->icons[] = '';
        }
        
        if (has_capability('block/parentseve:book', $context)) {
            $parentseves = get_records_select('parentseve', 'timestart > '.time());
            foreach($parentseves as $parentseve) {
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/book.php?id='.$parentseve->id.'">'.date('D jS M Y', $parentseve->timestart).'</a>';
                $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/item.gif" />';
                if (parentseve_isteacher($USER->id, $parentseve) || has_capability('block/parentseve:viewall', $context)) {
                	$this->content->items[] = '&nbsp;&nbsp;&nbsp;&ndash; <a href="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id.'">'.get_string('viewapps', 'block_parentseve').'</a>';
                    $this->content->icons[] = '';
                }
                
            }
        }

        return $this->content;

    }

    function has_config() {
        return true;
    }

}

?>