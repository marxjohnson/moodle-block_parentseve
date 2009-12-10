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
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $sql = 'SELECT *
            FROM '.$CFG->prefix.'parentseve_app
            WHERE teacherid='.$teacher->id.'
                AND parentseveid='.$parentseve->id.'
            ORDER BY apptime';
    if (!$appointments = get_records_sql($sql)) {
        return false;
    }

    echo '<h3 class="parentseve_schedule_header">'.get_string('schedulefor','block_parentseve',$teacher->firstname.' '.$teacher->lastname).'</h3>';

    $table = new flexible_table('parentseveschedule_'.$teacher->id);
    $columns = array('time', 'parent', 'student');
    if (has_capability('block/parentseve:cancel', $systemcontext)) {
        $columns[] = 'cancel';	
        $table->column_class('cancel', 'function');
    } 
    $table->define_columns($columns);
    $headers = array(get_string('apptime','block_parentseve'), 
                    get_string('parentname','block_parentseve'), 
                    get_string('studentname','block_parentseve'));
    if (has_capability('block/parentseve:cancel', $systemcontext)) {
        $headers[] = '';
    }                 
    $table->define_headers($headers); 
    $table->set_attribute('class', 'generaltable generalbox parentseve_schedule');    
    $table->setup();     
   
    $appcron = array();
    foreach($appointments as $appointment){
        $appcron[$appointment->apptime]['parentname'] = $appointment->parentname;
        $appcron[$appointment->apptime]['studentname'] = $appointment->studentname;
        $appcron[$appointment->apptime]['id'] = $appointment->id;
    }

    for($time = $parentseve->timestart; $time < $parentseve->timeend; $time += $parentseve->appointmentlength) {
        
        $row = array();
        $row[] = date('G:i',$time);        
        $row[] = '';
        $row[] = '';
        $row[] = '';
        
        if(!empty($appcron[$time])) {
            $row[1] = $appcron[$time]['parentname'];
            $row[2] = $appcron[$time]['studentname'];            
            if (has_capability('block/parentseve:cancel', $systemcontext)) {                
                $row[3] = '<a href="'.$CFG->wwwroot.'/blocks/parentseve/cancel.php?id='.$appcron[$time]['id'].'">'.get_string('cancel').'</a>';
            }  
        }        
        
        $table->add_data($row);
    }
    $table->print_html();

    return true;
}

/**
 * Function to get a list of teachers fo ra particular parents evening, creates a list based on the configured option.
 *
 * @param $peid int parents evening id
 * @return array array of user objects containing only ids, firstnames and lastnames
 */

function parentseve_get_teachers($parentseve) {
    global $CFG;

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

function parentseve_isteacher($userid, $parentseve) {
    $parentseve = get_record('parentseve', 'id', $parentseve->id);
    $teachers = explode(',',$parentseve->teachers);

    if (array_search($userid,$teachers) === false) {
        return false;
    } else {
        return true;
    }
}

?>
