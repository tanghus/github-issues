/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Repository Controller */
(function() {
	angular.module('IssueControllers')
	.controller('ReposCtrl', ['$scope', 'Request', function($scope, Request) {

		//console.log('ReposCtrl');
		$scope.repos = [];
		$scope.org = 'owncloud';
		$scope.initialized = false;
		Request.getRepos('owncloud')
		.then(function(response) {
			// call was successful
			$scope.repos = response;
			$scope.initialized = true;
		}, function(response) {
			// TODO: call returned an error
			$scope.repos = response;
		});

	}]);
}).call(this);
