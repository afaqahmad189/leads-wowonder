<?php
if ($wo['loggedin'] == false) {
  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
  exit();
}
//if ($wo['config']['groups'] == 0) {
//  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//  exit();
//}

//$groupData = Wo_GetMyPrivateGroups();
//if (count($groupData) > 0 || empty($wo['private_categories'])) {
//  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//  exit();
//}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'create_group';
$wo['title']       = $wo['lang']['create_new_group'];
//$wo['content']     = Wo_LoadPage('private_lead/leads-received-bid');
$wo['content']     = Wo_LoadPage('leads/create-lead');
