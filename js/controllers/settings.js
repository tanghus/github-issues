/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
'use strict';

/* Settings Controller */
(function() {
	angular.module('IssueControllers')
	.controller('SettingsCtrl', ['$scope', '$document', '$element', 'Request', 'OC',
	function($scope, $document, $element, Request, OC) {

		console.log('$element', $element);
		// Whether the settings dialog is open
		$scope.isOpen = false;
		$scope.isUpdating = false;

		// The settingsContainer must close when clicked outside of it.
		$scope.slideDown = function (event) {
			$scope.isOpen = false;
			$scope.isUpdating = false;
			// Click was out of scope
			$scope.$apply();
		};

		$scope.toggleOpen = function() {
			$scope.isOpen = !$scope.isOpen;
		};

		$scope.authenticate = function() {
			Request.authenticate($scope.user)
			.then(function(response) {
				// FIXME: Shouldn't rely on text status
				if (response.status === 'success') {
					$scope.isAuthenticated = true;
					$scope.isUpdating = false;
					$scope.user = response.user;
				} else {
					console.warn(response);
					$scope.isAuthenticated = false;
				}
			}, function(response) {
				console.warn(response);
				$scope.isAuthenticated = false;
			});

		};

		$scope.unAuthenticate = function() {
			Request.unAuthenticate()
			.then(function(response) {
				$scope.isAuthenticated = false;
				$scope.user = {};
			}, function(response) {
				console.warn(response);
			});

		};

	}])
	.service('SettingsLoader', function($rootScope, Request, OC) {
		return function() {
			console.log('SettingsLoader');

			Request.getUser()
			.then(function(response) {
				// FIXME: Shouldn't rely on text status
				if (response.status === 'success') {
					$rootScope.isAuthenticated = true;
					$rootScope.user = response.user;
				} else {
					console.warn(response);
					$rootScope.isAuthenticated = false;
				}
			}, function(response) {
				console.warn(response);
				$rootScope.isAuthenticated = false;
			});
		};
	});
}).call(this);
