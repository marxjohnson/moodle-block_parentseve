<?php
require_once '../../config.php';
require $CFG->libdir.'/tablelib.php';

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/parentseve:manage', $systemcontext);

$parentseves = get_records('parentseve', '', '', 'timestart DESC');
require_js(array('yui_yahoo',
                    'yui_event',
                    'yui_element',
                    'yui_datasource',
                    'yui_datatable',
                    $CFG->wwwroot.'/blocks/parentseve/js/lib.js.php'));

$navlinks = array();
$navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'type' => 'activity');
$navlinks[] = array('name' => get_string('manageparentseve', 'block_parentseve'), 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple(get_string('manageparentseve', 'block_parentseve'), '', $navigation, "", "", true, '');

echo '<h2>'.get_string('manageparentseve', 'block_parentseve').'</h2>
        <p><a href="'.$CFG->wwwroot.'/blocks/parentseve/edit.php">'.get_string('createnew', 'block_parentseve').'</a></p>';
    
$table = new flexible_table('parentseves');
$table->define_columns(array('date', 'timestart', 'timeend', 'edit', 'delete'));
$table->column_class('edit', 'function');
$table->column_class('delete', 'function');
$table->define_headers(array(get_string('date', 'block_parentseve'), 
                            get_string('timestart', 'block_parentseve'), 
                            get_string('timeend', 'block_parentseve'), 
                            '', 
                            ''));
$table->sortable(true, 'date', SORT_DESC);
$table->set_attribute('id', 'parentseves');
$table->set_attribute('class', 'generaltable generalbox');
$table->setup();
foreach ($parentseves as $parentseve) {
    $row = array();
    $row[] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/schedule.php?id='.$parentseve->id.'">'.date('d/m/Y', $parentseve->timestart).'</a>';
    $row[] = date('H:i', $parentseve->timestart);
    $row[] = date('H:i', $parentseve->timeend);
    $row[] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/edit.php?id='.$parentseve->id.'">'.get_string('edit').'</a>';
    $row[] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/delete.php?id='.$parentseve->id.'">'.get_string('delete').'</a>';
    $table->add_data($row);     
}
$table->print_html($CFG->wwwroot.'/blocks/parentseve/manage.php');

print_footer();

?>
