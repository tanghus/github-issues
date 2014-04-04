<?php
/**
 * @author Thomas Tanghus
 * @copyright 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
namespace OCA\Issues;

use OCA\Issues\Dispatcher;

//define the routes
$this->create('issues_index', '')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('PageController', 'index');
		}
	);

$this->create('issues_github_user', 'github/user')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'getUser');
		}
	);

$this->create('issues_github_authenticate', 'github/authenticate')
	->post()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'authenticate');
		}
	)
	->requirements(array('user', 'password'));

$this->create('issues_github_remove_authentication', 'github/authenticate')
	->delete()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'unAuthenticate');
		}
	);

$this->create('issues_github_repos', 'github/repos/{org}')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'getRepos');
		}
	)
	->requirements(array('org',));

$this->create('issues_github_issues', 'github/repos/{org}/{repo}')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'getIssues');
		}
	)
	->requirements(array('org', 'repo'));

$this->create('issues_github_issue', 'github/repos/{org}/{repo}/{number}')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'getIssue');
		}
	)
	->requirements(array('org', 'repo', 'number'));

$this->create('issues_github_issue_comments', 'github/repos/{org}/{repo}/{number}/comments')
	->get()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'getIssueComments');
		}
	)
	->requirements(array('org', 'repo', 'number'));

$this->create('issues_github_clientconfig_write', 'github/clientconfig/write')
	->post()
	->action(
		function($params) {
			session_write_close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GithubController', 'writeClientConfig');
		}
	);

