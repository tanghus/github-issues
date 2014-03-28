<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Issues;

use OCP\AppFramework\App as MainApp,
	OCP\AppFramework\IAppContainer,
	OCA\Issues\Controller\PageController,
	OCA\Issues\Controller\GithubController;

/**
 * This class manages our app actions
 *
 * TODO: Merge with App
 */

class Dispatcher extends MainApp {
	/**
	* @var App
	*/
	protected $app;

	public function __construct($params) {
		parent::__construct('issues', $params);
		$this->container = $this->getContainer();
		$this->registerServices();
	}

	public function registerServices() {
		$this->container->registerService('PageController', function(IAppContainer $container) {
			return new PageController($container);
		});
		$this->container->registerService('GithubController', function(IAppContainer $container) {
			return new GithubController($container);
		});
	}

}
