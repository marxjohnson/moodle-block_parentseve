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
        $this->version = 2010030502;
    }

    /**
     * Display the block
     * 
     * Displays a block containing a list of links to the current parents' evenings
     * If the user has parentseve:manage, also displays a link to the admin interface.
     */
    function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
        global $CFG, $USER;
        
        $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
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
 
    function applicable_formats() {
        return array('site-index' => true);
    }

}

?>