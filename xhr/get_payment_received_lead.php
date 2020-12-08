<?php
if ($f == 'get_payment_received_lead') {
    $received_id        = $_GET['received_id'];
    $wo['hide'] = false;
    $load = Wo_LoadPage('modals/pay-go-pro-received-lead');
//            $load = str_replace('{pro_type}', $pkg_id, $load);
    $load = str_replace('{pro_type_id}', $received_id, $load);
//            $load = str_replace('{pro_type_description}', $description, $load);
//            $load = str_replace('{pro_type_price}', $price, $load);
    if (!empty($load)) {
        $data = array(
            'status' => 200,
            'html' => $load
        );
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
