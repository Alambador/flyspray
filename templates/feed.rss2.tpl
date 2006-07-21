{!'<?xml version="1.0" ?>'}
<rss version="2.0">
  <channel>
    <title>{$fs->prefs[page_title]}</title>
    <lastBuildDate>{date('r',$most_recent)}</lastBuildDate>
    <description>{$feed_description}</description>
    <link>{$baseurl}</link>
    <?php if($feed_image): ?>
    <image>
      <url>{$feed_image}</url>
      <link>{$baseurl}</link>
      <title>[Logo]</title>
    </image>
    <?php endif;
    foreach($task_details as $row):?>
    <item>
      <title>{$row['item_summary']}</title>
      <author>{$row['real_name'] . " <" . $row['email_address'] . ">"}</author>
      <pubDate>{date('r',intval($row['last_edited_time']))}</pubDate>
      <description><![CDATA[{!str_replace(chr(13), "<br />", htmlspecialchars(strip_tags($row['detailed_desc'])))}]]></description>
      <link>{CreateURL('details', $row['task_id'])}</link>
      <guid>{CreateURL('details', $row['task_id'])}</guid>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>
