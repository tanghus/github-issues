/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OC.Issues = OC.Issues || {
	org:'owncloud',
	init:function() {
		this.$repoList = $('#repos');
		this.$issueList = $('#issues');
		this.$navigation = $('#app-navigation');
		this.$content = $('#app-content');
		this.$issueTemplate = $('#issueTemplate');

		this.requester = new this.Requester();
		this.repos = new this.RepoList(
			this.requester,
			this.org,
			this.$repoList,
			this.$issueList,
			this.$issueTemplate
		);
		this.bindEvents();
		this.loadRepos();
	},
	bindEvents: function() {
		var self = this;

		$(document).bind('status:issue:open', function(e, data) {
			console.log('status:issue:open', data);

			if (data.status === true) {
				self.$issueList.hide();
			} else {
				self.$issueList.show();
			}
		});

		$(document).bind('status:issues:loading', function(e, data) {
			console.log('status:issues:loading', data);

			if (data.status === true) {
				self.$issueList.hide();
				self.$content.addClass('loading');
			} else {
				self.$issueList.show();
				self.$content.removeClass('loading');
			}
		});

	},
	loadRepos: function() {
		var self = this;
		$.when(this.repos.loadRepos())
			.then(function(response) {
			if(!response.error) {
				console.log('Num repos', response.length);
			} else {
				console.warn('response', response);
			}
		})
		.fail(function(response) {
			console.warn(response.message);
		}).always(function() {
			self.$navigation.removeClass('loading');
			self.$content.removeClass('loading');
		});
	}
};

$(document).ready(function() {

	OC.Issues.init();

});

