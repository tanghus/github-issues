/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Issue Controller */
var issueCtrl = angular.module('issueDetailCtrl', []);

issueCtrl.controller(
	'issueDetailCtrl',
	['$scope', '$sce', '$routeParams', 'OC', 'Request', 'marked',
	function($scope, $sce, $routeParams, OC, Request, marked
) {

	console.log('routeParams', $routeParams);
	$scope.initialized = false;
	Request.getIssue($routeParams.org, $routeParams.repo, $routeParams.issue)
	.then(function(issue) {
		// call was successful
		console.log('issue', issue);
		marked.setOptions({
		renderer: new marked.Renderer(),
		gfm: true,
		tables: true,
		breaks: true,
		pedantic: false,
		sanitize: true,
		smartLists: true,
		smartypants: false
		});
		var date = new Date(issue.created_at);
		issue.reldate = relative_modified_date(date/1000);
		issue.isodate = date.toISOString();
		issue.date = date.toLocaleDateString();
		issue.body = $sce.trustAsHtml(marked(issue.body));
		$scope.org = $routeParams.org;
		$scope.repo = $routeParams.repo;
		$scope.issue = issue;
		$scope.initialized = true;
	}, function(issue) {
		// TODO: call returned an error
		$scope.issue = issue;
	});
}]);
