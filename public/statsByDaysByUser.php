<?php
//
// Description
// ===========
// This method will return stats for the specified previous X days from today.
//
// Arguments
// ---------
// 
// Returns
// -------
// <rsp stat='ok'/>
//
function ciniki_clicktracker_statsByDaysByUser($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'days'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Days'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'clicktracker', 'private', 'checkAccess');
    $rc = ciniki_clicktracker_checkAccess($ciniki, $args['business_id'], 'ciniki.clicktracker.statsByDaysByUser'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Get the list of users
    //
    $strsql = "SELECT DISTINCT ciniki_clicktracker.user_id, ciniki_users.display_name "
        . "FROM ciniki_clicktracker "
        . "LEFT JOIN ciniki_users ON (ciniki_clicktracker.user_id = ciniki_users.id) "
        . "WHERE ciniki_clicktracker.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND ciniki_clicktracker.date_added >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL '" . ciniki_core_dbQuote($ciniki, $args['days']) . "' DAY) "
        . "ORDER BY display_name "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.clicktracker', 
        array(
            array('container'=>'users', 'fname'=>'user_id', 'name'=>'user',
                'fields'=>array('user_id', 'display_name')),
            ));
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }
    $users = $rc['users'];
    
    //
    // Get the stats from the database
    //
    $strsql = "SELECT CONCAT_WS('-', panel_id, item_clicked) AS cid, panel_id, item_clicked AS item, "
        . "ciniki_users.display_name AS name, COUNT(*) AS clicked "
        . "FROM ciniki_clicktracker "
        . "LEFT JOIN ciniki_users ON (ciniki_clicktracker.user_id = ciniki_users.id) "
        . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND ciniki_clicktracker.date_added >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL '" . ciniki_core_dbQuote($ciniki, $args['days']) . "' DAY) "
        . "GROUP BY panel_id, item, name "
        . "ORDER BY panel_id, item, name "
        . "";
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.clicktracker', 
        array(
            array('container'=>'stats', 'fname'=>'cid', 'name'=>'stat',
                'fields'=>array('panel_id', 'item', 'clicked'), 'sums'=>array('clicked')),
            array('container'=>'users', 'fname'=>'name', 'name'=>'user',
                'fields'=>array('name', 'clicked')),
            ));
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    return array('stat'=>'ok', 'stats'=>$rc['stats'], 'users'=>$users);
}
?>
