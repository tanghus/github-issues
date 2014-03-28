/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OC.Issues = OC.Issues || {};


(function(window, $, OC) {
	'use strict';

	var Issue = function(
		requester,
		org,
		repo,
		data,
		$listTemplate,
		$fullTemplate
	) {
		var self = this;
		this.requester = requester;
		this.org = org;
		this.repo = repo;
		this.data = data;
		this.$listTemplate = $listTemplate;
		this.$fullTemplate = $fullTemplate;
		//console.log('Issue data', this.data)
		$(document).bind('status:issues:loading', function(e, data) {
			if (data.status === true) {
				self.close();
			}
		});

	};

	Issue.prototype.getTitle = function() {
		return this.data.title;
	};

	Issue.prototype.getBody = function(html) {
		return html ? this.data.body_html : this.data.body;
	};

	Issue.prototype.getNumber = function() {
		return this.data.number;
	};

	Issue.prototype.getAuthor = function() {
		return this.data.user;
	};

	Issue.prototype.getNumber = function() {
		return this.data.number;
	};

	Issue.prototype.getUrl = function() {
		return this.data.html_url;
	};

	Issue.prototype.getUrl = function() {
		return this.data.html_url;
	};

	Issue.prototype.getState = function() {
		return this.data.state;
	};

	Issue.prototype.getDate = function() {
		return new Date(this.data.created_at);
	};

	Issue.prototype.close = function() {
		if (this.$fullElement) {
			console.log('Issue.close');
			this.$fullElement.remove();
			this.$fullElement = null;
		}

	};

	Issue.prototype.getMetadata = function() {
		var meta = {
			title: this.getTitle(),
			number: this.getNumber(),
			url: this.getUrl(),
			state: this.getState(),
			reldate: relative_modified_date(this.getDate()/1000),
			date: this.getDate().toLocaleDateString(),
			isodate: this.getDate().toISOString(),
			author: this.getAuthor().login,
			authorUrl: this.getAuthor().html_url
		};

		return meta;
	};

	Issue.prototype.renderListItem = function() {
		var self = this;
		// TODO: Format date using the locale set in ownCloud
		// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toLocaleDateString
		this.$listElement = this.$listTemplate.octemplate(this.getMetadata()).data('obj', this);
		this.$listElement.on('click', function(e) {
			if ($(e.target).is('a')) {
				return;
			}
			self.renderFullItem();
		});
		return this.$listElement;
	};

	Issue.prototype.renderFullItem = function() {
		$(document).triggerHandler('status:issue:open', {
			status: true
		});

		this.close();

		console.log('Issue.renderFullItem', this.data);
		var self = this;
		// TODO: Format date using the locale set in ownCloud
		// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toLocaleDateString
		var data = this.getMetadata();
		this.$fullElement = this.$fullTemplate.octemplate(data).data('obj', this);
		this.$fullElement.find('.body').html(data.body = this.getBody(true));

		this.$fullElement.prependTo($('#app-content'));
	};

	OC.Issues.Repo.Issue = Issue;

})(window, jQuery, OC);
