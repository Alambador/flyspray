<?php

  /*********************************************************\
  | Deal with lost passwords                                |
  | ~~~~~~~~~~~~~~~~~~~~~~~~                                |
  \*********************************************************/

if (!defined('IN_FS')) {
    die('Do not access this file directly.');
}

class FlysprayDoLostpw extends FlysprayDo
{
    function is_accessible()
    {
        global $user;
        return $user->isAnon();
    }

    function action_chpass()
    {
        global $db;

        // Check that the user submitted both the fields, and they are the same
        if (!Post::val('pass1') || strlen(trim(Post::val('magic_url'))) !== 32) {
            return array(ERROR_RECOVER, L('erroronform'));
        }

        if (Post::val('pass1') != Post::val('pass2')) {
            return array(ERROR_RECOVER, L('passnomatch'));
        }

        $new_pass_hash = Flyspray::cryptPassword(Post::val('pass1'));
        $db->Execute("UPDATE  {users} SET user_pass = ?, magic_url = ''
                       WHERE  magic_url = ?",
                      array($new_pass_hash, Post::val('magic_url')));

        return array(SUBMIT_OK, L('passchanged'), './');
    }

    function action_sendmagic()
    {
        global $db;

        // Check that the username exists
        $user = Flyspray::getUserDetails(Flyspray::username_to_id(Post::val('user_name')));

        // If the username doesn't exist, throw an error
        if (!is_array($user) || !count($user)) {
            return array(ERROR_RECOVER, L('usernotexist'));
        }

        //no microtime(), time,even with microseconds is predictable ;-)
        $magic_url    = md5(uniqid(rand(), true));

        // Insert the random "magic url" into the user's profile
        $db->Execute('UPDATE {users}
                         SET magic_url = ?
                       WHERE user_id = ?',
                     array($magic_url, $user['user_id']));

        Notifications::send($user_details['user_id'], ADDRESS_USER, NOTIFY_PW_CHANGE, array($baseurl, $magic_url));

        return array(SUBMIT_OK, L('magicurlsent'));
    }

    function _onsubmit()
    {
        return $this->handle('action', Post::val('action'));
    }

    function show()
    {
        global $page, $fs;

        $page->setTitle($fs->prefs['page_title'] . L('lostpw'));

        if (!Get::has('magic_url')) {
            // Step One: user requests magic url
            $page->pushTpl('lostpw.step1.tpl');
        } else {
            // Step Two: user enters new password
            $check_magic = $db->GetOne('SELECT user_id FROM {users} WHERE magic_url = ?',
                                        array(Get::val('magic_url')));

            if ($check_magic) {
                $page->pushTpl('lostpw.step2.tpl');
            } else {
                $page->pushTpl('lostpw.step1.tpl');
            }
        }
    }
}


?>
