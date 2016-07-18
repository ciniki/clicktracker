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
function ciniki_clicktracker_statsByDays($ciniki) {
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
    $rc = ciniki_clicktracker_checkAccess($ciniki, $args['business_id'], 'ciniki.clicktracker.statsByDays'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Get the stats from the database
    //
    $strsql = "SELECT CONCAT_WS('-', panel_id, item_clicked) AS cid, panel_id, item_clicked AS item, COUNT(*) AS clicked "
        . "FROM ciniki_clicktracker "
        . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND date_added >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL '" . ciniki_core_dbQuote($ciniki, $args['days']) . "' DAY) "
        . "GROUP BY panel_id, item "
        . "ORDER BY panel_id, item "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.clicktracker', 
        array(
            array('container'=>'stats', 'fname'=>'cid', 'name'=>'stat',
                'fields'=>array('panel_id', 'item', 'clicked'))
            ));
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    return array('stat'=>'ok', 'stats'=>$rc['stats']);
}
?>
