<?php
$block_parentseve_capabilities = array(

    'block/parentseve:manage' => array(
        // Manage edit, create and delete parents evenings
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'block/parentseve:book' => array(
        // Create a booking (if not set to allow anon bookings)
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_ALLOW
        )
    ),

    'block/parentseve:cancel' => array(
        // Cancel bookings
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),    

    'block/parentseve:viewall' => array(
        // View all bookings
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW
        )
    )

);

?>
