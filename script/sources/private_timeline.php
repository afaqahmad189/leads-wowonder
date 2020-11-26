<?php
if ($wo['loggedin'] == false) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

if (isset($_GET['u'])) {
    $check_group = Wo_PrivateGroupExists($_GET['u']);

    if (in_array(true, array($check_group))) {
        $id                  = $group_id = Wo_PrivateGroupIdFromGroupname($_GET['u']);
        $wo['group_profile'] = Wo_PrivateGroupData($group_id);
        $type                = 'private_group';
        $about               = $wo['group_profile']['about'];
        $name                = $wo['group_profile']['name'];
    } else {
        header("Location: " . Wo_SeoLink('index.php?link1=404'));
        exit();
    }
} else {
    header("Location: " . $wo['config']['site_url']);
    exit();
}

if ($wo['config']['private_groups'] == 0) {
    header("Location: " . $wo['config']['site_url']);
    exit();
}

if (!Wo_IsPrivateGroupJoined($group_id, $wo['user']['user_id'])) {
    header("Location: " . Wo_SeoLink('index.php?link1=404'));
    exit();
}

if (!empty($_GET['ref'])) {
    if ($_GET['ref'] == 'se') {
        $regsiter_recent = Wo_RegsiterRecent($id, $type);
    }
}

if (!empty($_GET['type']) && in_array($_GET['type'], array('private_group'))) {
    $name = $name . " | " . Wo_Secure($_GET['type']);
}

$wo['description'] = $about;
$wo['keywords']    = '';
$wo['page']        = $type;
$wo['title']       = str_replace('&#039;', "'", $name);
$wo['content']     = Wo_LoadPage("{$type}/content");
