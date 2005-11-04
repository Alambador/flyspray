<?php foreach($proj->listGroups() as $group): ?>
<a class="grouptitle" href="{$fs->CreateURL($gr_link, $group['group_id'])}">{$group['group_name']}</a>
<p>{$group['group_desc']}</p>
<form action="{$baseurl}" method="post">
  <div>
    <input type="hidden" name="do" value="modify" />
    <input type="hidden" name="action" value="movetogroup" />
    <input type="hidden" name="old_group" value="{$group['group_id']}" />
    <input type="hidden" name="project_id" value="{$proj->id}" />
    <input type="hidden" name="prev_page" value="{$_SERVER['REQUEST_URI']}" />
  </div>

  <table class="userlist">
    <tr>
      <th></th>
      <th>{$admin_text['username']}</th>
      <th>{$admin_text['realname']}</th>
      <th>{$admin_text['accountenabled']}</th>
    </tr>
    <?php foreach($proj->listUsersIn($group['group_id']) as $user): ?>
    <tr>
      <td>{!tpl_checkbox('users['.$user['user_id'].']')}</td>
      <td><a href="{$fs->CreateURL('user', $user['user_id'])}">{$user['user_name']}</a></td>
      <td>{$user['real_name']}</td>
      <?php if ($user->infos['account_enabled']): ?>
      <td>{$admin_text['yes']}</td>
      <?php else: ?>
      <td>{$admin_text['no']}</td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>

    <tr>
      <td colspan="4">
        <input class="adminbutton" type="submit" value="{$admin_text['moveuserstogroup']}" />
        <select class="adminlist" name="switch_to_group">
          <?php if ($proj->id): ?>
          <option value="0">{$admin_text['nogroup']}</option>
          <?php endif; ?>
          {!tpl_options($proj->listGroups())}
        </select>
      </td>
    </tr>
  </table>
</form>
<?php endforeach; ?>

<?php if ($proj->id): ?>
<form action="{$baseurl}" method="post">
  <div>
    <input type="hidden" name="do" value="modify" />
    <input type="hidden" name="action" value="addtogroup" />
    <input type="hidden" name="project_id" value="{$proj->id}" />
    <input type="hidden" name="prev_page" value="{$_SERVER['REQUEST_URI']}" />
    <select class="adminlist" name="user_list[]" multiple="multiple" size="15">
      <?php foreach($proj->listUsersIn() as $user): ?>
      <option value="{$user['user_id']}">{$user['user_name']} ({$user['real_name']})</option>
      <?php endforeach; ?>
    </select>
    <br />
    <input class="adminbutton" type="submit" value="{$admin_text['addtogroup']}" />
    <select class="adminbutton" name="add_to_group">
      {!tpl_options($proj->listGroups())}
    </select>
  </div>
</form>
<?php endif; ?>