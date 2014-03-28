'use strict';

/* App Module */

var issues = angular.module('Issues', [
	'ngRoute',
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
		otherwise({
			redirectTo: OC.generateUrl('apps/issues')
		});
	}
]);
