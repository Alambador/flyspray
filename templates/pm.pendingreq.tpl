<div id="toolbox">
  <h3>{L('pmtoolbox')} :: {L('pendingrequests')}</h3>

  <fieldset class="box">
    <legend>{L('pendingrequests')}</legend>

    <?php if (!count($pendings)): ?>
    {L('nopendingreq')}
    <?php else: ?>
    <table class="requests">
      <tr>
        <th>{L('eventdesc')}</th>
        <th>{L('requestedby')}</th>
        <th>{L('daterequested')}</th>
        <th>{L('reasongiven')}</th>
        <th class="pm-buttons"> </th>
      </tr>
      <?php foreach ($pendings as $req): ?>
      <tr>
        <td>
        <?php if ($req['request_type'] == 1) : ?>
        {L('closetask')} -
        <a href="{$this->url(array('details', 'task' . $req['task_id']))}">{$proj->prefs['project_prefix']}#{$req['task_id']} :
          {$req['item_summary']}</a>
        <?php elseif ($req['request_type'] == 2) : ?>
        {L('reopentask')} -
        <a href="{$this->url(array('details', 'task' . $req['task_id']))}">{$proj->prefs['project_prefix']}#{$req['task_id']} :
          {$req['item_summary']}</a>
        <?php endif; ?>
        </td>
        <td>{!tpl_userlink($req['user_id'])}</td>
        <td>{formatDate($req['time_submitted'], true)}</td>
        <td>{$req['reason_given']}</td>
        <td>
          <?php if ($req['request_type'] == 1) : ?>
          <a class="button" href="{$this->url(array('details', 'task' . $req['task_id']), array('showclose' => 1))}#formclosetask">{L('accept')}</a>
          <?php elseif ($req['request_type'] == 2) : ?>
          <a class="button" href="{$_SERVER['SCRIPT_NAME']}?do=details&amp;action=reopen&task_id={$req['task_id']}">{L('accept')}</a>
          <?php endif; ?>
          <a href="#" class="button" onclick="showhidestuff('denyform{$req['request_id']}');">{L('deny')}</a>
          <div id="denyform{$req['request_id']}" class="denyform">
            <form action="{$this->url(array('pm', 'proj' . $proj->id, 'pendingreq'))}" method="post">
              <div>
                <input type="hidden" name="action" value="denypmreq" />
                <input type="hidden" name="req_id" value="{$req['request_id']}" />
                <label for="deny_reason{$req['request_id']}" class="inline">{L('reasonfordeinal')}</label><br />
                <textarea cols="40" rows="5" name="deny_reason" id="deny_reason{$req['request_id']}"></textarea>
                <br />
                <button type="submit">{L('deny')}</button>
              </div>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>

  </fieldset>
</div>
