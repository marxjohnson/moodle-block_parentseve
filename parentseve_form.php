<?php
require_once ($CFG->libdir.'/formslib.php');
class parentseve_form extends moodleform {
        function definition() {
        $mform    =& $this->_form;
        $mform->addElement('hidden','id');
        $mform->addElement('hidden','cmid');
        $mform->addElement('hidden','reviewid');
        $mform->addElement('date_time_selector', 'timestart', get_string('timestart', 'termreview'));
        $mform->addElement('date_time_selector', 'timeend', get_string('timeend', 'termreview'));
        $mform->addElement('text','appointmentlength',get_string('appointmentlength', 'termreview')); // will have to take this in minutes until figure out duration element type (moodle 2.0)
        $mform->addElement('htmleditor', 'info', get_string('parentseveinfo', 'termreview'),'rows="10" cols="25"');

        $mform->addElement('hidden', 'teachers', null, array('id' => 'id_teachers'));

    $this->add_action_buttons(false);
    }
}

function parentseve_teacher_form($selectedusers, $unselectedusers) {
    global $CFG, $THEME, $searchtext;

    echo '<table summary="" style="margin-left:auto;margin-right:auto" border="0" cellpadding="5" cellspacing="0">
            <tr>
                <td valign="top">
                <label for="removeselect">'.get_string('parentseveteachers', 'termreview').'</label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple" >';


    $i = 0;
    foreach ($selectedusers as $selecteduser) {
        $fullname = fullname($selecteduser);
        echo '<option value="'.$selecteduser->id.'">'.$fullname."</option>\n";
        $i++;
    }

    if ($i == 0) {
        echo '<option/>'; // empty select breaks xhtml strict
    }

      echo '</select></td>
      <td valign="top">
        <br />';
        check_theme_arrows();
        echo '<p class="arrow_button">
            <input name="add" id="add" type="button" value="'. $THEME->larrow.'&nbsp;'.get_string('add').'" title="'.get_string('add').'" onclick="termreview_parentseve_addteachers()" /><br />
            <input name="remove" id="remove" type="button" value="'.get_string('remove').'&nbsp;'.$THEME->rarrow.'" title="'.get_string('remove').'" onclick="termreview_parentseve_removeteachers()"/>
        </p>
      </td>
      <td valign="top">
          <select name="addselect[]" size="20" id="addselect" multiple="multiple" >';

        $i = 0;
        foreach ($unselectedusers as $unselecteduser) {
                $fullname = fullname($unselecteduser);
                echo '<option value="'.$unselecteduser->id.'">'.$fullname."</option>\n";
                $i++;
        }
        if ($i==0) {
            echo '<option />'; // empty select breaks xhtml strict
        }

        echo '</select>
         <br />
         <label for="searchtext" class="accesshide">'.get_string('search').'</label>
         <input type="text" name="searchtext" id="searchtext" size="30" value="'.$searchtext.'" />
         <input name="search" id="search" type="button" value="'.get_string('search').'" onclick="termreview_parentseve_teachersearch(YAHOO.util.Dom.get(\'searchtext\').value);" />';

          if (!empty($searchtext)) {
              echo '<input name="showall" id="showall" type="submit" value="'.get_string('showall').' />'."\n";
          }

       echo '</td>
    </tr>
  </table>';
}

?>