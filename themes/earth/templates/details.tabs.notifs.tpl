<div id="notify" class="tab">
  <p><em>{L('theseusersnotify')}</em></p>
  <?php foreach ($notifications as $row): ?>
  <p>
    {!tpl_userlink($row['user_id'])} -
    <a href="{$_SERVER['SCRIPT_NAME']}?do=details&amp;action=remove_notification&amp;task_id={$task['task_id']}&amp;ids={$task['task_id']}&amp;user_id={$row['user_id']}#notify">{L('remove')}</a>
  </p>
  <?php endforeach; ?>

  <?php if ($user->perms('manage_project')): ?>
  <form action="{CreateUrl(array('details', 'task' . $task['task_id']))}#notify" method="post">
    <p>
        <label class="default multisel" for="notif_user_id">{L('addusertolist')}</label>
        {!tpl_userselect('user_id', Req::val('user_id'), 'notif_user_id')}

      <button type="submit">{L('add')}</button>
      <input type="hidden" name="ids" value="{Req::num('ids', $task['task_id'])}" />
      <input type="hidden" name="action" value="add_notification" />
    </p>
  </form>
  <?php endif; ?>
</div>

