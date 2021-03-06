<?php
if ($f == 'private_group') {
    if ($s == 'get_private_payment_method') {
        if (!empty($_GET['type'])) {
            $html            = '';
            $type = $_GET['type'];
            $types_array = array(
                'create',
                'join',
            );
            if (in_array($type, $types_array)) {
                $currency = $wo['config']['currency'];
                $symbol = Wo_GetCurrency($wo['config']['currency']);
                $price = ($type == 'create') ? $wo['config']['creation_fee'] : $wo['config']['joining_fee'];

                $load = Wo_LoadPage('modals/pay-private');
                $load = str_replace('{type}', $type, $load);
                $load = str_replace('{price}', $price, $load);

                if (!empty($load)) {
                    $data = array(
                        'status' => 200,
                        'html' => $load
                    );
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'bank_transfer') {
        if (Wo_CheckSession($hash_id) === true) {
            $request   = array();
            $request[] = (empty($_FILES["thumbnail"]));
            if (in_array(true, $request) || empty($_POST['type']) || !in_array($_POST['type'], array('create', 'join'))) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($error)) {
                $fileInfo      = array(
                    'file' => $_FILES["thumbnail"]["tmp_name"],
                    'name' => $_FILES['thumbnail']['name'],
                    'size' => $_FILES["thumbnail"]["size"],
                    'type' => $_FILES["thumbnail"]["type"],
                    'types' => 'jpeg,jpg,png,bmp,gif'
                );
                $media         = Wo_ShareFile($fileInfo);
                $mediaFilename = $media['filename'];
                if (!empty($mediaFilename)) {
                    $group_id = 0;
                    if (!empty($_POST['group_id'])) {
                        $group_id = $_POST['group_id'];
                    }

                    $insert_id = Wo_InsertBankTrnsfer(array(
                        'user_id'      => $wo['user']['id'],
                        'group_id'     => $group_id,
                        'description'  => ($_POST['type'] == "create") ? "Private Group Creation" : "Private Group Joining",
                        'price'        => ($_POST['type'] == "create") ? $wo['config']['creation_fee'] : $wo['config']['joining_fee'],
                        'type'         => 'group',
                        'receipt_file' => $mediaFilename,
                        'mode'         => Wo_Secure($_POST['type'])
                    ));
                    if (!empty($insert_id)) {
                        $data = array(
                            'message' => $success_icon . $wo['lang']['bank_transfer_request'],
                            'p_id' => $insert_id,
                            'status' => 200
                        );
                    }
                } else {
                    $error = $error_icon . $wo['lang']['file_not_supported'];
                    $data = array(
                        'status' => 500,
                        'message' => $error
                    );
                }
            } else {
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

    if ($s == 'create_group') {
        if (empty($_POST['group_name']) || empty($_POST['group_title']) || empty($_POST['address']) || empty($_POST['group_job_category']) || empty($_POST['group_employee_job_category']) || empty(Wo_Secure($_POST['group_title'])) || Wo_CheckSession($hash_id) === false) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        } else {
            $is_exist = Wo_IsNameExist($_POST['group_name'], 0);
            if (in_array(true, $is_exist)) {
                $errors[] = $error_icon . $wo['lang']['group_name_exists'];
            }
            if (in_array($_POST['group_name'], $wo['site_pages'])) {
                $errors[] = $error_icon . $wo['lang']['group_name_invalid_characters'];
            }
            if (strlen($_POST['group_name']) < 5 or strlen($_POST['group_name']) > 32) {
                $errors[] = $error_icon . $wo['lang']['group_name_characters_length'];
            }
            if (!preg_match('/^[\w]+$/', $_POST['group_name'])) {
                $errors[] = $error_icon . $wo['lang']['group_name_invalid_characters'];
            }
            if (empty($_POST['payment_id'])) {
                $errors[] = $error_icon . $wo['lang']['creation_error'];
            }
        }
        if (empty($errors)) {
            $re_group_data = array(
                'group_name' => Wo_Secure($_POST['group_name']),
                'payment_id' => Wo_Secure($_POST['payment_id']),
                'user_id' => Wo_Secure($wo['user']['user_id']),
                'group_title' => Wo_Secure($_POST['group_title']),
                'address' => Wo_Secure($_POST['address']),
                'about' => Wo_Secure($_POST['about']),
                'category' => Wo_Secure($_POST['category'])
            );

            $group_id = Wo_RegisterPrivateGroup($re_group_data);
            if (!empty($group_id)) {
                $user_id = $wo['user']['id'];
                $active = 1;

                $job_category = $_POST['group_job_category'];
                $query = mysqli_query($sqlConnect, " INSERT INTO " . T_PRIVATE_MEMBERS . " (`user_id`,`group_id`,`category`,`active`,`time`) VALUES ({$user_id},{$group_id},{$job_category},'{$active}'," . time() . ")");

                $employee_categories = $_POST['group_employee_job_category'];
                if (!is_array($employee_categories)) {
                    $employee_categories = array($employee_categories);
                }

                foreach ($employee_categories as $category_id) {
                    mysqli_query($sqlConnect, " INSERT INTO " . T_PRIVATE_ALLOWED_CATEGORY . " (`group_id`,`category_id`,`jobcategory_id`) VALUES ({$group_id},{$job_category},{$category_id})");
                }

                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['group_created'],
                    'location' => Wo_SeoLink('index.php?link1=timeline&u=' . $re_group_data['group_name'])
                );
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'update_group_avatar_picture') {
        if (isset($_FILES['avatar']['name']) && !empty($_POST['group_id']) && is_numeric($_POST['group_id']) && $_POST['group_id'] > 0) {
            if (Wo_UploadImage($_FILES["avatar"]["tmp_name"], $_FILES['avatar']['name'], 'avatar', $_FILES['avatar']['type'], $_POST['group_id'], 'private_group')) {
                $img  = Wo_PrivateGroupData($_POST['group_id']);
                $data = array(
                    'status' => 200,
                    'img' => $img['avatar']
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'update_group_cover_picture') {
        if (isset($_FILES['cover']['name']) && !empty($_POST['group_id']) && is_numeric($_POST['group_id']) && $_POST['group_id'] > 0) {
            if (Wo_UploadImage($_FILES["cover"]["tmp_name"], $_FILES['cover']['name'], 'cover', $_FILES['cover']['type'], $_POST['group_id'], 'private_group')) {
                $img  = Wo_PrivateGroupData($_POST['group_id']);
                $data = array(
                    'status' => 200,
                    'img' => $img['cover']
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'update_images_setting') {
        if (isset($_POST['group_id']) && is_numeric($_POST['group_id']) && $_POST['group_id'] > 0 && Wo_CheckSession($hash_id) === true) {
            $Userdata = Wo_PrivateGroupData($_POST['group_id']);
            if (!empty($Userdata['id'])) {
                if (!empty($_FILES['avatar']['name'])) {
                    if (Wo_UploadImage($_FILES["avatar"]["tmp_name"], $_FILES['avatar']['name'], 'avatar', $_FILES['avatar']['type'], $_POST['group_id'], 'private_group') === true) {
                        $page_data = Wo_PrivateGroupData($_POST['group_id']);
                    }
                }
                if (!empty($_FILES['cover']['name'])) {
                    if (Wo_UploadImage($_FILES["cover"]["tmp_name"], $_FILES['cover']['name'], 'cover', $_FILES['cover']['type'], $_POST['group_id'], 'private_group') === true) {
                        $page_data = Wo_PrivateGroupData($_POST['group_id']);
                    }
                }
                if ($Userdata['user_id'] == $wo['user']['id'] || Wo_IsCanPrivateGroupUpdate($_POST['group_id'], 'avatar')) {
                    if (empty($errors)) {
                        $userdata2 = Wo_PrivateGroupData($_POST['group_id']);
                        $data      = array(
                            'status' => 200,
                            'message' => $success_icon . $wo['lang']['setting_updated'],
                            'cover' => $userdata2['cover'],
                            'avatar' => $userdata2['avatar']
                        );
                    }
                } else {
                    $errors[] = $error_icon . $wo['lang']['please_check_details'];
                }
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
    }

    if ($s == 'update_general_settings') {
        if (!empty($_POST['group_id']) && is_numeric($_POST['group_id']) && !empty($_POST['address']) && $_POST['group_id'] > 0 && Wo_CheckSession($hash_id) === true) {
            $group_data = Wo_PrivateGroupData($_POST['group_id']);
            if (empty($_POST['group_name']) or empty($_POST['group_title']) or empty(Wo_Secure($_POST['group_title']))) {
                $errors[] = $error_icon . $wo['lang']['please_check_details'];
            } else {
                if ($_POST['group_name'] != $group_data['group_name']) {
                    $is_exist = Wo_IsNameExist($_POST['group_name'], 0);
                    if (in_array(true, $is_exist)) {
                        $errors[] = $error_icon . $wo['lang']['group_name_exists'];
                    }
                }
                if (in_array($_POST['group_name'], $wo['site_pages'])) {
                    $errors[] = $error_icon . $wo['lang']['group_name_invalid_characters'];
                }
                if (strlen($_POST['group_name']) < 5 || strlen($_POST['group_name']) > 32) {
                    $errors[] = $error_icon . $wo['lang']['group_name_characters_length'];
                }
                if (!preg_match('/^[\w]+$/', $_POST['group_name'])) {
                    $errors[] = $error_icon . $wo['lang']['group_name_invalid_characters'];
                }
                if ($group_data['user_id'] == $wo['user']['id'] || Wo_IsCanPrivateGroupUpdate($_POST['group_id'], 'general')) {
                    if (empty($errors)) {
                        $Update_data = array(
                            'group_name' => $_POST['group_name'],
                            'group_title' => $_POST['group_title'],
                            'address' => Wo_Secure($_POST['address']),
                            'about' => $_POST['about']
                        );

                        if (Wo_UpdatePrivateGroupData($_POST['group_id'], $Update_data)) {
                            if (!empty($_POST['group_employee_job_category'])) {
                                $group_id = $group_data['id'];
                                $job_category = $group_data['category_id'];
                                $employee_categories = $_POST['group_employee_job_category'];
                                if (!is_array($employee_categories)) {
                                    $employee_categories = array($employee_categories);
                                }

                                foreach ($employee_categories as $category_id) {
                                    mysqli_query($sqlConnect, " INSERT INTO " . T_PRIVATE_ALLOWED_CATEGORY . " (`group_id`,`category_id`,`jobcategory_id`) VALUES ({$group_id},{$job_category},{$category_id})");
                                }
                            }

                            $data = array(
                                'status' => 200,
                                'message' => $success_icon . $wo['lang']['setting_updated']
                            );
                        }
                    }
                } else {
                    $errors[] = $error_icon . $wo['lang']['please_check_details'];
                }
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'delete_group') {
        if (!empty($_POST['group_id']) && is_numeric($_POST['group_id']) && $_POST['group_id'] > 0 && Wo_CheckSession($hash_id) === true) {
            if (!Wo_HashPassword($_POST['password'], $wo['user']['password']) && !Wo_CheckPrivateGroupAdminPassword($_POST['password'], $_POST['group_id'])) {
                $errors[] = $error_icon . $wo['lang']['current_password_mismatch'];
            }
            $group_data = Wo_PrivateGroupData($_POST['group_id']);
            if ($group_data['user_id'] == $wo['user']['id'] || Wo_IsCanPrivateGroupUpdate($_POST['group_id'], 'delete_group')) {

                if (empty($errors)) {
                    if (Wo_DeletePrivateGroup($_POST['group_id']) === true) {
                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . $wo['lang']['group_deleted'],
                            'location' => Wo_SeoLink('index.php?link1=private-groups')
                        );
                    }
                }
            } else {
                $errors[] = $error_icon . $wo['lang']['please_check_details'];
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'delete_joined_user') {
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id']) && $_GET['user_id'] > 0 && !empty($_GET['group_id']) && is_numeric($_GET['group_id']) && $_GET['group_id'] > 0) {
            $group_data = Wo_PrivateGroupData($_GET['group_id']);
            if ($group_data['user_id'] == $wo['user']['id'] || Wo_IsCanPrivateGroupUpdate($_GET['group_id'], 'members')) {
                if (Wo_LeavePrivateGroup($_GET['group_id'], $_GET['user_id']) === true) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'privileges') {
        if (!empty($_POST['group_id']) && is_numeric($_POST['group_id']) && $_POST['group_id'] > 0 && !empty($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
            $group_data = Wo_PrivateGroupData($_POST['group_id']);
            if ($group_data['user_id'] == $wo['user']['id'] || Wo_IsCanPrivateGroupUpdate($_POST['group_id'], 'members')) {

                $update_array = array('general' => 0, 'avatar' => 0, 'members' => 0, 'analytics' => 0, 'delete_group' => 0);
                if (!empty($_POST['general']) && $_POST['general'] == 1) {
                    $update_array['general'] = 1;
                }
                if (!empty($_POST['avatar']) && $_POST['avatar'] == 1) {
                    $update_array['avatar'] = 1;
                }
                if (!empty($_POST['members']) && $_POST['members'] == 1) {
                    $update_array['members'] = 1;
                }
                if (!empty($_POST['analytics']) && $_POST['analytics'] == 1) {
                    $update_array['analytics'] = 1;
                }
                if (!empty($_POST['delete_group']) && $_POST['delete_group'] == 1) {
                    $update_array['delete_group'] = 1;
                }

                if (Wo_UpdatePrivateGroupAdminData($_POST['group_id'], $update_array, $_POST['user_id'])) {
                    $data = array(
                        'status' => 200,
                        'message' => $success_icon . $wo['lang']['setting_updated']
                    );
                }
            } else {
                $errors[] = $error_icon . $wo['lang']['please_check_details'];
            }
        } else {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'invite_friends') {
        if (!empty($_GET['group_id']) && is_numeric($_GET['group_id']) && $_GET['group_id'] > 0 && !empty($_GET['user_id']) && is_numeric($_GET['user_id']) && $_GET['user_id'] > 0) {
            $wo['user_data'] = Wo_UserData($_GET['user_id']);
            $wo['group_data'] = Wo_PrivateGroupData($_GET['group_id']);
            if (!empty($wo['group_data']) && !empty($wo['user_data'])) {
                $wo['token'] = Wo_RegisterPrivateGroupInvite($wo['user_data']['user_id'], $wo['group_data']['id']);
                if (!empty($wo['token'])) {
                    $email             = Wo_Secure($wo['user_data']['email']);
                    $message           = Wo_LoadPage('emails/group-invite');

                    $send_message_data = array(
                        'from_email' => $wo['config']['siteEmail'],
                        'from_name' => $wo['config']['siteName'],
                        'to_email' => $email,
                        'to_name' => '',
                        'subject' => 'private group invitation request',
                        'charSet' => 'utf-8',
                        'message_body' => $message,
                        'is_html' => true
                    );
                    $send              = Wo_SendMessage($send_message_data);
                    if ($send) {
                        $notification_data = array(
                            'recipient_id' => $wo['user_data']['user_id'],
                            'notifier_id' => $wo['user']['id'],
                            'private_id' => $wo['group_data']['id'],
                            'type' => 'private_group_invited',
                            'url' => 'index.php?link1=join-private-group&token=' . $wo['token'] . '&ref=' . $wo['group_data']['group_name']
                        );
                        Wo_RegisterNotification($notification_data);

                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . $wo['lang']['email_sent']
                        );
                    } else {
                        $errors[] = $error_icon . $wo['lang']['processing_error'];
                    }
                }
            }
        }

        header("Content-type: application/json");
        if (!empty($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'join_group') {
        if (empty($_POST['group_id']) || empty($_POST['job_category']) || Wo_CheckSession($hash_id) === false) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (empty($_POST['payment_id'])) {
                $errors[] = $error_icon . $wo['lang']['joining_error'];
            }
        }

        if (empty($errors)) {
            $group_id = $_POST['group_id'];
            $user_id = $wo['user']['id'];
            $job_category = $_POST['job_category'];
            $query = mysqli_query($sqlConnect, " INSERT INTO " . T_PRIVATE_MEMBERS . " (`user_id`,`group_id`,`category`,`time`) VALUES ({$user_id},{$group_id},{$job_category}," . time() . ")");
            if (!empty($query)) {
                mysqli_query($sqlConnect, " UPDATE " . T_PRIVATE_ALLOWED_CATEGORY . " SET active = '1' WHERE `group_id` = {$group_id} AND `jobcategory_id` = '{$job_category}' ");
                mysqli_query($sqlConnect, " UPDATE " . T_PRIVATE_INVITES . " SET active = '1' WHERE `group_id` = {$group_id} AND `recipient_id` = '{$user_id}' ");

                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['group_joined']
                );
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'leave_group') {
        if (!empty($_GET['group_id']) && Wo_CheckSession($hash_id) === false) {
            $group_id = $_GET['group_id'];
            $user_id = $wo['user']['id'];

            $data = array();
            $member = Wo_GetPrivateGroupMember($group_id);
            if (!empty($member)) {
                mysqli_query($sqlConnect, " UPDATE " . T_PRIVATE_ALLOWED_CATEGORY . " SET active = '0' WHERE `group_id` = {$group_id} AND `jobcategory_id` = '{$job_category}' ");
                $query = mysqli_query($sqlConnect, " DELETE FROM " . T_PRIVATE_MEMBERS . " WHERE `group_id` = {$group_id} AND `user_id` = '{$user_id}' ");

                if ($query) {
                    $data = array(
                        'status' => 200,
                        'url' => $wo['config']['site_url']
                    );
                }
            }

            header("Content-type: application/json");
            if (isset($errors)) {
                echo json_encode(array(
                    'errors' => $errors
                ));
            } else {
                echo json_encode($data);
            }
            exit();
        }
    }

    if ($s == 'search_for') {
        $data_info = array();
        $data['status'] = 400;
        if (!empty($_GET['name']) && !empty($_GET['type'])) {
            $data_info = Wo_SearchForMembers(Wo_Secure($_GET['name']), Wo_Secure($_GET['type']));
        }

        if (!empty($data_info)) {
            foreach ($data_info as $wo['result']) {
                $data['html'] .= Wo_LoadPage('private_group/search-result');
            }
        } else {
            $data['html'] = '<li>' .  $wo['lang']['no_users_found'] . '</li>';
        }

        $data['status'] = 200;

        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'send_referral') {
        if (empty($_POST['referral_to']) || empty($_POST['referral_type']) || empty($_POST['referral']) || empty($_POST['telephone']) || empty($_POST['email']) || empty(Wo_Secure($_POST['address']))) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (empty($_POST['user_id'])) {
                $errors[] = $error_icon . $wo['lang']['please_choose_user'];
            }

            if (!preg_match('/^\+?\d+$/', $_POST['telephone'])) {
                $errors[] = $error_icon . $wo['lang']['worng_phone_number'];
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = $error_icon . $wo['lang']['email_invalid_characters'];
            }
        }
        if (empty($errors)) {
            $call = isset($_POST['call']) ? 1 : 0;
            $card = isset($_POST['card']) ? 1 : 0;

            $re_data = array(
                'sender_id' => Wo_Secure($wo['user']['user_id']),
                'recipient_id' => Wo_Secure($_POST['user_id']),
                'referral_type' => Wo_Secure($_POST['referral_type']),
                'referral' => Wo_Secure($_POST['referral']),
                'telephone' => Wo_Secure($_POST['telephone']),
                'email' => Wo_Secure($_POST['email']),
                'address' => Wo_Secure($_POST['address']),
                'call' => Wo_Secure($_POST['call']),
                'card' => Wo_Secure($_POST['card']),
                'comments' => Wo_Secure($_POST['comments']),
                'graph_level' => Wo_Secure($_POST['graph_level']),
                'time' => time()
            );

            $levels = array('1' => 'cold', '2' => 'heat', '3' => 'warm', '4' => 'hot');
            $referral_id = Wo_RegisterReferral($re_data);
            if (!empty($referral_id)) {
                $wo['referral_date'] = $re_data;
                $wo['sender_data'] = Wo_UserData($wo['user']['user_id']);
                $wo['recipient_data'] = Wo_UserData($_POST['user_id']);
                $wo['rating'] = array_search($_POST['graph_level'], $levels);
                $wo['group_data'] = Wo_GetUserPrivateGroup($_POST['user_id']);

                $email             = Wo_Secure($wo['recipient_data']['email']);
                $message           = Wo_LoadPage('emails/referral-notice');

                $send_message_data = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $email,
                    'to_name' => '',
                    'subject' => 'referral notice',
                    'charSet' => 'utf-8',
                    'message_body' => $message,
                    'is_html' => true
                );
                Wo_SendMessage($send_message_data);

                $notification_data = array(
                    'recipient_id' => $wo['recipient_data']['user_id'],
                    'notifier_id' => $wo['user']['id'],
                    'private_id' => $wo['group_data']['id'],
                    'type' => 'send_referral_notice',
                    'url' => 'index.php?link1=timeline&u=' . $wo['group_data']['group_name']
                );
                Wo_RegisterNotification($notification_data);

                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['referral_sent']
                );
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'send_thanks') {
        if (empty($_POST['thanks_to']) || empty($_POST['appreciation_value']) || empty($_POST['thanks_type']) || empty($_POST['referral_type'])) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (empty($_POST['user_id'])) {
                $errors[] = $error_icon . $wo['lang']['please_choose_user'];
            }
        }
        if (empty($errors)) {
            $re_data = array(
                'sender_id' => Wo_Secure($wo['user']['user_id']),
                'recipient_id' => Wo_Secure($_POST['user_id']),
                'thanks_type' => Wo_Secure($_POST['thanks_type']),
                'referral_type' => Wo_Secure($_POST['referral_type']),
                'value' => Wo_Secure($_POST['appreciation_value']),
                'comments' => Wo_Secure($_POST['comments']),
                'time' => time()
            );

            $thanks_id = Wo_RegisterThanks($re_data);
            if (!empty($thanks_id)) {
                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['thanks_sent']
                );
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'resend_referral') {
        if (empty($_POST['forward_to']) || empty($_POST['referral_id'])) {
            $errors[] = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (empty($_POST['user_id'])) {
                $errors[] = $error_icon . $wo['lang']['please_choose_user'];
            }
        }
        if (empty($errors)) {
            $wo['referral_date'] = Wo_GetReferral($_POST['referral_id']);

            $re_data = array(
                'sender_id' => Wo_Secure($wo['user']['user_id']),
                'recipient_id' => Wo_Secure($_POST['user_id']),
                'referral_type' => $wo['referral_date']['referral_type'],
                'referral' => $wo['referral_date']['referral'],
                'telephone' => $wo['referral_date']['telephone'],
                'email' => $wo['referral_date']['email'],
                'address' => $wo['referral_date']['address'],
                'call' => $wo['referral_date']['call'],
                'card' => $wo['referral_date']['card'],
                'comments' => $wo['referral_date']['comments'],
                'graph_level' => $wo['referral_date']['graph_level'],
                'time' => time()
            );

            $levels = array('1' => 'cold', '2' => 'heat', '3' => 'warm', '4' => 'hot');
            $referral_id = Wo_RegisterReferral($re_data);
            if (!empty($referral_id)) {
                $wo['sender_data'] = Wo_UserData($wo['user']['user_id']);
                $wo['recipient_data'] = Wo_UserData($_POST['user_id']);
                $wo['rating'] = array_search($wo['referral_date']['graph_level'], $levels);
                $wo['group_data'] = Wo_GetUserPrivateGroup($_POST['user_id']);

                $email             = Wo_Secure($wo['recipient_data']['email']);
                $message           = Wo_LoadPage('emails/referral-notice');

                $send_message_data = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $email,
                    'to_name' => '',
                    'subject' => 'referral notice',
                    'charSet' => 'utf-8',
                    'message_body' => $message,
                    'is_html' => true
                );
                Wo_SendMessage($send_message_data);

                $notification_data = array(
                    'recipient_id' => $wo['recipient_data']['user_id'],
                    'notifier_id' => $wo['user']['id'],
                    'private_id' => $wo['group_data']['id'],
                    'type' => 'send_referral_notice',
                    'url' => 'index.php?link1=timeline&u=' . $wo['group_data']['group_name']
                );
                Wo_RegisterNotification($notification_data);

                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['referral_sent']
                );
            }
        }
        header("Content-type: application/json");
        if (isset($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }

    if ($s == 'suggest_categories') {
        $data = array(
            'status' => 400,
            'message' => $error . $wo['lang']['please_check_details']
        );

        if (!empty($_POST['group_id']) && !empty($_POST['category_id']) && !empty($_POST['suggestions'])) {
            $suggestions = array_map('trim', explode(';', $_POST['suggestions']));

            foreach ($suggestions as $suggestion) {
                $re_data = array(
                    'user_id' => Wo_Secure($wo['user']['user_id']),
                    'group_id' => Wo_Secure($_POST['group_id']),
                    'category_id' => Wo_Secure($_POST['category_id']),
                    'suggestion' => Wo_Secure($suggestion),
                    'time' => time()
                );

                Wo_RegisterSuggestions($re_data);
            }
            $data = array(
                'status' => 200,
                'message' => $success_icon . $wo['lang']['suggestions_registered']
            );
        }

        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

    if ($s == 'change_referral_status') {
        $data['status'] = 400;
        if (!empty($_POST['type']) && !empty($_POST['referral_id'])) {
            $type = $_POST['type'];
            $types_array = array(
                'accept',
                'reject',
            );
            if (in_array($type, $types_array)) {
                $status = ($type == 'accept') ? 1 : 2;
                $status = Wo_Secure($status);
                $referral_id = Wo_Secure($_POST['referral_id']);

                $query = mysqli_query($sqlConnect, " UPDATE " . T_PRIVATE_REFERRALS . " SET status = {$status} WHERE `id` = {$referral_id} ");

                if (!empty($query)) {
                    $data['status'] = 200;
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
