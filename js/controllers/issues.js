/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Issue List Controller */
(function() {
	angular.module('IssueControllers')
	.controller(
		'IssuesCtrl', ['$scope', '$location', '$routeParams', 'Request',
		function($scope, $location, $routeParams, Request
	) {

		console.log('routeParams', $routeParams);
		console.log('page', $routeParams.page || 1);
		$scope.initialized = false;
		console.log('scope', $scope.navigation);
		$scope.page = $routeParams.page || 1;
		var params = {page: $scope.page};
		Request.getIssues($routeParams.org, $routeParams.repo, params)
		.then(function(response) {
			// call was successful
			$scope.org = $routeParams.org;
			$scope.repo = $routeParams.repo;
			var issues = [];
			angular.forEach(response.issues, function(issue) {
				var date = new Date(issue.created_at);
				issue.reldate = relative_modified_date(date/1000);
				issue.isodate = date.toISOString();
				issue.date = date.toLocaleDateString();
				issues.push(issue);
			});
			$scope.issues = issues;
			$scope.navigation = response.navigation;
			$scope.navigation.first = $scope.navigation.first || 1;
			$scope.navigation.prev = $scope.navigation.prev || parseInt($scope.navigation.page) - 1;
			$scope.navigation.next = $scope.navigation.next || parseInt($scope.navigation.page) + 1;
			$scope.initialized = true;
		}, function(response) {
			// TODO: call returned an error
			$scope.issues = response;
		});

		$scope.nextPage = function() {
			$scope.page = parseInt($scope.page) + 1;
			console.log('gotoPage', $scope.page);
			$location.search({page: $scope.page});
		}

		$scope.prevPage = function() {
			$scope.page = parseInt($scope.page) - 1;
			console.log('gotoPage', $scope.page);
			$location.search({page: $scope.page});
		}
	}]);
}).call(this);
