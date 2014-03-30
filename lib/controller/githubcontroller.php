<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Issues\Controller;

use OCA\Issues\Controller,
	OCP\AppFramework\IAppContainer,
	OCP\AppFramework\Http\JSONResponse,
	OCP\AppFramework\Http,
	Github\Client;


/**
 * Controller class for groups/categories
 * TODO: Check return values for all Github calls
 */
class GithubController extends Controller {

	/**
	 * @var \Github\Client
	 */
	protected $github;

	/**
	 * @var \Parsedown
	 */
	protected $parsedown;

	/**
	 * Github client config. False if not configured.
	 *
	 * @var array|bool
	 */
	private $ghClientConfig;

	protected $ghClientConfigFile; // = \OC::$SERVERROOT . '/config/gh_client_config.json';

	public function __construct(IAppContainer $container) {
		parent::__construct($container);
		$vendorDir = realpath(__DIR__ . '/../../3rdparty/vendor');
		//\OCP\Util::writeLog('issues', __METHOD__.' vendorDir: ' . $vendorDir, \OCP\Util::DEBUG);
		// TODO: Check that knplabs/github-api is installed
		require_once $vendorDir . '/autoload.php';

		$this->parsedown = new \Parsedown();

		$view = \OCP\Files::getStorage('issues');
		if(!$view->file_exists('cache')) {
			$view->mkdir('cache');
		}
		$tmpDir = $view->getLocalFile('/cache');

		$this->github = new Client(
			new \Github\HttpClient\CachedHttpClient(array('cache_dir' => $tmpDir))
		);

		$this->ghClientConfig = $this->readClientConfig();
		if($this->ghClientConfig !== false) {
		} else {
			$this->github->authenticate('tanghus', 'hjkiu45d', Client::AUTH_HTTP_PASSWORD);
		}
		$this->github->getHttpClient()->setOption('user_agent', 'ownCloud Issues');

	}

	/**
	 * Read any configured Github client configuration
	 */
	private function readClientConfig() {
		if (is_file($this->ghClientConfigFile)) {
			return json_decode(file_get_contents($this->ghClientConfigFile), true);
		}

		return false;
	}

	public function writeClientConfig() {
		$id = $this->request->post['id'];
		$secret = $this->request->post['secret'];

		$config = array('client_id' => $id, 'client_secret' => $secret);

		$response = new JSONResponse();

		if(file_put_contents(self::$ghClientConfigFile, json_encode($config))) {
			chmod(self::$ghClientConfigFile, 0600);
		} else {
			$response->setStatus(Http::STATUS_FORBIDDEN);
			$response->setData(
				array(
					'status' => 'error',
					'message' => 'Web server does not have permission to write to '
						. self::$ghClientConfigFile
				)
			);
		}

		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function getRepos() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();
		$org = $params['org'];

		$repos = array();

		$tmpRepos = $this->github->api('repos')->org($org);

		$headers = $this->github->getHttpClient()->getLastResponse()->getHeaders();

		foreach ($headers as $name => $value) {
			//\OCP\Util::writeLog('issues', __METHOD__.' header: ' . $name . ': ' . print_r($value, true), \OCP\Util::DEBUG);
		}

		//\OCP\Util::writeLog('issues', __METHOD__.' RateLimit: ' . $this->github->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Limit'), \OCP\Util::DEBUG);
		//\OCP\Util::writeLog('issues', __METHOD__.' Remaining: ' . $this->github->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining'), \OCP\Util::DEBUG);
		//\OCP\Util::writeLog('issues', __METHOD__.' Link: ' . print_r($this->github->getHttpClient()->getLastResponse()->getHeader('link')->getLink('next'), true), \OCP\Util::DEBUG);

		foreach ($tmpRepos as $repo) {
			if ($repo['has_issues'] === true) {
				$repos[] = $repo;
			}
		}

		\OCP\Util::writeLog('issues', __METHOD__.' num repos: ' . count($repos), \OCP\Util::DEBUG);

		$response->setData($repos);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function getIssues() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$org = $params['org'];
		$repo = $params['repo'];
		$page = isset($this->request['page'])
			? $this->request['page']
			: '1';
		$issues = array();

		\OCP\Util::writeLog('issues', __METHOD__.' page: ' . $page, \OCP\Util::DEBUG);

		try {
			$tmpIssues = $this->github->api('issues')->all(
				$org,
				$repo,
				array('sort' => 'created', 'state' => 'all', 'page' => $page)
			);

			foreach ($tmpIssues as $issue) {
				if (isset($issue['pull_request'])) {
					continue;
				}
				$issue['body_html'] = $this->parsedown->parse($issue['body']);
				$issues[] = $issue;
			}
			/*
			$headers = $this->github->getHttpClient()->getLastResponse()->getHeaders();

			foreach ($headers as $name => $value) {
				\OCP\Util::writeLog('issues', __METHOD__.' header: ' . $name . ': ' . print_r($value, true), \OCP\Util::DEBUG);
			}*/
			$links = $this->github->getHttpClient()->getLastResponse()->getHeader('link');
			if ($links) {
				\OCP\Util::writeLog('issues', __METHOD__.' prev: ' . print_r($links->getLink('prev'), true), \OCP\Util::DEBUG);
				\OCP\Util::writeLog('issues', __METHOD__.' next: ' . print_r($links->getLink('next'), true), \OCP\Util::DEBUG);
				\OCP\Util::writeLog('issues', __METHOD__.' first: ' . print_r($links->getLink('first'), true), \OCP\Util::DEBUG);
				\OCP\Util::writeLog('issues', __METHOD__.' last: ' . print_r($links->getLink('last'), true), \OCP\Util::DEBUG);
			}
			$response->setData($issues);

			return $response;
		} catch (\Github\Exception\RuntimeException $e) {
			$response->setStatus(Http::STATUS_METHOD_NOT_ALLOWED);
			$response->setData(array('status' => 'error', 'message' => $e->getMessage()));
			return $response;
		}

		//\OCP\Util::writeLog('issues', __METHOD__.' content: ' . print_r($issues, true), \OCP\Util::DEBUG);
	}

	/**
	 * @NoAdminRequired
	 */
	public function getIssue() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$org = $params['org'];
		$repo = $params['repo'];
		$number = $params['number'];

		$issue = $this->github->api('issues')->show($org, $repo, $number);

		\OCP\Util::writeLog('issues', __METHOD__.' content: ' . print_r($issue, true), \OCP\Util::DEBUG);
		$response->setData($issue);

		return $response;
	}

}
