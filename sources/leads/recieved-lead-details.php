<?php
//var_dump("ajk");
//die("12");
if ($wo['loggedin'] == false) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
//if (isset($_GET['group']) && !empty($_GET['group'])) {
//    if (Wo_LeadExists($_GET['group']) === false) {
//        header("Location: " . Wo_SeoLink('index.php?link1=404'));
//        exit();
//    }
//    $lead_id=Wo_GetPostIdFromUrl($_GET['group']);
//    $wo['setting'] = Wo_Getcreated_lead_data($lead_id);
//}
//if ($wo['config']['events'] == 0) {
//	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//    exit();
//}
//var_dump("ajk");
//die("12");
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'leads';
$wo['active']      = 6;
$wo['title']       = $wo['lang']['lead_details_title'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('leads/received-lead-details');