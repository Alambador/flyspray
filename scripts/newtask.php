<?php

/*
   This script allows a user to open a new task.
*/

$fs->get_language_pack('newtask');
$fs->get_language_pack('index');
$fs->get_language_pack('details');
$fs->get_language_pack('status');
$fs->get_language_pack('severity');
$fs->get_language_pack('priority');

if (!$user->perms['open_new_tasks'] && $proj->prefs['anon_open'] != '1') {
    // Check if the user has the right to open new tasks
    $fs->Redirect( $fs->CreateURL('error', null) );
}

?>
<h3><?php echo htmlspecialchars($proj->prefs['project_title']) . ':: ' . $newtask_text['newtask'];?></h3>

<div id="taskdetails">
  <form enctype="multipart/form-data" action="<?php echo $conf['general']['baseurl'];?>index.php" method="post">
    <div>
      <table>
      <tr>
        <td>
          <input type="hidden" name="do" value="modify" />
          <input type="hidden" name="action" value="newtask" />
          <input type="hidden" name="project_id" value="<?php echo $proj->id;?>" />
          <label for="itemsummary"><?php echo $newtask_text['summary'];?></label>
        </td>
        <td>
          <input id="itemsummary" type="text" name="item_summary" size="50" maxlength="100" />
        </td>
      </tr>
      </table>

      <div id="taskfields1">

        <table>
        <tr>
          <td><label for="tasktype"><?php echo $newtask_text['tasktype'];?></label></td>
          <td>
            <select name="task_type" id="tasktype">
            <?php
            // Get list of task types
            $get_tasktype = $db->Query("SELECT  tasktype_id, tasktype_name FROM {list_tasktype}
                                         WHERE  show_in_list = '1' AND (project_id = '0' OR project_id = ?)
                                      ORDER BY  list_position", array($proj->id));

            while ($row = $db->FetchArray($get_tasktype)) {
                echo "<option value=\"{$row['tasktype_id']}\">{$row['tasktype_name']}</option>";
            }
            ?>
            </select>
          </td>
          </tr>

          <tr>
            <td><label for="productcategory"><?php echo $newtask_text['category'];?></label></td>
            <td>
              <select class="adminlist" name="product_category" id="productcategory">
              <?php
              // Get list of categories
              $cat_list = $db->Query("SELECT  category_id, category_name
                                        FROM  {list_category}
                                       WHERE  show_in_list = '1' AND parent_id < '1'
                                              AND (project_id = '0' OR project_id = ?)
                                    ORDER BY  list_position", array($proj->id));

              while ($row = $db->FetchArray($cat_list)) {
                  $category_name = $row['category_name'];
                  echo "<option value=\"{$row['category_id']}\">$category_name</option>\n";

                  $subcat_list = $db->Query("SELECT  category_id, category_name
                                               FROM  {list_category}
                                              WHERE  show_in_list = '1' AND parent_id = ?
                                           ORDER BY  list_position", array($row['category_id']));

                  while ($subrow = $db->FetchArray($subcat_list)) {
                      $subcategory_name = $subrow['category_name'];
                      echo "<option value=\"{$subrow['category_id']}\">&nbsp;&nbsp;&rarr;$subcategory_name</option>\n";
                  }
              }
              ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="itemstatus"><?php echo $newtask_text['status'];?></label></td>
            <td>
              <select id="itemstatus" name="item_status" <?php if (!$user->perms['modify_all_tasks']) echo ' disabled="disabled"';?>>
              <?php
              // Get list of statuses
              foreach($status_list as $key => $val) {
                 if ($key == '2') {
                    echo "<option value=\"$key\" selected=\"selected\">$val</option>\n";
                 } else {
                    echo "<option value=\"$key\">$val</option>\n";
                 }
              }
              ?>
              </select>
            </td>
          </tr>

          <tr>
            <td>
              <?php if (!$user->perms['modify_all_tasks']):
                  // If the user cant modify jobs, we will have to set a hidden field for the status and priority
              ?>
              <input type="hidden" name="item_status"   value="1" />
              <input type="hidden" name="task_priority" value="2" />
              <?php endif; ?>
              <label for="assignedto"><?php echo $newtask_text['assignedto'];?></label>
            </td>
            <td>
              <select id="assignedto" name="assigned_to" <?php if (!$user->perms['modify_all_tasks']) echo ' disabled="disabled"';?>>
              <?php // Get list of users
              echo "<option value=\"0\">{$newtask_text['noone']}</option>\n";
              $fs->ListUsers($proj->id);
              ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="operatingsystem"><?php echo $newtask_text['operatingsystem'];?></label></td>
            <td>
              <select id="operatingsystem" name="operating_system">
              <?php // Get list of operating systems
              $get_os = $db->Query("SELECT  os_id, os_name
                                      FROM  {list_os}
                                     WHERE  (project_id = ?  OR project_id = '0')
                                            AND show_in_list = '1'
                                  ORDER BY  list_position", array($proj->id));

              while ($row = $db->FetchArray($get_os)) {
                  echo "<option value=\"{$row['os_id']}\">{$row['os_name']}</option>";
              }
              ?>
              </select>
            </td>
          </tr>
        </table>
      </div>

      <div id="taskfields2">
        <table>
        <tr>
          <td><label for="taskseverity"><?php echo $newtask_text['severity'];?></label></td>
          <td>
            <select id="taskseverity" class="adminlist" name="task_severity">
            <?php // Get list of severities
            foreach($severity_list as $key => $val) {
               if ($key == '2') {
                  echo "<option value=\"$key\" selected=\"selected\">$val</option>\n";
               } else {
                  echo "<option value=\"$key\">$val</option>\n";
               }
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="task_priority"><?php echo $newtask_text['priority'];?></label></td>
          <td>
            <select id="task_priority" name="task_priority" <?php if (!$user->perms['modify_all_tasks']) echo ' disabled="disabled"';?>>
            <?php // Get list of statuses
            foreach($priority_list as $key => $val) {
               if ($key == '2') {
                  echo "<option value=\"$key\" selected=\"selected\">$val</option>\n";
               } else {
                  echo "<option value=\"$key\">$val</option>\n";
               }
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="productversion"><?php echo $newtask_text['reportedversion'];?></label></td>
          <td>
            <select class="adminlist" name="product_version" id="productversion">
            <?php // Get list of versions
            $get_version = $db->Query("SELECT  version_id, version_name
                                         FROM  {list_version}
                                        WHERE  show_in_list = '1' AND version_tense = '2'
                                               AND (project_id = '0' OR project_id = ?)
                                     ORDER BY  list_position", array($proj->id,));

            while ($row = $db->FetchArray($get_version)) {
                echo "<option value=\"{$row['version_id']}\">{$row['version_name']}</option>\n";
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="closedbyversion"><?php echo $newtask_text['dueinversion'];?></label></td>
          <td>
            <select id="closedbyversion" name="closedby_version" <?php if (!$user->perms['modify_all_tasks']) echo ' disabled="disabled"';?>>
            <?php
            echo "<option value=\"\">{$newtask_text['undecided']}</option>\n";

            $get_version = $db->Query("SELECT  version_id, version_name
                                         FROM  {list_version}
                                        WHERE  show_in_list = '1' AND version_tense = '3'
                                               AND (project_id = '0' OR project_id = ?)
                                     ORDER BY  list_position", array($proj->id,));

            while ($row = $db->FetchArray($get_version)) {
                echo "<option value=\"{$row['version_id']}\">{$row['version_name']}</option>\n";
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="duedate"><?php echo $newtask_text['duedate'];?></label></td>
          <td id="duedate">
            <input id="duedatehidden" type="hidden" name="due_date" value="" />
            <span id="duedateview"><?php echo $index_text['selectduedate'];?></span> <small>|</small>
            <a href="#" onclick="document.getElementById('duedatehidden').value = '0';document.getElementById('duedateview').innerHTML = '<?php echo $index_text['selectduedate']?>'">X</a>
            <script type="text/javascript">
               Calendar.setup({
                      inputField  : "duedatehidden",// ID of the input field
                      ifFormat    : "%d-%b-%Y",     // the date format
                      displayArea : "duedateview",  // The display field
                      daFormat    : "%d-%b-%Y",
                      button      : "duedateview"   // ID of the button
               });
            </script>
          </td>
        </tr>
        </table>
      </div>

      <div id="taskdetailsfull">
        <label for="details"><?php echo $newtask_text['details'];?></label>
        <textarea id="details" name="detailed_desc" cols="70" rows="10"></textarea>
      </div>

      <?php if ($user->perms['create_attachments']): ?>
      <div id="uploadfilebox">
         <?php echo $details_text['uploadafile'];?>
         <input type="file" size="55" name="userfile[]" /><br />
      </div>
      <input class="adminbutton" type="button" onclick="addUploadFields()" value="<?php echo $details_text['selectmorefiles'];?>" />
      <?php endif; // End of checking 'create attachments' permission ?>

      <input class="adminbutton" type="submit" name="buSubmit" value="<?php echo $newtask_text['addthistask'];?>" accesskey="s"/>

      <?php if (!$user->isAnon()): ?>
      &nbsp;&nbsp;<input class="admintext" type="checkbox" name="notifyme" value="1" checked="checked" />
      <?php echo $newtask_text['notifyme']; ?>
      <?php endif; ?>
    </div>
  </form>
</div>
