<?php
if ($wo['loggedin'] == false) {
  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
  exit();
}
if ($wo['config']['events'] == 0) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'events';
$wo['active']      = 3;
$wo['events']      = Wo_GetInvitedEvents();
$wo['title']       = $wo['lang']['event_invited'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('leads/leads-invited');
