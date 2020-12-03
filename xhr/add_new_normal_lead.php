<?php
if ($f == "add_new_normal_lead" && Wo_IsAdmin() == true && Wo_CheckSession($hash_id) === true) {
    if (empty($_POST['name']) || empty($_POST['tag']) || empty($_POST['service_location'])
            ||empty($_POST['budget']) || empty($_POST['detail']) ) {
        $error = $error_icon . $wo['lang']['please_check_details'];
    }
    if (empty($error)) {
        $registration_data = array(
            'service_name' => Wo_Secure($_POST['name']),
            'tags' => Wo_Secure($_POST['tag']),
            'service_location' => Wo_Secure($_POST['service_location']),
            'budget'=>WO_Secure($_POST['budget']),
            'detail'=>WO_Secure($_POST['detail'])
        );
        if (Wo_Addnormallead($registration_data))
        {
            $data = array(
                'message' => $success_icon . $wo['lang']['forum_post_added'],
                'status' => 200
            );
        }
        else {
            $data = array(
                'status' => 500,
                'message' => $wo['lang']['please_check_details']
            );
        }
    } else {
        $data = array(
            'status' => 500,
            'message' => $error
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
