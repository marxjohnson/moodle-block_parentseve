<?php

/**
* prints a schedule for the teacher specified
*
* @param $teacher object the user object for the teacher
*
* @return boolean was a schedule printed sucessfully? Will return false if teacher has no appointments booked
*/

function parentseve_print_schedule($teacher,$parentseve) {
    global $CFG;

    $sql = 'SELECT *
            FROM '.$CFG->prefix.'parentseve_app
            WHERE teacherid='.$teacher->id.'
                AND parentseveid='.$parentseve->id.'
            ORDER BY apptime';
    if (!$appointments = get_records_sql($sql)) {
        return false;
    }

    echo '<h3>'.get_string('schedulefor','termreview',$teacher->firstname.' '.$teacher->lastname).'</h3>
        <table class="parentseve_schedule">
            <tr>
                <th class="parentseve_schedule">'.get_string('apptime','termreview').'</th>
                <th class="parentseve_schedule">'.get_string('parentname','termreview').'</th>
                <th class="parentseve_schedule">'.get_string('studentname','termreview').'</th>
            </tr>';
    $appcron = array();

    foreach($appointments as $appointment){
        $appcron[$appointment->apptime]['parentname']=$appointment->parentname;
        $appcron[$appointment->apptime]['studentname']=$appointment->studentname;
    }

    for($time = $parentseve->timestart; $time < $parentseve->timeend; $time += $parentseve->appointmentlength) {
        if(!empty($appcron[$time])) {
            echo '<tr>
                    <td class="parentseve_schedule">'.date('G:i',$time).'</td>
                    <td class="parentseve_schedule">'.$appcron[$time]['parentname'].'</td>
                    <td class="parentseve_schedule">'.$appcron[$time]['studentname'].'</td>
                </tr>';
        } else {
            echo '<tr>
                    <td class="parentseve_schedule">'.date('G:i',$time).'</td>
                    <td class="parentseve_schedule"></td>
                    <td class="parentseve_schedule"></td>
                </tr>';
        }
    }

    echo '</table>';

    return true;
}

/**
 * Function to get a list of teachers fo ra particular parents evening, creates a list based on the configured option.
 *
 * @param $peid int parents evening id
 * @return array array of user objects containing only ids, firstnames and lastnames
 */

function parentseve_get_teachers($peid) {
    global $CFG;

    $parentseve = get_record('parentseve', 'id', $peid);
    $sql = 'SELECT id, firstname, lastname
            FROM '.$CFG->prefix.'user
            WHERE id IN('.$parentseve->teachers.')
            ORDER BY lastname';
    return get_records_sql($sql);
}

/**
 * is the supplied user on the list of teachers for a particular parents evening?
 *
 * @param int $userid the id of the user to check
 * @param int $peid the id of the parents evening
 * @return bool is the user a teacher on the list?
 */

function parentseve_isteacher($userid,$peid) {
    $parentseve = get_record('parentseve','id',$peid);
    $teachers = explode(',',$parentseve->teachers);

    if (array_search($userid,$teachers) === false) {
        return false;
    } else {
        return true;
    }
}

?>
