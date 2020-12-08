<?php
if ($f == "bank_transfer_received_lead") {
    if (Wo_CheckSession($hash_id) === true) {
        $request   = array();
        $request[] = (empty($_FILES["thumbnail"]));
//        print_r($request);
        if (in_array(true, $request) || empty($_POST['received_id'])) {
            $error = $error_icon . $wo['lang']['please_check_details'];
        }
        if (empty($error)) {
//            $description = !empty($_POST['description']) ? Wo_Secure($_POST['description']) : '';
            $pro = $wo['pro_packages'][$wo['pro_packages_types'][$_POST['type']]];
            $fileInfo      = array(
                'file' => $_FILES["thumbnail"]["tmp_name"],
                'name' => $_FILES['thumbnail']['name'],
                'size' => $_FILES["thumbnail"]["size"],
                'type' => $_FILES["thumbnail"]["type"],
                'types' => 'jpeg,jpg,png,bmp,gif'
            );
            $media = Wo_ShareFile($fileInfo);
            $mediaFilename = $media['filename'];
            if (!empty($mediaFilename)) {
                $insert_id = Wo_UpdateReceived_lead_payment(Wo_Secure($_POST['received_id']),$mediaFilename);
                if (!empty($insert_id)) {
                    $data = array(
                        'message' => $success_icon . $wo['lang']['package_send'],
                        'status' => 200
                    );
                }
            }
            else{
                $error = $error_icon . $wo['lang']['file_not_supported'];
                $data = array(
                    'status' => 500,
                    'message' => $error
                );
            }
        }
        else {
            $data = array(
                'status' => 500,
                'message' => $error
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
