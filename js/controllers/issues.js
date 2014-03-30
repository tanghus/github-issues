/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Issue List Controller */
var issuesCtrl = angular.module('IssuesCtrl', []);

issuesCtrl.controller(
	'IssuesCtrl', ['$scope', '$location', '$routeParams', 'OC', 'Request',
	function($scope, $location, $routeParams, OC, Request
) {

	console.log('routeParams', $routeParams);
	console.log('page', $routeParams.page || 1);
	$scope.initialized = false;
	$scope.page = $routeParams.page || 1;
	var params = {page: $scope.page};
	Request.getIssues($routeParams.org, $routeParams.repo, params)
	.then(function(response) {
		// call was successful
		$scope.org = $routeParams.org;
		$scope.repo = $routeParams.repo;
		var issues = [];
		angular.forEach(response, function(issue) {
			var date = new Date(issue.created_at);
			issue.reldate = relative_modified_date(date/1000);
			issue.isodate = date.toISOString();
			issue.date = date.toLocaleDateString();
			issues.push(issue);
		});
		$scope.issues = issues;
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
