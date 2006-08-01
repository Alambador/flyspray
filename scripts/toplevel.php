<?php

  /********************************************************\
  | Top level project overview                             |
  | ~~~~~~~~~~~~~                                          |
  \********************************************************/

if(!defined('IN_FS')) {
    die('Do not access this file directly.');
}

$projects = ($proj->id) ? array(0 => array('project_id' => $proj->id, 'project_title' => $proj->prefs['project_title'])) : $fs->projects;

$most_wanted = array();
$stats = array();

// Most wanted tasks for each project
foreach ($projects as $project) {
    $sql = $db->Query('SELECT v.task_id, count(*) AS num_votes
                         FROM {votes} v
                    LEFT JOIN {tasks} t ON v.task_id = t.task_id AND t.attached_to_project = ?
                        WHERE t.is_closed = 0
                     GROUP BY v.task_id
                     ORDER BY num_votes DESC 
                        LIMIT 5', array($project['project_id']));
    if ($db->CountRows($sql)) {
        $most_wanted[$project['project_id']] = $db->FetchAllArray($sql);
    }
}

// Project stats
foreach ($projects as $project) {
    $sql = $db->Query('SELECT count(*) FROM {tasks} WHERE attached_to_project = ?',
                      array($project['project_id']));
    $stats[$project['project_id']]['all'] = $db->fetchOne($sql);
    $sql = $db->Query('SELECT count(*) FROM {tasks} WHERE attached_to_project = ? AND is_closed = 0',
                      array($project['project_id']));
    $stats[$project['project_id']]['open'] = $db->fetchOne($sql);
    $sql = $db->Query('SELECT avg(percent_complete) FROM {tasks} WHERE attached_to_project = ? AND is_closed =0',
                      array($project['project_id']));
    $stats[$project['project_id']]['average_done'] = round($db->fetchOne($sql), 0);
}

$page->uses('most_wanted', 'stats', 'projects');

$page->setTitle($fs->prefs['page_title'] . $proj->prefs['project_title'] . ': ' . L('toplevel'));
$page->pushTpl('toplevel.tpl');

?>