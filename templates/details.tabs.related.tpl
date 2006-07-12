<div id="related" class="tab">
  <table> <?php // table based layout, sorry. if anyone has the desire to face browser bugs, feel free to rewrite it with floats ?>
   <tr><td>
    <form method="post" action="{$baseurl}" >
        <table id="tasks_related" class="userlist">
        <tr>
          <th>
            <a href="javascript:ToggleSelected('tasks_related')">
              <img title="{L('toggleselected')}" alt="{L('toggleselected')}" src="{$this->get_image('kaboodleloop')}" width="16" height="16" />
            </a>
          </th>
          <th>{L('tasksrelated')} ({count($related)})</th>
        </tr>
        <?php
          foreach ($related as $row):
        ?>
        <tr>
          <td class="ttcolumn">
            <input type="checkbox" name="related_id[]" {!tpl_disableif(!$user->can_edit_task($task_details))} value="{$row['related_id']}" /></td>
          <td>{!tpl_tasklink($row)}</td>
        </tr>
        <?php endforeach; ?>
        <td colspan="2">
            <input type="hidden" name="do" value="modify" />
            <input type="hidden" name="action" value="remove_related" />
            <input type="hidden" name="id" value="{Get::num('id')}" />
            <button type="submit">{L('remove')}</button>
        </td>
        </table>
    </form>
    </td><td>
    
    <table id="duplicate_tasks" class="userlist">
        <tr>
          <th>{L('duplicatetasks')} ({count($duplicates)})</th>
        </tr>
        <?php foreach ($duplicates as $row): ?>
        <tr><td>{!tpl_tasklink($row)}</td></tr>
        <?php endforeach; ?>
    </table>
    </td></tr>
  </table>

  <?php if ($user->can_edit_task($task_details) && !$task_details['is_closed']): ?>
  <form action="{$baseurl}#related" method="post" id="formaddrelatedtask">
    <div>
      <input type="hidden" name="do" value="modify" />
      <input type="hidden" name="action" value="details.add_related" />
      <input type="hidden" name="id" value="{Req::num('id')}" />
      <label>{L('addnewrelated')}
        <input name="related_task" id="related_task_input" type="text" class="text" size="10" maxlength="10" /></label>
      <button type="submit" onclick="return checkok('{$baseurl}javascript/callbacks/checkrelated.php?related_task=' + $('related_task_input').value + '&amp;project={$proj->id}', '{L('relatedproject')}', 'formaddrelatedtask')">{L('add')}</button>
    </div>
  </form>
  <?php endif; ?>
</div>
