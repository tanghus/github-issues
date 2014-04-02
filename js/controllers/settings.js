/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Settings Controller */
(function() {
	angular.module('SettingsCtrl', [])
	.controller('SettingsCtrl', ['$scope', 'OC', 'Request', function($scope, OC, Request) {

		$scope.initialized = false;
		/*Request.getRepos('owncloud')
		.then(function(response) {
			// call was successful
			$scope.repos = response;
			$scope.initialized = true;
		}, function(response) {
			// TODO: call returned an error
			$scope.repos = response;
		});*/

	}]);
}).call(this);
