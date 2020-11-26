<?php
if (isset($_GET['g'])) {
    if (Wo_PrivateGroupExists($_GET['g']) === true && Wo_PrivateGroupActive($_GET['g'])) {
        $group_id            = Wo_PrivateGroupIdFromGroupname($_GET['g']);
        $wo['group_profile'] = Wo_PrivateGroupData($group_id);
    } else {
        header("Location: " . Wo_SeoLink('index.php?link1=404'));
        exit();
    }
} else {
    header("Location: " . $wo['config']['site_url']);
    exit();
}
if ($wo['config']['private_groups'] == 0) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
$wo['description'] = $wo['group_profile']['about'];
$wo['keywords']    = '';
$wo['page']        = 'private_group';
$wo['title']       = $wo['group_profile']['name'];
$wo['content']     = Wo_LoadPage('private_group/content');