'use strict';

/* App Module */

(function() {
	angular.module('Issues', [
		'ngRoute',
		'IntroCtrl',
		'ReposCtrl',
		'IssuesCtrl',
		'issueDetailCtrl',
		'CommentsCtrl',
		'SettingsCtrl'
		//'phonecatFilters',
		//'phonecatServices'
	]).config(['$routeProvider',
		function($routeProvider) {
			$routeProvider.
			when('/:org/:repo', {
				templateUrl: 'templates/issues.php',
				controller: 'IssuesCtrl'
			}).
			/*when('/:org/:repo/issues/:issue', {
				// Hack because I don't do rexexps
				redirectTo: '/:org/:repo/:issue'
			}).*/
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
}).call(this);
