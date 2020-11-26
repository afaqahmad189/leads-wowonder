<?php 
if ($f == "lead") {
    if ($s == 'insert') {
        if (empty($_POST['service_name']) || empty($_POST['tag_keyword']) || empty($_POST['group_category']) || empty($_POST['job_category'])  || empty($_POST['product_category'])  || empty($_POST['budget'])  || empty($_POST['service_location'])  || empty($_POST['details']) || Wo_CheckSession($hash_id) === FALSE) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        }
        if (empty($errors)) {
            $data_array = array(
                'user_id' => (int)Wo_Secure($wo['user']['user_id']),
                'service_name' => Wo_Secure($_POST['service_name']),
                'tag_keyword' => Wo_Secure($_POST['tag_keyword']),
                'group_category' => (int)Wo_Secure($_POST['group_category']),
                'job_category' => (int)Wo_Secure($_POST['job_category']),
                'product_category' => (int)Wo_Secure($_POST['product_category']),
                'budget' => Wo_Secure($_POST['budget']),
                'service_location' => Wo_Secure($_POST['service_location']),
                'details' => Wo_Secure($_POST['details']),
            );
            $query     = $db->insert(T_LEADS, $data_array);
            if ($query) {
                $data['status'] = 200;
                $data['message'] = $success_icon . $wo['lang']['lead_create_success'];
            }
        }else {
            $data = array(
                'status' => 400,
                'errors' => $errors
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'get_created_leads') {
//        if (empty($_POST['service_name']) || empty($_POST['tag_keyword']) || empty($_POST['group_category']) || empty($_POST['job_category'])  || empty($_POST['product_category'])  || empty($_POST['budget'])  || empty($_POST['service_location'])  || empty($_POST['details']) || Wo_CheckSession($hash_id) === FALSE) {
//            $errors[] = $error_icon . $wo['lang']['please_check_details'];
//        }
//        if (empty($errors)) {
            $data_array = array(
                'user_id' => (int)Wo_Secure($wo['user']['user_id']),
                'service_name' => Wo_Secure($_POST['service_name']),
                'tag_keyword' => Wo_Secure($_POST['tag_keyword']),
                'group_category' => (int)Wo_Secure($_POST['group_category']),
                'job_category' => (int)Wo_Secure($_POST['job_category']),
                'product_category' => (int)Wo_Secure($_POST['product_category']),
                'budget' => Wo_Secure($_POST['budget']),
                'service_location' => Wo_Secure($_POST['service_location']),
                'details' => Wo_Secure($_POST['details']),
            );
            $query     = $db->get(T_LEADS);
            if ($query) {
                foreach ($query->result() as $row){
                    echo'kjbkjb';
                }
                 $data['status'] = 200;
//                $data['message'] = $success_icon . $wo['lang']['lead_create_success'];
            }
//        }
//    else {
//            $data = array(
//                'status' => 400,
//                'errors' => $errors
//            );
//        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }


}
