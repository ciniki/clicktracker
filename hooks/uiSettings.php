<?php
//
// Description
// -----------
// This function will return a list of user interface settings for the module.
//
// Arguments
// ---------
// ciniki:
// business_id:     The ID of the business to get clicktracker for.
//
// Returns
// -------
//
function ciniki_clicktracker_hooks_uiSettings($ciniki, $business_id, $args) {

    //
    // Setup the default response
    //
    $rsp = array('stat'=>'ok', 'menu_items'=>array());

    //
    // Check permissions for what menu items should be available
    //
    if( isset($ciniki['business']['modules']['ciniki.clicktracker']) && ($ciniki['session']['user']['perms']&0x01) == 0x01 ) {
        $menu_item = array(
            'priority'=>3000,
            'label'=>'Click Tracking', 
            'edit'=>array('app'=>'ciniki.clicktracker.main'),
            );
        $rsp['menu_items'][] = $menu_item;
    } 

    return $rsp;
}
?>
