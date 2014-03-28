/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OC.Issues = OC.Issues || {};

(function(window, $, OC) {
	'use strict';

	var JSONResponse = function(response, jqXHR) {
		this.getAllResponseHeaders = jqXHR.getAllResponseHeaders;
		this.getResponseHeader = jqXHR.getResponseHeader;
		this.statusCode = jqXHR.status;
		this.error = false;
		// 204 == No content
		// 304 == Not modified
		if(!response) {
			if([204, 304].indexOf(this.statusCode) === -1) {
				console.log('jqXHR', jqXHR);
				this.error = true;
				this.message = jqXHR.statusText;
			}
		} else {
			// We need to allow for both the 'old' success/error status property
			// with the body in the data property, and the newer where we rely
			// on the status code, and the entire body is used.
			if(response.status === 'error'|| this.statusCode >= 400) {
				this.error = true;
				if(this.statusCode < 500) {
					this.message = (response.data && response.data.message)
						? response.data.message
						: response;
				} else {
					this.message = t('issues', 'Server error! Please inform system administator');
				}
			} else {
				this.data = response;
			}
		}
	};

	/**
	* An object for communicating with the backend
	*
	* All methods returns a jQuery.Deferred object which resolves
	* to either the requested response or an error object:
	* {
	*	error: true,
	*	message: The error message
	* }
	*
	* @param string user The user to query for. Defaults to current user
	*/
	var Requester = function(user) {
		this.user = user ? user : OC.currentUser;
	};

	/**
	 * When the response isn't returned from requestRoute(), you can
	 * wrap it in a JSONResponse so that it's parsable by other objects.
	 *
	 * @param object response The body of the response
	 * @param XMLHTTPRequest http://api.jquery.com/jQuery.ajax/#jqXHR
	 */
	Requester.prototype.formatResponse = function(response, jqXHR) {
		return new JSONResponse(response, jqXHR);
	};

	Requester.prototype.writeClientConfig = function(id, secret) {
		return this.requestRoute(
			'github/repos/{org}',
			'POST',
			{},
			{client_id: id, client_secret: secret}
		);
	};

	Requester.prototype.getRepos = function(org) {
		return this.requestRoute(
			'github/repos/{org}',
			'GET',
			{org: org}
		);
	};

	Requester.prototype.getIssues = function(org, repo, params) {
		return this.requestRoute(
			'github/repos/{org}/{repo}',
			'GET',
			{org: org, repo: repo},
			params
		);
	};

	Requester.prototype.getIssue = function(org, repo, number, params) {
		return this.requestRoute(
			'github/repos/{org}/{repo}/{number}',
			'GET',
			{org: org, repo: repo, number: number},
			params
		);
	};

	Requester.prototype.requestRoute = function(route, type, routeParams, params, additionalHeaders) {
		var isJSON = (typeof params === 'string');
		var contentType = isJSON
			? (type === 'PATCH' ? 'application/json-merge-patch' : 'application/json')
			: 'application/x-www-form-urlencoded';
		var processData = !isJSON;
		contentType += '; charset=UTF-8';
		var url = OC.generateUrl('apps/issues/' + route, routeParams);
		var headers = {
			Accept : 'application/json; charset=utf-8'
		};
		if(typeof additionalHeaders === 'object') {
			headers = $.extend(headers, additionalHeaders);
		}
		var ajaxParams = {
			type: type,
			url: url,
			dataType: 'json',
			headers: headers,
			contentType: contentType,
			processData: processData,
			data: params
		};

		var defer = $.Deferred();

		var jqxhr = $.ajax(ajaxParams)
			.done(function(response, textStatus, jqXHR) {
				defer.resolve(new JSONResponse(response, jqXHR));
			})
			.fail(function(jqXHR/*, textStatus, error*/) {
				console.log(jqXHR);
				var response = jqXHR.responseText ? $.parseJSON(jqXHR.responseText) : null;
				console.log('response', response);
				defer.reject(new JSONResponse(response, jqXHR));
			});

		return defer.promise();
	};

	OC.Issues.Requester = Requester;

})(window, jQuery, OC);
