<div id="toolbox">
  <h3>{L('admintoolboxlong')} :: {L('preferences')}</h3>

  <form action="{CreateURL(array('admin', 'prefs'))}" method="post">
  <ul id="submenu">
   <li><a href="#general">{L('general')}</a></li>
   <li><a href="#userregistration">{L('userregistration')}</a></li>
   <li><a href="#notifications">{L('notifications')}</a></li>
   <li><a href="#lookandfeel">{L('lookandfeel')}</a></li>
  </ul>

   <div id="general" class="tab">
      <table class="box">
        <tr>
          <td><label for="pagetitle">{L('pagetitle')}</label></td>
          <td>
            <input id="pagetitle" name="page_title" type="text" class="text" size="40" maxlength="100" value="{$fs->prefs['page_title']}" />
          </td>
        </tr>
        <tr>
          <td><label for="defaultproject">{L('defaultproject')}</label></td>
          <td>
            <select id="defaultproject" name="default_project">
              {!tpl_options(array_merge(array(0 => L('allprojects')), Flyspray::listProjects()), $fs->prefs['default_project'])}
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="langcode">{L('language')}</label></td>
          <td>
            <select id="langcode" name="lang_code">
              {!tpl_options(Flyspray::listLangs(), $fs->prefs['lang_code'], true)}
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="dateformat">{L('dateformat')}</label></td>
          <td>
            <input id="dateformat" name="dateformat" type="text" class="text" size="40" maxlength="30" value="{$fs->prefs['dateformat']}" />
          </td>
        </tr>
        <tr>
          <td><label for="dateformat_extended">{L('dateformat_extended')}</label></td>
          <td>
            <input id="dateformat_extended" name="dateformat_extended" class="text" type="text" size="40" maxlength="30" value="{$fs->prefs['dateformat_extended']}" />
          </td>
        </tr>
        <tr>
          <td><label for="cache_feeds">{L('cache_feeds')}</label></td>
          <td>
            <select id="cache_feeds" name="cache_feeds">
            {!tpl_options(array('0' => L('no_cache'), '1' => L('cache_disk'), '2' => L('cache_db')), $fs->prefs['cache_feeds'])}
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="anon_userlist">{L('allowanonuserlist')}</label></td>
          <td>{!tpl_checkbox('anon_userlist', $fs->prefs['anon_userlist'], 'anon_userlist')}</td>
        </tr>
        <tr>
          <td><label for="resolution_list">{L('resolutionlist')}</label></td>
          <td>
            <select id="resolution_list" name="resolution_list">
            {!tpl_options($lists, $fs->prefs['resolution_list'])}
            </select>
          </td>
        </tr>
        <?php if ($fs->prefs['resolution_list']): ?>
        <tr>
          <td><label for="resolution_dupe">{L('duplicateres')}</label></td>
          <td>
            <select id="resolution_dupe" name="resolution_dupe">
            {!tpl_options($proj->get_list(array('list_id' => $fs->prefs['resolution_list'])), $fs->prefs['resolution_dupe'])}
            </select>
          </td>
        </tr>
        <?php endif; ?>
      </table>
    </div>

    <div id="userregistration" class="tab">
      <table class="box">
        <tr>
          <td><label for="allowusersignups">{L('anonreg')}</label></td>
          <td>{!tpl_checkbox('anon_reg', $fs->prefs['anon_reg'], 'allowusersignups')}</td>
        </tr>
        <tr>
          <td><label for="spamproof">{L('spamproof')}</label></td>
          <td>{!tpl_checkbox('spam_proof', $fs->prefs['spam_proof'], 'spamproof')}</td>
        </tr>
        <tr>
          <td><label for="notify_registration">{L('notify_registration')}</label></td>
          <td>{!tpl_checkbox('notify_registration', $fs->prefs['notify_registration'], 'notify_registration')}</td>
        </tr>
        <tr>
          <td><label for="defaultglobalgroup">{L('defaultglobalgroup')}</label></td>
          <td>
            <select id="defaultglobalgroup" name="anon_group">
              {!tpl_options(Flyspray::listGroups(), $fs->prefs['anon_group'])}
            </select>
          </td>
        </tr>
      <?php if (function_exists('ldap_connect')): ?>
        <tr>
        <th colspan="2"><hr />
            {L('ldap')}
          </th>
        </tr>
        <tr>
          <td><label for="ldap_enabled">{L('ldapenabled')}</label></td>
          <td>{!tpl_checkbox('ldap_enabled', $fs->prefs['ldap_enabled'], 'ldap_enabled')}</td>
        </tr>
        <tr>
          <td><label for="ldap_server">{L('ldapserver')}</label></td>
          <td><input id="ldap_server" name="ldap_server" class="text" type="text" size="40" value="{$fs->prefs['ldap_server']}" /></td>
        </tr>
        <tr>
          <td><label for="ldap_base_dn">{L('ldapbasedn')}</label></td>
          <td><input id="ldap_base_dn" name="ldap_base_dn" class="text" type="text" size="40" value="{$fs->prefs['ldap_base_dn']}" /></td>
        </tr>
        <tr>
          <td><label for="ldap_userkey">{L('ldapuserkey')}</label></td>
          <td><input id="ldap_userkey" name="ldap_userkey" class="text" type="text" size="40" value="{$fs->prefs['ldap_userkey']}" /></td>
        </tr>
        <tr>
          <td><label for="ldap_user">{L('ldapuser')}</label></td>
          <td><input id="ldap_user" name="ldap_user" class="text" type="text" size="40" value="{$fs->prefs['ldap_user']}" /></td>
        </tr>
        <tr>
          <td><label for="ldap_password">{L('ldappassword')}</label></td>
          <td><input id="ldap_password" name="ldap_password" class="text" type="text" size="40" value="{$fs->prefs['ldap_password']}" /></td>
        </tr>
      </table>
      <?php else: ?>
      </table>
      <p><em>{L('ldapnotsupported')}</em></p>
      <?php endif; ?>
    </div>

    <div id="notifications" class="tab">
      <table class="box">
        <tr>
          <td><label for="usernotify">{L('forcenotify')}</label></td>
          <td>
            <select id="usernotify" name="user_notify">
              {!tpl_options(array(L('neversend'), L('userchoose'), L('email'), L('jabber')), $fs->prefs['user_notify'])}
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="send_background">{L('sendbackground')}</label></td>
          <td>
              {!tpl_checkbox('send_background', $fs->prefs['send_background'], 'send_background', 1)}
          </td>
        </tr>
        <tr>
          <th colspan="2"><hr />
            {L('emailnotify')}
          </th>
        </tr>
        <tr>
          <td><label for="adminemail">{L('fromaddress')}</label></td>
          <td>
            <input id="adminemail" name="admin_email" class="text" type="text" size="40" maxlength="100" value="{$fs->prefs['admin_email']}" />
          </td>
        </tr>
        <tr>
          <td><label for="smtpserv">{L('smtpserver')}</label></td>
          <td>
            <input id="smtpserv" name="smtp_server" class="text" type="text" size="40" maxlength="100" value="{$fs->prefs['smtp_server']}" />
            <?php if (extension_loaded('openssl')) : ?>
            {!tpl_checkbox('email_ssl', $fs->prefs['email_ssl'], 'email_ssl')} <label class="inline" for="email_ssl">{L('ssl')}</label>
            {!tpl_checkbox('email_tls', $fs->prefs['email_tls'], 'email_tls')} <label class="inline" for="email_tls">{L('tls')}</label>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td><label for="smtpuser">{L('smtpuser')}</label></td>
          <td>
            <input id="smtpuser" name="smtp_user" class="text" type="text" size="40" maxlength="100" value="{$fs->prefs['smtp_user']}" />
          </td>
        </tr>
        <tr>
          <td><label for="smtppass">{L('smtppass')}</label></td>
          <td>
            <input id="smtppass" name="smtp_pass" class="text" type="text" size="40" maxlength="100" value="{$fs->prefs['smtp_pass']}" />
          </td>
        </tr>
        <?php if (extension_loaded('xml')) : ?>
        <tr>
          <th colspan="2"><hr />
            {L('jabbernotify')}
          </th>
        </tr>
        <tr>
          <td><label for="jabberserver">{L('jabberserver')}</label></td>
          <td>
            <input id="jabberserver" class="text" type="text" name="jabber_server" size="25" maxlength="100" value="{$fs->prefs['jabber_server']}" />
		<?php if (extension_loaded('openssl')) : ?>
            {!tpl_checkbox('jabber_ssl', $fs->prefs['jabber_ssl'], 'jabber_ssl')} <label class="inline" for="jabber_ssl">{L('ssl')}</label>
        <?php endif; ?>
		</td>
        </tr>
        <tr>
          <td><label for="jabberport">{L('jabberport')}</label></td>
          <td>
            <input id="jabberport" class="text" type="text" name="jabber_port" size="40" maxlength="100" value="{$fs->prefs['jabber_port']}" />
          </td>
        </tr>
        <tr>
          <td><label for="jabberusername">{L('jabberuser')}</label></td>
          <td>
            <input id="jabberusername" class="text" type="text" name="jabber_username" size="40" maxlength="100" value="{$fs->prefs['jabber_username']}" />
          </td>
        </tr>
        <tr>
          <td><label for="jabberpassword">{L('jabberpass')}</label></td>
          <td>
            <input id="jabberpassword" name="jabber_password" class="password" type="password" size="40" maxlength="100" value="{$fs->prefs['jabber_password']}" />
          </td>
        </tr>
        <?php else: ?>
        <tr><td><em>{L('jabbernotsupported')}</em></td></tr>
        <?php endif; ?>
      </table>
    </div>

    <div id="lookandfeel" class="tab">
      <table class="box">
        <tr>
          <td><label for="globaltheme">{L('globaltheme')}</label></td>
          <td>
            <select id="globaltheme" name="global_theme">
              {!tpl_options(Flyspray::listThemes(), $fs->prefs['global_theme'], true)}
            </select>
          </td>
        </tr>
        <tr>
          <td><label id="viscollabel">{L('visiblecolumns')}</label></td>
          <td class="text">
            <?php // Set the selectable column names
            $selectedcolumns = explode(' ', $fs->prefs['visible_columns']);
            ?>
            {!tpl_double_select('visible_columns', $proj->columns, $selectedcolumns)}
          </td>
        </tr>
      </table>
    </div>

    <div class="tbuttons">
      <input type="hidden" name="action" value="globaloptions" />
      <button type="submit">{L('saveoptions')}</button>

      <button type="reset">{L('resetoptions')}</button>
    </div>

  </form>

</div>
