<div class="redirectmessage">
  <p><em>{$modify_text['relatedproject']}</em></p>
  <form action="index.php" method="post">
    <input type="hidden" name="do" value="modify">
    <input type="hidden" name="action" value="add_related">
    <input type="hidden" name="this_task" value="{Post::val('this_task')}">
    <input type="hidden" name="related_task" value="{Post::val('related_task')}">
    <input type="hidden" name="allprojects" value="1">
    <input class="adminbutton" type="submit" value="{$modify_text['addanyway']}">
  </form>
  <form action="index.php" method="get">
    <input type="hidden" name="do" value="details">
    <input type="hidden" name="id" value="{Post::val('this_task')}">
    <input type="hidden" name="area" value="related">
    <input class="adminbutton" type="submit" value="{$modify_text['cancel']}">
  </form>
</div>
