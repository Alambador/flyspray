<?php echo '<?xml version="1.0" ?>' ?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title type="text">Flyspray</title>
  <subtitle type="text">
    {$feed_description}
  </subtitle>
  <id>{$baseurl}</id>
  <?php if($feed_image): ?>
  <icon>{$feed_image}</icon>
  <?php endif; ?>
  <updated>{date('Y-m-d\TH:i:s\Z',$most_recent)}</updated>
  <link rel="self" type="text/xml" href="{$baseurl}feed.php?feed_type=atom"/>
  <link rel="alternate" type="text/html" hreflang="en" href="{$baseurl}"/>
<?php foreach ($task_details as $row): ?>
  <entry>
    <title>{$row['item_summary']}</title>
    <link href="{$fs->CreateURL('details', $row['task_id'])}" />    
    <updated>{date('Y-m-d\TH:i:s\Z',$row['last_edited_time'])}</updated>    
    <published>{date('Y-m-d\TH:i:s\Z',$row['date_opened'])}</published>
    <content type="xhtml" xml:lang="en" 
     xml:base="http://diveintomark.org/">
      <div xmlns="http://www.w3.org/1999/xhtml">
        {!tpl_FormatText($row['detailed_desc'])}
      </div>
    </content>
    <author><name>{$row['real_name']}</name></author>
    <id>{$baseurl}:{$row['task_id']}</id>
  </entry>
<?php   endforeach; ?>
</feed>