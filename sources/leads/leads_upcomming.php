<?php

if ($wo['config']['leads_visibility'] == 1) {
	if ($wo['loggedin'] == false) {
	  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
	  exit();
	}
}

//if ($wo['config']['leads'] == 0) {
//	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//    exit();
//}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'leads';
$wo['active']      = 1;
$wo['events']      = Wo_GetEvents();
$wo['title']       = $wo['lang']['leads_upcoming'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('leads/leads-upcomming');