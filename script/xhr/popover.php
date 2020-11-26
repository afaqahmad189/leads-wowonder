<?php 
if ($f == 'popover') {
    $html        = '';
    $array_types = array(
        'user',
        'page',
        'group',
        'private-group' // private group type
    );
    if (!empty($_GET['id']) && !empty($_GET['type']) && in_array($_GET['type'], $array_types)) {
        if ($_GET['type'] == 'page') {
            $wo['popover'] = Wo_PageData($_GET['id']);
            if (!empty($wo['popover'])) {
                $html = Wo_LoadPage('popover/page-content');
            }
        } else if ($_GET['type'] == 'user') {
            $wo['popover'] = Wo_UserData($_GET['id']);
            if (!empty($wo['popover'])) {
                $html = Wo_LoadPage('popover/content');
            }
        } else if ($_GET['type'] == 'group') {
            $wo['popover'] = Wo_GroupData($_GET['id']);
            if (!empty($wo['popover'])) {
                $html = Wo_LoadPage('popover/group-content');
            }
        } else if ($_GET['type'] == 'private-group') {
            // for private groups
            $wo['popover'] = Wo_PrivateGroupData($_GET['id']);
            if (!empty($wo['popover'])) {
                $html = Wo_LoadPage('popover/private-content');
            }
        }
    }
    $data = array(
        'status' => 200,
        'html' => $html
    );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}


