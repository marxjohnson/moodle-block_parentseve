<?php
require_once '../../config.php';

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
        <p><a href="'.$CFG->wwwroot.'/blocks/parentseve/edit.php">'.get_string('createnew', 'block_parentseve').'</a></p>
        <div id="parentseves_table" class="yui-skin-sam">
        <table id="parentseves">
            <thead>
                <tr>
                    <td>'.get_string('date', 'block_parentseve').'</td>
                    <td>'.get_string('timestart', 'block_parentseve').'</td>
                    <td>'.get_string('timeend', 'block_parentseve').'</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>';
if($parentseves) {
    foreach ($parentseves as $parentseve) {
    echo '<tr>
            <td><a href="'.$CFG->wwwroot.'/blocks/parentseve/edit.php?id='.$parentseve->id.'">'.date('d/m/Y', $parentseve->timestart).'</a></td>
            <td>'.date('H:i', $parentseve->timestart).'</td>
            <td>'.date('H:i', $parentseve->timeend).'</td>
            <td><a href="'.$CFG->wwwroot.'/blocks/parentseve/delete.php?id='.$parentseve->id.'">'.get_string('delete').'</a></td>
        </tr>';
    }     
} else {
    echo '<tr><td colspan="4">'.get_string('noparentseves', 'block_parentseve').'</td></tr>';	
}       
                
echo '      </tbody>
        </table>
    </div>
<script type="text/javascript">YAHOO.util.Event.addListener(window, "load", parentseve_table());</script>';

print_footer();

?>
