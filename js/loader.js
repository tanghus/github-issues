/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

$(document).ready(function() {
	// If we're on the help page
	if (window.location.href.indexOf('settings/help') !== -1) {
		// Replace the issue tracker URL with a link to this app
		$('a.button[href^="https://github.com/owncloud/"]')
		.attr('href', OC.generateUrl('/apps/issues/'))
		.attr('target', '_self');
	}
});
