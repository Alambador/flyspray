<?php
// This script checks for pending scheduled notifications
// and sends them at the right time.
$path = dirname(dirname(__FILE__));
require_once("$path/header.php");
require_once("$path/includes/notify.inc.php");

$fs->get_language_pack('functions');

$notify = new Notifications;
$now = date(U);

$get_reminders = $db->Query("SELECT  * FROM {reminders} r
                          LEFT JOIN  {tasks} t ON r.task_id = t.task_id
                          LEFT JOIN  {projects} p ON t.attached_to_project = p.project_id
                              WHERE  t.is_closed = '0'
                           ORDER BY  r.reminder_id");

while ($row = $db->FetchRow($get_reminders)) {
    // Check to see if it's time to send a reminder
    if (($row['start_time'] < $now) && (($row['last_sent'] + $row['how_often']) < $now)) {
        // Send the reminder

        $jabber_users = array();
        $email_users  = array();

        // Get the user's notification type and address
        $get_details  = $db->Query("SELECT  notify_type, jabber_id, email_address
                                      FROM  {users}
                                     WHERE  user_id = ?", array($row['to_user_id']));

        while ($subrow = $db->FetchArray($get_details)) {
            if (($fs->prefs['user_notify'] == '1' && $subrow['notify_type'] == '1')
                    OR ($fs->prefs['user_notify'] == '2'))
            {
                $email_users[] = $subrow['email_address'];

            }
            elseif (($fs->prefs['user_notify'] == '1' && $subrow['notify_type'] == '2')
                    OR ($fs->prefs['user_notify'] == '3'))
            {
                $jabber_users[] = $subrow['jabber_id'];
            }
        }

        $subject = $functions_text['notifyfrom'];
        $message = stripslashes($row['reminder_message']);

        // Pass the recipients and message onto the notification function
        $notify->SendEmail($email_users, $subject, $message);
        $notify->StoreJabber($jabber_users, $subject, $message);

        // Update the database with the time sent
        $update_db = $db->Query("UPDATE  {reminders}
                                    SET  last_sent = ?
                                  WHERE  reminder_id = ?",
                              array($now, $row['reminder_id']));
    }
}

// send those stored notifications
$notify->SendJabber();

?>
<html>
<head>
<title>Scheduled Reminders</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<h1>Scheduled Reminders</h1>
This is a backend script that really isn't meant to be displayed in your browser.
To enable scheduled reminders, you set up some sort of background program to
activate this script regularly.  The unix utility 'cron' can be used in conjunction
with 'wget' to do this.
</body>
</html>
