<?php
if ($f == 'get_payment_method_packages') {
                    $pkg_id        = $_GET['pkg_id'];
            $wo['hide'] = false;
//            if (strpos($_SERVER["HTTP_REFERER"], 'wallet') !== false) {
//                $wo['hide'] = true;
//            }
            $load = Wo_LoadPage('modals/pay-go-pro-packages');
//            $load = str_replace('{pro_type}', $pkg_id, $load);
            $load = str_replace('{pro_type_id}', $pkg_id, $load);
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
