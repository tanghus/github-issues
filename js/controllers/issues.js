/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Issue List Controller */
var issuesCtrl = angular.module('IssuesCtrl', []);

issuesCtrl.controller('IssuesCtrl', ['$scope', '$routeParams', 'OC', 'Request', function($scope, $routeParams, OC, Request) {

	console.log('routeParams', $routeParams);
	Request.getIssues($routeParams.org, $routeParams.repo)
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
	}, function(response) {
		// TODO: call returned an error
		$scope.issues = response;
	});

}]);
