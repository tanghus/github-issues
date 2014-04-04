/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

(function() {
	angular.module('Issues')
	.factory('Request', ['$http', '$injector', '$q', 'OC',
	function($http, $injector, $q, OC) {
		return {
			getUser: function() {
				return this.requestRoute(
					'github/user',
					'GET'
				);
			},
			authenticate: function(user) {
				return this.requestRoute(
					'github/authenticate',
					'POST',
					{},
					user
				);
			},
			unAuthenticate: function() {
				return this.requestRoute(
					'github/authenticate',
					'DELETE'
				);
			},
			getRepos: function(org) {
				return this.requestRoute(
					'github/repos/{org}',
					'GET',
					{org: org}
				);
			},
			getIssues: function(org, repo, params) {
				return this.requestRoute(
					'github/repos/{org}/{repo}',
					'GET',
					{org: org, repo: repo},
					params
				);
			},
			getIssue: function(org, repo, number, params) {
				return this.requestRoute(
					'github/repos/{org}/{repo}/{number}',
					'GET',
					{org: org, repo: repo, number: number},
					params
				);
			},
			getComments: function(org, repo, issue) {
				return this.requestRoute(
					'github/repos/{org}/{repo}/{number}/comments',
					'GET',
					{org: org, repo: repo, number: issue}
				);
			},
			writeClientConfig: function(id, secret) {
				return this.requestRoute(
					'github/repos/{org}',
					'POST',
					{},
					{client_id: id, client_secret: secret}
				);
			},
			requestRoute: function(route, method, routeParams, params, additionalHeaders) {
				var rScope = $injector.get('$rootScope');
				if(rScope) {
					//console.log('got rootScope');
					//rScope.$broadcast('exception',exception, cause);
				}
				var isJSON = (typeof params === 'string');
				var contentType = isJSON
					? (type === 'PATCH' ? 'application/json-merge-patch' : 'application/json')
					: 'application/x-www-form-urlencoded';
				console.log('contentType', contentType);
				var headers = {
					Accept : 'application/json; charset=utf-8',
					contentType: contentType
				};
				if(typeof additionalHeaders === 'object') {
					headers = $.extend(headers, additionalHeaders);
				}

				var config = {
					method: method,
					url: OC.generateUrl('apps/issues/' + route, routeParams),
					headers: headers,
				};
				if (method === 'GET') {
					config.params = params;
				} else {
					config.data = params;
				}

				var deferred = $q.defer();

				console.log('params', params);
				$http(config)
				.success(function(response, status, headers, config) {
					console.log('status, headers, config', status, headers(), config);
					deferred.resolve(response);
				}).error(function (response, status, headers, config) {
					deferred.reject(response);
				});

				return deferred.promise;
			}
		}
	}]);
}).call(this);
