<?php
//var_dump("ajk");
//die("12");
if ($wo['loggedin'] == false) {
  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
  exit();
}
//if ($wo['config']['events'] == 0) {
//	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//    exit();
//}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'leads';
$wo['active']      = 6;
$wo['title']       = $wo['lang']['create_leads'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('leads/create-lead');