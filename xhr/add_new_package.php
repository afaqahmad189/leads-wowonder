<?php
if ($f == "add_new_package" && Wo_IsAdmin() == true && Wo_CheckSession($hash_id) === true) {
    if (empty($_POST['name']) || empty($_POST['limit']) || empty($_POST['pricing'])) {
        $error = $error_icon . $wo['lang']['please_check_details'];
    }
    if (empty($error)) {
        $registration_data = array(
            'name' => Wo_Secure($_POST['name']),
            'send_limit' => Wo_Secure($_POST['limit']),
            'pricing' => Wo_Secure($_POST['pricing']),
            'year' => Wo_Secure($_POST['year']),
            'month' => Wo_Secure($_POST['month'])
        );
        if (Wo_Packages($registration_data))
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
