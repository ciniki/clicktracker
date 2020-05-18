//
function ciniki_clicktracker_main() {
    //
    // Panels
    //
    this.init = function() {
        this.menu = new M.panel('Click Tracker',
            'ciniki_clicktracker_main', 'menu',
            'mc', 'narrow', 'sectioned', 'ciniki.clicktracker.main.menu');
        this.menu.data = null;
        this.menu.sections = {
            'reports':{'label':'', 'type':'simplelist', 'list':{
//              'last7':{'label':'Last 7 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(\'M.ciniki_clicktracker_main.menu.show();\', 7);'},
                'bydays30':{'label':'Last 30 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(\'M.ciniki_clicktracker_main.menu.show();\', 30);'},
                'bydaysbyusers30':{'label':'Users last 30 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(\'M.ciniki_clicktracker_main.menu.show();\', 30);'},
//              'last90':{'label':'Last 90 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(\'M.ciniki_clicktracker_main.menu.show();\', 90);'},
//              'last365':{'label':'Last 365 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(\'M.ciniki_clicktracker_main.menu.show();\', 365);'},
                }},
            'trending':{'label':'Trends', 'type':'simplelist', 'list':{
                'weekly':{'label':'Weekly', 'fn':''},
                'monthly':{'label':'Monthly', 'fn':''},
                }},
            };
        this.menu.addClose('Back');

        this.bydays = new M.panel('Report',
            'ciniki_clicktracker_main', 'bydays',
            'mc', 'medium', 'sectioned', 'ciniki.clicktracker.main.bydays');
        this.bydays.sections = {
            'tabs':{'label':'', 'type':'paneltabs', 'selected':'30', 'tabs':{
                '1':{'label':'1 Day', 'fn':'M.ciniki_clicktracker_main.showByDays(null, \'1\');'},
                '7':{'label':'7 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(null, \'7\');'},
                '30':{'label':'30 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(null,\'30\');'},
                '90':{'label':'90 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(null,\'90\');'},
                '180':{'label':'180 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(null,\'180\');'},
                '365':{'label':'365 Days', 'fn':'M.ciniki_clicktracker_main.showByDays(null,\'365\');'},
                }},
            '_':{'label':'', 'num_cols':3, 'type':'simplegrid', 'sortable':'yes',
                'headerValues':['Panel', 'Item', 'Clicked'],
                'cellClasses':['', '', ''],
                'sortTypes':['text', 'text', 'number'],
                'noData':'No clicks reported',
                },
            };
        this.bydays.sectionData = function(s, i, d) { return this.data; }
        this.bydays.rowFn = function(s, i, d) {
            return 'M.ciniki_clicktracker_main.showClickedItem(\'M.ciniki_clicktracker_main.bydays.show();\', \'' + d.stat.panel_id + '\',\'' + d.stat.item + '\');'; 
        };
        this.bydays.cellValue = function(s, i, j, d) {
            if( j == 0 ) { return d.stat.panel_id; }
            if( j == 1 ) { return d.stat.item; }
            if( j == 2 ) { return d.stat.clicked; }
        };
        this.bydays.addClose('Back');

        //
        // This panel displays the number of clicks per user for last X number of days
        this.bydaysbyusers = new M.panel('Report',
            'ciniki_clicktracker_main', 'bydaysbyusers',
            'mc', 'large', 'sectioned', 'ciniki.clicktracker.main.bydaysbyusers');
        this.bydaysbyusers.sections = {
            'tabs':{'label':'', 'type':'paneltabs', 'selected':'30', 'tabs':{
                '1':{'label':'1 Day', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null, \'1\');'},
                '7':{'label':'7 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null, \'7\');'},
                '30':{'label':'30 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null,\'30\');'},
                '90':{'label':'90 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null,\'90\');'},
                '180':{'label':'180 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null,\'180\');'},
                '365':{'label':'365 Days', 'fn':'M.ciniki_clicktracker_main.showByDaysByUsers(null,\'365\');'},
                }},
            '_':{'label':'', 'num_cols':3, 'type':'simplegrid', 'sortable':'yes',
                'headerValues':['Panel', 'Item', 'Clicked'],
                'cellClasses':['', '', ''],
                'sortTypes':['text', 'text', 'number'],
                'noData':'No clicks reported',
                },
            };
        this.bydaysbyusers.sectionData = function(s, i, d) { return this.data; }
        this.bydaysbyusers.rowFn = function(s, i, d) {
            return 'M.ciniki_clicktracker_main.showClickedItem(\'M.ciniki_clicktracker_main.bydays.show();\', \'' + d.stat.panel_id + '\',\'' + d.stat.item + '\');'; 
        };
        this.bydaysbyusers.cellValue = function(s, i, j, d) {
            if( j == 0 ) { return d.stat.panel_id; }
            if( j == 1 ) { return d.stat.item; }
            if( j >= 2 ) { 
                // Find the user in the list
                for(k in d.stat.users) {
                    if( d.stat.users[k].user.name == this.users[j-2].user.display_name ) {
                        return d.stat.users[k].user.clicked;
                    }
                }
                return 0;
            }
        };
        this.bydaysbyusers.addClose('Back');

        this.clickitem = new M.panel('Click Item',
            'ciniki_clicktracker_main', 'item',
            'mc', 'medium', 'sectioned', 'ciniki.clicktracker.main.item');
        this.clickitem.sections = {
            
            };
        this.clickitem.addClose('Back');
    }

    //
    // Arguments:
    // aG - The arguments to be parsed into args
    //
    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) {
            args = eval(aG);
        }

        //
        // Create the app container if it doesn't exist, and clear it out
        // if it does exist.
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_clicktracker_main', 'yes');
        if( appContainer == null ) {
            M.alert('App Error');
            return false;
        } 

        this.cb = cb;
        this.menu.show();
    }

    this.showByDays = function(cb, days) {
        this.bydays.sections.tabs.selected = days;
        this.bydays.data = {};
        var rsp = M.api.getJSON('ciniki.clicktracker.statsByDays', {'tnid':M.curTenantID, 'days':days});
        if( rsp['stat'] != 'ok' ) {
            M.api.err(rsp);
            return false;
        }
        this.bydays.data = rsp['stats'];
        this.bydays.refresh();
        if( cb != null ) {
            this.bydays.show(cb);
        } else {
            this.bydays.show();
        }
    }

    this.showByDaysByUsers = function(cb, days) {
        this.bydaysbyusers.sections.tabs.selected = days;
        this.bydaysbyusers.data = {};
        this.bydaysbyusers.users = {};
        var rsp = M.api.getJSON('ciniki.clicktracker.statsByDaysByUser', {'tnid':M.curTenantID, 'days':days});
        if( rsp['stat'] != 'ok' ) {
            M.api.err(rsp);
            return false;
        }
        this.bydaysbyusers.data = rsp.stats;
        this.bydaysbyusers.users = rsp.users;
        this.bydaysbyusers.sections._.headerValues = ['Panel', 'Item'];
        this.bydaysbyusers.sections._.sortTypes = ['text', 'text'];
        this.bydaysbyusers.sections._.cellClasses = ['text', 'text'];
        this.bydaysbyusers.sections._.num_cols = 2;
        for(i in rsp.users) {
            this.bydaysbyusers.sections._.headerValues.push(rsp.users[i].user.display_name);
            this.bydaysbyusers.sections._.sortTypes.push('number');
            this.bydaysbyusers.sections._.cellClasses.push('aligncenter');
            this.bydaysbyusers.sections._.num_cols += 1;
        }
        this.bydaysbyusers.refresh();
        if( cb != null ) {
            this.bydaysbyusers.show(cb);
        } else {
            this.bydaysbyusers.show();
        }
    }
}
