<?php

use Aws\S3\S3Client;
use Google\Cloud\Storage\StorageClient;

if ($f == 'send-lead' and (Wo_IsAdmin() || Wo_IsModerator())) {
    if ($s == 'send-created-lead') {
        $lead_id=$_POST['lead_id'];
        foreach ($_POST['user'] as $user_id){
            insert_recieved_lead($lead_id,$user_id);
        }

        $data = array(
            'status' => 200
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
