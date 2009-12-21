<?php
/**
 * The server side code for creating a new bookings list
 * 
 * Takes the parent's evening and teacher ID, and creates a list of appointment times for the teacher
 * 
 * @package block_parentseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int id The ID of the parent's evening
 * @param int teacher The ID of the teacher
 */
    require_once('../../config.php');
    require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
    $id = required_param('id', PARAM_INT); // Parents evening ID
    $teacherid = required_param('teacher', PARAM_INT);

    if (!$parentseve = get_record('parentseve', 'id', $id)) {
        error('Parents evening ID was incorrect');
    }
    if ($parentseve->appointmentlength == 0) {
        print_error('appointmentlengthzero','termreview');
    }
    //In order to avoid a loop of DB calls, fetch all the relevant appointments then put them into an array which php can manipulate a lot quicker
    $appcron = array();
    if ($appointments=get_records_sql('SELECT apptime FROM '.$CFG->prefix.'parentseve_app WHERE teacherid='.$teacherid.' AND parentseveid='.$parentseve->id)){
        foreach ($appointments as $appointment) {
            $appcron[$appointment->apptime]=true;
        }
    }
    $list='';
    for ($time = $parentseve->timestart; $time < $parentseve->timeend; $time += $parentseve->appointmentlength) {
        $list .= date('G:i',$time);
        if (!empty($appcron[$time])) {
            $list .= ',true';
        } else {
            $list .= ',false';
        }
        $list .= ','.$time."\n";
    }
    //remove last \n to prevent blank row in table
    echo substr($list, 0, -1);
?>