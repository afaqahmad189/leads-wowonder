<?php
if ($f == "disapprove_user" && Wo_CheckMainSession($hash_id) === true) {
    $data = array(
        'status' => 500
    );
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        if (Wo_DisApproveUser(Wo_Secure($_GET['id']))) {
            Notification(Wo_Secure($_GET['user_id']),"Your Package Have Been  DisApproved.");
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
