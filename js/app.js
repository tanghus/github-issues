'use strict';

/* App Module */

(function() {
	angular.module('Issues', [
		'ngRoute',
		'IssueControllers'
		//'phonecatFilters',
		//'phonecatServices'
	])
	.controller('AppCtrl', ['$rootScope', '$document', 'OC', 'SettingsLoader',
	function($rootScope, $document, OC, SettingsLoader) {
		console.dir(SettingsLoader);
		// Contains property 'login' if credentials have been saved
		// and 'password' when user is adding/updating credentials.
		$rootScope.user = {};
		$rootScope.isAuthenticated = false;
		SettingsLoader();
	}])
	.config(['$routeProvider',
		function($routeProvider) {
			$routeProvider.
			when('/:org/:repo', {
				title: 'Repo',
				templateUrl: 'templates/issues.php',
				controller: 'IssuesCtrl'
			}).
			/*when('/:org/:repo/issues/:issue', {
				// Hack because I don't do rexexps
				redirectTo: '/:org/:repo/:issue'
			}).*/
			when('/:org/:repo/:issue', {
				title: 'Issue',
				templateUrl: 'templates/issue.php',
				controller: 'issueDetailCtrl'
			}).
			when('/:org/:repo/new', {
				title: 'New',
				templateUrl: 'templates/newissues.php',
				controller: 'NewIssueCtrl'
			}).
			otherwise({
				title: 'Intro',
				templateUrl: 'templates/intro.php',
				controller: 'IntroCtrl'
				//redirectTo: '/'
			});
		}
	])
	.run(['$location', '$rootScope', '$document',
		function($location, $rootScope, $document) {

			// TODO: Test this
			// http://docs.angularjs.org/api/ng/service/$controller
			//$controller('SettingsCtrl', [/*locals*/]);
			// Update title base on context
			$rootScope.$on('$routeChangeSuccess', function(event, current, previous) {
				if (!$rootScope.originalTitle) {
					$rootScope.originalTitle = $document[0].title;
				}
				var title = '', context = [];
				if (current.keys) {
					angular.forEach(current.keys, function(key) {
						context.push(current.params[key.name]);
					});
				}
				var separator = context.length > 0 ? ' - ' : '';
				title = 'Issues - ' + title + context.join('/') + separator + $rootScope.originalTitle;
				$document[0].title = title;
			});
		}
	]);
}).call(this);
