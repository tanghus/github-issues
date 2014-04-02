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
	 * @var \OC\Files\View
	 */
	private $storage;

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

	protected $ghClientConfigFile = 'gh_client_config.json';

	public function __construct(IAppContainer $container) {
		parent::__construct($container);
		$vendorDir = realpath(__DIR__ . '/../../3rdparty/vendor');
		//\OCP\Util::writeLog('issues', __METHOD__.' vendorDir: ' . $vendorDir, \OCP\Util::DEBUG);
		// TODO: Check that knplabs/github-api is installed
		require_once $vendorDir . '/autoload.php';

		$this->parsedown = new \Parsedown();

		$this->storage = \OCP\Files::getStorage('issues');
		if(!$this->storage->file_exists('cache')) {
			$this->storage->mkdir('cache');
		}
		$tmpDir = $this->storage->getLocalFile('/cache');

		$this->github = new Client(
			new \Github\HttpClient\CachedHttpClient(array('cache_dir' => $tmpDir))
		);

		$this->ghClientConfig = $this->readClientConfig();
		if(is_array($this->ghClientConfig)) {
			$this->github->authenticate(
				$this->ghClientConfig['user'],
				$this->ghClientConfig['password'], Client::AUTH_HTTP_PASSWORD
			);
		}
		$this->github->getHttpClient()->setOption('user_agent', 'ownCloud Issues');

		$urlGenerator = $this->server->getUrlGenerator();
		$appPath = $urlGenerator->getAbsoluteURL($urlGenerator->linkToRoute('issues_index'));
		\OCP\Util::writeLog('issues', __METHOD__.' appPath: ' . $appPath, \OCP\Util::DEBUG);
	}

	/**
	 * Read any configured Github client configuration
	 */
	private function readClientConfig() {
		if ($this->storage->is_file($this->ghClientConfigFile)) {
			return json_decode($this->storage->file_get_contents($this->ghClientConfigFile), true);
		}

		return false;
	}

	public function writeClientConfig() {
		$id = $this->request->post['id'];
		$secret = $this->request->post['secret'];

		$config = array('client_id' => $id, 'client_secret' => $secret);

		$response = new JSONResponse();

		$options = 0;
		if (defined('JSON_PRETTY_PRINT')) {
			// only for PHP >= 5.4
			$options = JSON_PRETTY_PRINT;
		}
		if ($this->storage->file_put_contents($this->ghClientConfigFile, json_encode($config, $options))) {
			$this->storage->chmod($this->ghClientConfigFile, 0600);
		} else {
			$response->setStatus(Http::STATUS_FORBIDDEN);
			$response->setData(
				array(
					'status' => 'error',
					'message' => 'Web server does not have permission to write to '
						. $this->ghClientConfigFile
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

		$page = 1;
		while ($tmpRepos = $this->github->api('repos')->org($org, array('page' => $page))) {
			\OCP\Util::writeLog('issues', __METHOD__.' page: ' . $page, \OCP\Util::DEBUG);
			\OCP\Util::writeLog('issues', __METHOD__.' batch count: ' . count($tmpRepos), \OCP\Util::DEBUG);
			if (count($tmpRepos) === 0) {
				break;
			}

		$headers = $this->github->getHttpClient()->getLastResponse()->getHeaders();

		foreach ($headers as $name => $value) {
			//\OCP\Util::writeLog('issues', __METHOD__.' header: ' . $name . ': ' . print_r($value, true), \OCP\Util::DEBUG);
		}

		//\OCP\Util::writeLog('issues', __METHOD__.' RateLimit: ' . $this->github->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Limit'), \OCP\Util::DEBUG);
		//\OCP\Util::writeLog('issues', __METHOD__.' Remaining: ' . $this->github->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining'), \OCP\Util::DEBUG);
		//\OCP\Util::writeLog('issues', __METHOD__.' Link: ' . print_r($this->github->getHttpClient()->getLastResponse()->getHeader('link')->getLink('next'), true), \OCP\Util::DEBUG);

		$links = $this->github->getHttpClient()->getLastResponse()->getHeader('link');
		\OCP\Util::writeLog('issues', __METHOD__.' Links: ' . print_r($links, true), \OCP\Util::DEBUG);

		foreach ($tmpRepos as $repo) {
			if ($repo['has_issues'] === true) {
				$repos[] = $repo;
			}
		}
		$page++;
		}
		\OCP\Util::writeLog('issues', __METHOD__.' num repos: ' . count($repos), \OCP\Util::DEBUG);

		$response->setData($repos);

		return $response;
	}

	/**
	 * Get the paging number based on a URL where the
	 * query string contains a 'page' variable.
	 *
	 * @param string $url
	 * @return int|null
	 */
	protected function getPageFromUrl($url) {
		$query = parse_url($url, PHP_URL_QUERY );
		$vars = array();
		parse_str($query, $vars);
		if (isset($vars['page'])) {
			//\OCP\Util::writeLog('issues', __METHOD__.' vars: ' . print_r($vars, true), \OCP\Util::DEBUG);
			return (int)$vars['page'];
		}
	}

	/**
	* Extract the page numbers from the Link header
	*
	* Returns an assosiative array with the keys:
	* prev, next, first and last containing either an
	* integer or null.
	*
	* @param \Guzzle\Http\Message\Header\Link $links
	* @return array
	*/
	protected function extractPages($links) {

		$pages = array(
			'prev' => null,
			'next' => null,
			'first' => null,
			'last' => null
		);

		if (!$links) {
			return $pages;
		}

		foreach (array_keys($pages) as $rel) {
			$link = $links->getLink($rel);

			/*if (!is_array($link)) {
				continue;
			}*/

			$url = $link['url'];
			$pages[$rel] = $this->getPageFromUrl($url);
			/*$query = parse_url($url, PHP_URL_QUERY );
			$vars = array();
			parse_str($query, $vars);
			if (isset($vars['page'])) {
				//\OCP\Util::writeLog('issues', __METHOD__.' vars: ' . print_r($vars, true), \OCP\Util::DEBUG);
				$pages[$rel] = (int)$vars['page'];
			}*/
		}

		return $pages;
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

			$remaining = $this->github->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining');
			\OCP\Util::writeLog('issues', __METHOD__.' Remaining: ' . print_r((string)$remaining, true), \OCP\Util::DEBUG);
			$links = $this->github->getHttpClient()->getLastResponse()->getHeader('link');
			$pages = $this->extractPages($links);
			$navigation = array_merge($this->request->get, $pages);
			$response->setData(array('issues' => $issues, 'navigation' => $navigation));

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
		$mdRenderer = $this->github->api('markdown');
		$issue['body_html'] = $mdRenderer->render($issue['body'], 'gfm', $org . '/' . $repo);

		$patterns = array('https://github.com/' . $org . '/' . $repo . '/issues/');
		$replacements = array($this->appPath . '#/'. $org . '/' . $repo . '/');

		$issue['body_html'] = str_replace($patterns, $replacements, $issue['body_html']);

		\OCP\Util::writeLog('issues', __METHOD__.' content: ' . print_r($issue, true), \OCP\Util::DEBUG);
		$response->setData($issue);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function getIssueComments() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$org = $params['org'];
		$repo = $params['repo'];
		$number = $params['number'];
		$page = 1;
		$mdRenderer = $this->github->api('markdown');
		$comments = array();

		$patterns = array('https://github.com/' . $org . '/' . $repo . '/issues/');
		$replacements = array($this->appPath . '#/'. $org . '/' . $repo . '/');

		while ($tmpComments = $this->github->api('issues')->comments()->all($org, $repo, $number, $page)) {
			foreach ($tmpComments as &$comment) {
				if (count($tmpComments) === 0) {
					break;
				}
				$comment['body_html'] = $mdRenderer->render($comment['body'], 'gfm', $org . '/' . $repo);
				$comment['body_html'] = str_replace($patterns, $replacements, $comment['body_html']);
				$comments[] = $comment;
				$page++;
			}
		}

		$response->setData(array('comments' => $comments));

		return $response;
	}

}
