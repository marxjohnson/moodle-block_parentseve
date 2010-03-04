<?php
/**
 * Defines functions for use in the block.
 * 
 * Defines {@see parenteseve_print_schedule()}, {@see parentseve_get_teachers()},
 * {@see parentseve_isteacher()} and {@see parentseve_search_filter()}.
 * 
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>, Mike Worth
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 */
 
/**
* Prints a schedule for the teacher specified
* 
* Prints out a {@see flexible_table} containing a list of all possible appointments for a teacher,
* with student and parents names for those appointments that have been booked.
*
* @param object $teacher the user object for the teacher
* @param object $parentseve The record for the parents' evening
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
 * Get list of teachers for a particular parent's evening
 * 
 * If the parents' evening exists and has some teachers defined, returns the user IDs of all the teachers
 * for the parents' evening.
 *
 * @param object $parentseve The record for the requires parents' evening
 * @return array array of user objects containing only ids, firstnames and lastnames
 */

function parentseve_get_teachers($parentseve) {
    if(!empty($parentseve->teachers)) {
        return get_records_select('user', 'id IN('.$parentseve->teachers.')', 'lastname, firstname', 'id, lastname, firstname');
    } else {
    	return array();
    }
    
}

/**
 * is the supplied user on the list of teachers for a particular parents evening?
 *
 * @param int $userid the id of the user to check
 * @param object $parentseve the record for the specified parents' evening 
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

/**
 * Does a given string exist in a user's name
 * 
 * Used in {@see edit.php} to filter search results for the teacher selection form.
 * Checks to see if the search text occurs within the user's full name (case insensitively).
 * 
 * @param object $user The user object containing at least a firstname and lastname attribute
 * @global $searchtext
 * @return bool True if the search text occurs in the user's name, otherwise false.
 * 
 */
function parentseve_search_filter($user) {
    global $searchtext;
    return stristr(fullname($user), $searchtext);
}

?>
