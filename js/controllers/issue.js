/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Issue Controller */
(function() {
	angular.module('issueDetailCtrl', [])
	.controller(
		'issueDetailCtrl',
		['$scope', '$sce', '$routeParams', 'OC', 'Request',
		function($scope, $sce, $routeParams, OC, Request
	) {

		console.log('routeParams', $routeParams);
		$scope.initialized = false;
		Request.getIssue($routeParams.org, $routeParams.repo, $routeParams.issue)
		.then(function(issue) {
			console.log('issue', issue);
			var date = new Date(issue.created_at);
			issue.reldate = relative_modified_date(date/1000);
			issue.isodate = date.toISOString();
			issue.date = date.toLocaleDateString();
			issue.body = $sce.trustAsHtml(issue.body_html);
			$scope.org = $routeParams.org;
			$scope.repo = $routeParams.repo;
			$scope.issue = issue;
			$scope.initialized = true;
		}, function(response) {
			// TODO: call returned an error
			console.warn(response);
		});
	}]);
}).call(this);
