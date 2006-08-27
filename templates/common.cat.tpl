<fieldset class="box">
  <legend>{L('categories')}</legend>
  <p>{L('listnote')}</p>
  <div id="controlBox">
    <div class="grip"></div>
    <div class="inner">
        <a href="#" onclick="TableControl.up('catTable'); return false;"><img src="{$this->themeUrl()}/up.png" alt="Up" /></a>
        <a href="#" onclick="TableControl.down('catTable'); return false;"><img src="{$this->themeUrl()}/down.png" alt="Down" /></a>
        <a href="#" onclick="TableControl.shallower('catTable'); return false;"><img src="{$this->themeUrl()}/left.png" alt="Left" /></a>
        <a href="#" onclick="TableControl.deeper('catTable'); return false;"><img src="{$this->themeUrl()}/right.png" alt="Right" /></a>
    </div>
  </div>
    <form action="{$baseurl}" method="post">
      <table class="list" id="catTable">
         <thead>
         <tr>
           <th>{L('name')}</th>
           <th>{L('owner')}</th>
           <th>{L('show')}</th>
           <th>{L('delete')}</th>
         </tr>
       </thead>
       <tbody>
        <?php
        $countlines = -1;
        $categories = $proj->listCategories($proj->id, false, false);
        $root = $categories[0];
        unset($categories[0]);
        
        foreach ($categories as $row):
            $countlines++;
        ?>
        <tr class="depth{$row['depth']}">
          <td class="first">
            <input type="hidden" name="lft[]" value="{$row['lft']}" />
            <input type="hidden" name="rgt[]" value="{$row['rgt']}" />
            <input type="hidden" name="id[]" value="{$row['category_id']}" />
            <span class="depthmark">{!str_repeat('&rarr;', $row['depth'])}</span>
            <input id="categoryname{$countlines}" class="text" type="text" size="15" maxlength="40" name="list_name[]" 
              value="{$row['category_name']}" />
          </td>
          <td title="{L('categoryownertip')}">
            {!tpl_userselect('category_owner' . $countlines, $row['category_owner'], 'categoryowner' . $countlines)}
          </td>
          <td title="{L('listshowtip')}">
            {!tpl_checkbox('show_in_list['.$countlines.']', $row['show_in_list'], 'showinlist'.$countlines)}
          </td>
          <td title="{L('listdeletetip')}">
            <input id="delete{$row['category_id']}" type="checkbox"
            <?php if ($row['used_in_tasks']): ?>disabled="disabled"<?php endif; ?>
            name="delete[{$row['category_id']}]" value="1" />
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if($countlines > -1): ?>
        <tr>
          <td colspan="3"></td>
          <td class="buttons">
            <input type="hidden" name="action" value="update_category" />
            <input type="hidden" name="list_type" value="category" />
            <input type="hidden" name="project_id" value="{$proj->id}" />
            <button type="submit">{L('update')}</button>
          </td>
        </tr>
        <?php endif; ?>
      </table>
      <script type="text/javascript">
        <?php
            echo 'TableControl.create("catTable",{
                controlBox: "controlBox",
                tree: true,
                spreadActiveClass: true
            });';
            echo 'new Draggable("controlBox",{
                handle: "grip"
            });';
        ?>
      </script>
    </form>

    <hr />

    <!-- Form to add a new category to the list -->
    <form action="{$baseurl}" method="post">
      <table class="list">
        <tr>
          <td>
            <input id="listnamenew" class="text" type="text" size="15" maxlength="40" name="list_name" />
          </td>
          <td title="{L('categoryownertip')}">
            {!tpl_userselect('category_owner', Req::val('category_owner'), 'categoryownernew')}
          </td>
          <td title="{L('categoryparenttip')}">
            <label for="parent_id">{L('parent')}</label>
            <select id="parent_id" name="parent_id">
              <option value="{$root['category_id']}">{L('notsubcategory')}</option>
              <?php $cat_opts = array_map(
              create_function('$x', 'return array($x["category_id"], $x["category_name"]);'),
              $categories);
              ?>
              {!tpl_options($cat_opts, Req::val('parent_id'))}
            </select>
          </td>
          <td class="buttons">
            <input type="hidden" name="action" value="{Req::val('action', $do . '.add_category')}" />
            <input type="hidden" name="area" value="{Req::val('area')}" />
            <?php if ($proj->id): ?>
            <input type="hidden" name="project_id" value="{$proj->id}" />
            <?php endif; ?>
            <button type="submit">{L('addnew')}</button>
          </td>
        </tr>
      </table>
    </form>
</fieldset>
