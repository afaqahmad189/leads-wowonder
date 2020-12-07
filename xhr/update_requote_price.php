<?php
if ($f == "update_requote_price") {
    $data = array(
        'status' => 500
    );
//    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        if (Wo_UpdateRequotePrice($_POST['received_id'],$_POST['requote_price'])) {
            $data['status'] = 200;
        }
//    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
