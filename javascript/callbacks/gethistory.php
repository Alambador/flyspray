<?php
/*
    This script gets the history of a task and
    returns it for HTML display in a page.
*/

define('IN_FS', true);

require_once('../../header.php');
require_once('../../includes/events.inc.php');

// Initialise user
if (Cookie::has('flyspray_userid') && Cookie::has('flyspray_passhash')) {
    $user = new User(Cookie::val('flyspray_userid'));
    $user->get_perms($proj);
    $user->check_account_ok();
}

// Check permissions
if (!$user->perms['view_history'])
{
    die();
}



if (is_numeric($details = Get::val('details'))) {
    $details = " AND h.history_id = $details";
} else {
    $details = null;
}

$sql = get_events(Get::val('id'), $details);
$histories = $db->fetchAllArray($sql);

if ($details && isset($GLOBALS['details_previous']) && isset($GLOBALS['details_new']))
{
    $html = '<table class="history">';
    $html .= '<tr>';
    $html .= '<th>' . L('previousvalue') . '</th>';
    $html .= '<th>' . L('newvalue') . '</th>';
    $html .= '</tr><tr>';
    $html .= '<td>' . $GLOBALS['details_previous'] . '</td>';
    $html .= '<td>' . $GLOBALS['details_new'] . '</td>';
    $html .= '</tr></table>';
    
    echo $html;
    die();
}


$html = '<table class="history"><tr>';
$html .= '<th>' . L('eventdate') . '</th>';
$html .= '<th>' . L('user') . '</th>';
$html .= '<th>' . L('event') . '</th>';
$html .= '</tr>';
foreach($histories as $history)
{
    $html .= '<tr>';
    $html .= '<td>' . formatDate($history['event_date'], false) . '</td>';
    $html .= '<td>' . tpl_userlink($history['user_id']) . '</td>';
    $html .= '<td>' . event_description($history) . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

echo $html;
