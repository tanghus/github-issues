/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Comment List Controller */
(function() {
	angular.module('IssueControllers').controller(
		'CommentsCtrl', ['$scope', '$sce', '$location', 'Request',
		function($scope, $sce, $location, Request
	) {

		$scope.initialized = false;
		$scope.loading = false;
		$scope.comments = [];

		$scope.loadComments = function(org, repo, issue) {
			console.log('loadComments:', org, repo, issue);
			$scope.loading = true;
			Request.getComments(org, repo, issue)
			.then(function(response) {
				// call was successful
				var comments = [];
				angular.forEach(response.comments, function(comment) {
					var date = new Date(comment.created_at);
					comment.reldate = relative_modified_date(date/1000);
					comment.isodate = date.toISOString();
					comment.date = date.toLocaleDateString();
					// We have comments and events in the same stream
					if (comment.actor) {
						comment.user = comment.actor;
					}
					if (comment.body_html) {
						comment.body = $sce.trustAsHtml(comment.body_html);
					}
					comments.push(comment);
				});
				$scope.comments = comments;
				$scope.initialized = true;
				$scope.loading = false;
			}, function(response) {
				// TODO: call returned an error
				console.warn(response);
				$scope.loading = false;
				//$scope.issues = response;
			});
		};
	}]);
}).call(this);
