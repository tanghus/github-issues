<?php

/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OC_Util::checkAdminUser();

if($_POST) {
	// CSRF check
	OCP\JSON::callCheck();

	if(isset($_POST['webdav_url'])) {
		OC_CONFIG::setValue('user_webdavauth_url', strip_tags($_POST['webdav_url']));
	}
}

// fill template
$tmpl = new OC_Template( 'user_webdavauth', 'settings');
$tmpl->assign( 'webdav_url', OC_Config::getValue( "user_webdavauth_url" ));

return $tmpl->fetchPage();
