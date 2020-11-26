<?php
if ($wo['loggedin'] == false) {
	header("Location: " . $wo['config']['site_url']);
	exit();
}

if ($wo['config']['private_groups'] == 0) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
	exit();
}

if (isset($_GET['token']) && isset($_GET['ref'])) {
	$group_id            = Wo_PrivateGroupIdFromGroupname($_GET['ref']);
	$wo['group_profile'] = Wo_PrivateGroupData($group_id);
	if (Wo_IsPrivateGroupJoined($group_id, $wo['user']['user_id'])) {
		header("Location: " . Wo_SeoLink('index.php?link1=404'));
		exit();
	}

	if (Wo_PrivateGroupInvitationExists($_GET['token'], $group_id) == true) {
		$wo['invitation_profile'] = Wo_GetPrivateGroupInvitation($_GET['token'], $group_id);
		$wo['allowed_categories'] = Wo_GetAllowedCategories($group_id);
		if (!empty($wo['allowed_categories'])) {
			$wo['description'] = $wo['config']['siteDesc'];
			$wo['keywords']    = $wo['config']['siteKeywords'];
			$wo['page']        = 'join_private_group';
			$wo['title']       = $wo['config']['siteTitle'];
			$wo['content']     = Wo_LoadPage('private_group/join-private-group');
		} else {
			header("Location: " . $wo['config']['site_url']);
			exit();
		}
	} else {
		header("Location: " . $wo['config']['site_url']);
		exit();
	}
} else {
	header("Location: " . $wo['config']['site_url']);
	exit();
}
