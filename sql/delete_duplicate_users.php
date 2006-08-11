<?php
   /**********************************************************\
   | This script removes duplicate user names                  |
   \**********************************************************/
   
require_once '../includes/class.flyspray.php';
require_once '../includes/constants.inc.php';
require_once BASEDIR . '/includes/class.database.php';

$db = new Database;
$db->dbOpenFast($conf['database']);

$users = $db->Query('SELECT * FROM {users} ORDER BY user_id ASC');

while ($row = $db->FetchRow($users))
{
    if (!isset($deleted[$row['user_name']])) {
        $deleted[$row['user_name']] = $row['user_id'];
    }
    
    $db->Query('DELETE FROM {users} WHERE user_name = ? AND user_id != ?',
                array($row['user_name'], $deleted[$row['user_name']]));
}


$users = $db->Query('SELECT * FROM {registrations} ORDER BY reg_id ASC');

while ($row = $db->FetchRow($users))
{
    if (!isset($deleted[$row['user_name']])) {
        $deleted[$row['user_name']] = $row['reg_id'];
    }
    
    $db->Query('DELETE FROM {registrations} WHERE user_name = ? AND reg_id != ?',
                array($row['user_name'], $deleted[$row['user_name']]));
}


?>