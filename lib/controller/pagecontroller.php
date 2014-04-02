<?php
/**
 * @author Thomas Tanghus
 * @copyright 2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Issues\Controller;

use OCA\Issues\Controller,
	OCP\AppFramework\Http\TemplateResponse;


/**
 * Controller class for main page.
 */
class PageController extends Controller {

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		if (defined('DEBUG') && DEBUG) {
			\OCP\Util::addScript('issues', 'bower_components/angular/angular');
			\OCP\Util::addScript('issues', 'bower_components/angular-route/angular-route');
			\OCP\Util::addScript('issues', 'bower_components/angular-resource/angular-resource');
		} else {
			\OCP\Util::addScript('issues', 'bower_components/angular/angular.min');
			\OCP\Util::addScript('issues', 'bower_components/angular-route/angular-route.min');
			\OCP\Util::addScript('issues', 'bower_components/angular-resource/angular-resource.min');
		}
		\OCP\Util::addScript('issues', 'app');
		\OCP\Util::addScript('issues', 'services/externals');
		\OCP\Util::addScript('issues', 'controllers/intro');
		\OCP\Util::addScript('issues', 'controllers/repos');
		\OCP\Util::addScript('issues', 'controllers/issues');
		\OCP\Util::addScript('issues', 'controllers/issue');
		\OCP\Util::addScript('issues', 'controllers/comments');
		\OCP\Util::addScript('issues', 'controllers/settings');
		\OCP\Util::addScript('issues', 'services/request');
		\OCP\Util::addStyle('issues', 'issues');

		// TODO: Make a HTMLTemplateResponse class
		$response = new TemplateResponse('issues', 'app');
		/*$response->setParams(array(
			'var' => $var,
		));*/

		return $response;
	}
}
