<?php
/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

//\OCP\App::registerAdmin('issues', 'settings');
\OCP\Util::addscript('issues', 'loader');
$vendorDir = realpath(__DIR__ . '/../3rdparty/vendor');
\OCP\Util::writeLog('issues', __METHOD__.' vendorDir: ' . $vendorDir, \OCP\Util::DEBUG);
// TODO: Check that knplabs/github-api is installed
require_once $vendorDir . '/autoload.php';
if (!class_exists('Crypt_AES')) {
	\OCP\Util::writeLog('issues', __METHOD__.' Crypt_AES not loaded ', \OCP\Util::DEBUG);
}