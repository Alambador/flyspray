<?php
/*
    This script gets the history of a task and
    returns it for HTML display in a page.
*/

define('IN_FS', true);

header('Content-type: text/html; charset=utf-8');

require_once('../../header.php');
require_once('../../includes/events.inc.php');
$baseurl = dirname(dirname($baseurl)) .'/' ;

// Check permissions
if (!$user->perms('view_history')) {
    die();
}

if ($details = Get::num('details')) {
    $details = " AND h.history_id = $details";
} else {
    $details = null;
}

$histories = get_events(Get::num('task_id'), $details);

$page = new FSTpl;
$page->uses('histories', 'details');
if ($details) {
    event_description($histories[0]); // modifies global variables
    $page->assign('details_previous', $GLOBALS['details_previous']);
    $page->assign('details_new', $GLOBALS['details_new']);
}
$page->display('details.tabs.history.callback.tpl');

?>
