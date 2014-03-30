'use strict';

/* App Module */

var issues = angular.module('Issues', [
	'ngRoute',
	'IntroCtrl',
	'ReposCtrl',
	'IssuesCtrl',
	'issueDetailCtrl'
	//'phonecatFilters',
	//'phonecatServices'
]);

issues.config(['$routeProvider',
	function($routeProvider) {
		$routeProvider.
		when('/:org/:repo', {
			templateUrl: 'templates/issues.php',
			controller: 'IssuesCtrl'
		}).
		when('/:org/:repo/:issue', {
			templateUrl: 'templates/issue.php',
			controller: 'issueDetailCtrl'
		}).
		when('/:org/:repo/new', {
			templateUrl: 'templates/newissues.php',
			controller: 'NewIssueCtrl'
		}).
		otherwise({
			templateUrl: 'templates/intro.php',
			controller: 'IntroCtrl'
			//redirectTo: '/'
		});
	}
]);
