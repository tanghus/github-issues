/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

// testing controller
describe('Request', function() {
	var OC, oc_requesttoken, $httpBackend, Request, httpResponse,
		org = 'owncloud', repo = 'core';

	beforeEach(function() {
		module('Issues', function($provide) {
			$provide.value('oc_requesttoken', oc_requesttokenMock);
			$provide.value('OC', OCMock);
			//spyOn(OCMock, 'generateUrl').andCallThrough();
		});

		// inject $httpBackend Request service
		inject(function(_$httpBackend_, _Request_) {
			$httpBackend = _$httpBackend_;
			Request = _Request_;

		});
	});

	it('will fetch all repositories', function() {

		httpResponse = { data: [1]};
		$httpBackend.whenGET('/index.php/apps/issues/github/repos/' + org).respond(httpResponse);
		var returnedPromise = Request.getRepos(org);
		var result;

		returnedPromise.then(function(response) {
			result = response;
		});

		$httpBackend.flush();

		expect(result).toEqual(httpResponse);
		//expect(OCMock.generateUrl).toHaveBeenCalledWith('/index.php/apps/issues/github/repos/{org}', {org:org});
	});
});