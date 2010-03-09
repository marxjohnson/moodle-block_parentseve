<?php
require_once('../../config.php');
require_once ($CFG->libdir.'/formslib.php');
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/parentseve:manage', $context);

$id = optional_param('id', 0, PARAM_INT);
$searchtext = optional_param('searchtext', '', PARAM_TEXT);

if (!$parentseve = get_record('parentseve', 'id', $id)) {
    print_error('noparentseve', 'block_parentseve');
}
$navlinks = array();
$navlinks[] = array('name' => get_string('parentseve', 'block_parentseve'), 'link' => $CFG->wwwroot.'/blocks/parentseve/manage.php', 'type' => 'activity');

$navlinks[] = array('name' => date('l jS M Y', $parentseve->timestart), 'link' => '', 'type' => 'activityinstance');

$navlinks[] = array('name' => get_string('config', 'block_parentseve'), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

$where = 'CONCAT(firstname, " ", lastname) LIKE "%'.$searchtext.'%"';
$order = 'firstname ASC';
$users = get_records_select('user', $where, $order);

$select = 'SELECT u.* ';
$from = 'FROM '.$CFG->prefix.'parentseve_teacher AS t
            JOIN '.$CFG->prefix.'user AS u ON t.userid = u.id ';
$where = 'WHERE t.parentseveid = '.$parentseve->id.' ';
$order = 'ORDER BY '.$order;

if(!$teachers = get_records_sql($select.$from.$where.$order)) {
    $teachers = array();
}

$users = array_diff_key($users, $teachers);

print_header_simple(get_string('parentseveschedule','block_parentseve'), '', $navigation, "", "", true, '');

echo '<form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/teachers.php';
        if (!empty($id)) {
            echo '?id='.$id;
        }
    echo '">
        <fieldset><legend>'.get_string('parentseveteachers', 'block_parentseve').'</legend></fieldset>
        <table style="margin-left:auto;margin-right:auto" border="0" cellpadding="5" cellspacing="0">
            <tr>
                <td valign="top">
                <label for="removeselect">'.get_string('parentseveteachers', 'block_parentseve').'</label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple" >';


    $i = 0;
    foreach ($teachers as $teacher) {
        $fullname = fullname($teacher);
        echo '<option value="'.$teacher->id.'">'.$fullname."</option>\n";
        $i++;
    }

    if ($i == 0) {
        echo '<option/>'; // empty select breaks xhtml strict
    }

      echo '</select></td>
      <td valign="top">
        <br />';
        check_theme_arrows();
        echo '<p class="arrow_button">';
            echo'<input name="add" id="add" type="submit" value="'. $THEME->larrow.'&nbsp;'.get_string('add').'" title="'.get_string('add').'" /><br />
                <input name="remove" id="remove" type="submit" value="'.get_string('remove').'&nbsp;'.$THEME->rarrow.'" title="'.get_string('remove').'" />';
        echo '</p>
      </td>
      <td valign="top">
          <select name="addselect[]" size="20" id="addselect" multiple="multiple" >';

        $i = 0;

        foreach ($users as $user) {
                $fullname = fullname($user);
                echo '<option value="'.$user->id.'">'.$fullname."</option>\n";
                $i++;
        }
        if ($i==0) {
            echo '<option />'; // empty select breaks xhtml strict
        }

        echo '</select>
         <br />
         <label for="searchtext" class="accesshide">'.get_string('search').'</label>
         <input type="text" name="searchtext" id="searchtext" size="30" value="'.$searchtext.'" />
         <input name="search" id="search" type="submit" value="'.get_string('search').'" title="'.get_string('search').'" />
         <input name="teachers" type="hidden" value="'.implode(',', array_keys($selectedusers)).'" />';
          if (!empty($searchtext)) {
              echo '<input name="showall" id="showall" type="submit" value="'.get_string('showall').'" />'."\n";
          }

       echo '</td>
    </tr>
  </table>
  </form>';

print_footer();
?>
