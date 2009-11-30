<?php

require_once($CFG->wwwroot.'/blocks/parentseve/lib.php');

class block_parentseve extends block_base {

    function init() {
        $this->title = get_string('parentseve', 'block_parentseve');
        $this->version = 2009113000;
    }

    function get_content() {

        global $CFG;
        $this->content->footer = '';
        if(empty($this->config->selected)) {
            $this->config->selected = ',';
        }

        return $this->content;

    }

    function has_config() {
        return true;
    }

    function config_save($data) {

    }

    function is_empty() {

        if (empty($this->instance->pinned)) {
            $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        } else {
            $context = get_context_instance(CONTEXT_SYSTEM); // pinned blocks do not have own context
        }

        $this->get_content();
        return(empty($this->content->text) && empty($this->content->footer));
    }

}

?>