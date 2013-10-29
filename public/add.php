<?php
//
// Description
// ===========
// This method will add a click to the tracking log, to later be examined.
//
// Arguments
// ---------
// 
// Returns
// -------
// <rsp stat='ok'/>
//
function ciniki_clicktracker_add($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'panel_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Panel'), 
        'item'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Item'), 
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
    $rc = ciniki_clicktracker_checkAccess($ciniki, $args['business_id'], 'ciniki.clicktracker.add'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Add the click to the database
	//
	$strsql = "INSERT INTO ciniki_clicktracker (business_id, user_id, "
		. "panel_id, item_clicked, "
		. "date_added) VALUES ("
		. "'" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $ciniki['session']['user']['id']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['panel_id']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['item']) . "', "
		. "UTC_TIMESTAMP())"
		. "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbInsert');
	$rc = ciniki_core_dbInsert($ciniki, $strsql, 'ciniki.clicktracker');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}

	return array('stat'=>'ok');
}
?>
