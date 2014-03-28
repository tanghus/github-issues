/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OC.Issues = OC.Issues || {};


(function(window, $, OC) {
	'use strict';

	/**
	 * Controls access to a repository
	 */
	var Repo = function(
		requester,
		org,
		data,
		$template,
		$issueListTemplate,
		$issueFullTemplate,
		$issueList
	) {
		this.requester = requester;
		this.org = org;
		this.data = data;
		this.$template = $template;
		this.$issueList = $issueList;
		this.$issueListTemplate = $issueListTemplate;
		this.$issueFullTemplate = $issueFullTemplate;
		this.issues = [];
	};

	Repo.prototype.getName = function() {
		return this.data.name;
	};

	Repo.prototype.getDescription = function() {
		return this.data.description;
	};

	Repo.prototype.getOwner = function() {
		return this.org;
	};

	Repo.prototype.setActive = function(state) {
		if (state === true) {
			this.$li.addClass('active');
		} else {
			this.$li.removeClass('active');
		}
	};

	Repo.prototype.isActive = function(state) {
		this.$li.hasClass('active');
	};

	/**
	 * Create an Issue object, save it in internal list and append it's rendered result to the list
	 *
	 * @param object repo The raw data from Github
	 * @return Repo
	 */
	Repo.prototype.insertIssue = function(data) {
		var issue = new OC.Issues.Repo.Issue(
			this.requester,
			this.getOwner(),
			this.getName(),
			data,
			this.$issueListTemplate,
			this.$issueFullTemplate
		);
		var result = issue.renderListItem();
		this.$issueList.append(result);
		this.issues.push(issue);
		return issue;
	};

	Repo.prototype.render = function() {
		var self = this;
		this.$li = this.$template.octemplate({
			name: this.getName(),
			description: this.getDescription()
		}).data('obj', this);
		this.$li.on('click', function() {
			console.log('Selected repo:', self.data);
			self.setActive(true);
			self.$issueList.find('tbody').empty();
			$(document).triggerHandler('status:issues:loading', {
				status: true,
				repo: self.getName()
			});
			$.when(self.requester.getIssues(self.getOwner(), self.getName()))
				.then(function(response) {
				if(!response.error) {
					//console.log('issues', response.data);
					$.each(response.data, function(idx, issue) {
						//console.log('issue', issue);
						self.insertIssue(issue);
					});
				} else {
					console.warn('response', response);
				}
			})
			.fail(function(response) {
				console.warn('Request Failed:', response);
			})
			.always(function(response) {
				$(document).triggerHandler('status:issues:loading', {
					status: false
				});
			});
		});
		return this.$li;
	};

	/**
	 * Controls access to repositories
	 */
	var RepoList = function(
			requester,
			org,
			$repoList,
			$issueList,
			$issueFullTemplate
	) {
		var self = this;
		this.org = org;
		this.requester = requester;
		this.$repoList = $repoList;
		this.$issueList = $issueList;
		this.$repoTemplate = this.$repoList.find('.repo').detach();
		this.$issueListTemplate = this.$issueList.find('tr.issue').detach();
		this.$issueFullTemplate = $issueFullTemplate
		console.log('RepoList: Issue template', this.$issueFullTemplate);
		this.repos = [];

		$(document).bind('status:issues:loading', function(e, data) {
			if (data.status === true && data.repo) {
				$.each(self.repos, function(idx, repo) {
					if (repo.getName() !== data.repo) {
						repo.setActive(false);
					}
				});
			}
		});
	};

	RepoList.prototype.count = function() {
		return this.repos.length;
	};

	RepoList.prototype.count = function() {
		var activeRepo = null;
		$each(self.repos, function(idx, repo) {
			if (repo.isActive()) {
				activeRepo = repo;
				return false; // break
			}
		});
		return activeRepo;
	};

	/**
	 * Create a Repo object, save it in internal list and append it's rendered result to the list
	 *
	 * @param object repo
	 * @return Repo
	 */
	RepoList.prototype.insertRepo = function(data) {
		var repo = new Repo(
			this.requester,
			this.org,
			data,
			this.$repoTemplate,
			this.$issueListTemplate,
			this.$issueFullTemplate,
			this.$issueList
		);
		var result = repo.render();
		this.$repoList.append(result);
		this.repos.push(repo);
		return repo;
	};

	/**
	 * Get a Repo
	 *
	 * @param name The name of the repo
	 * @return Repo|null
	 */
	RepoList.prototype.find = function(name) {
		console.log('RepoList.find', name);
		var repo = null;
		$.each(this.repos, function(idx, r) {
			if(r.getName() === String(name)) {
				repo = r;
				return false; // break loop
			}
		});
		return repo;
	};

	/**
	* Load repositories
	*/
	RepoList.prototype.loadRepos = function() {
		var self = this;
		var defer = $.Deferred();

		$.when(this.requester.getRepos(this.org))
			.then(function(response) {
			if(!response.error) {
				$.each(response.data, function(idx, repo) {
					self.insertRepo(repo);
				});
				defer.resolve(self.repos);
			} else {
				console.warn('response', response);
				defer.reject({
					error: true,
					message: t('issues', 'Failed loading repositories: {error}', {error:response.message})
				});
			}
		})
		.fail(function(response) {
			console.warn('Request Failed:', response);
			defer.reject({
				error: true,
				message: t('issues', 'Failed loading repositories: {error}', {error:response.message})
			});
		});


		return defer.promise();
	};

	OC.Issues.Repo = Repo;
	OC.Issues.RepoList = RepoList;

})(window, jQuery, OC);
