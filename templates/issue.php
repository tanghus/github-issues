<article id="issue" ng-class="{'loading': !initialized}">
	<header ng-show="initialized">
		<h1>
			<a class="back-arrow navigation" title="Back to issue list" href="#/{{org}}/{{repo}}">&lang;</a>
			<span class="state {{issue.state}}">{{issue.state}}</span>
			<span class="title">{{issue.title}}</span>
			<a class="number navigation" href="{{issue.html_url}}" target="_blank" title="View on Github">#{{issue.number}}</a>
		</h1>
		<img width="30" height="30" src="{{issue.user.avatar_url}}size=30" />
		<time pubdate datetime="{{issue.isodate}}" title="{{issue.date}}">{{issue.reldate}}</time>
			by <a href="{{issue.user.html_url}}" rel="author" target="_blank">{{issue.user.login}}</a>
	</header>
	<div class="body" ng-class="{'loading': !initialized}" ng-bind-html="issue.body"></div>
	<section ng-controller="CommentsCtrl">
		<button ng-show="issue.comments > 0 && comments.length === 0 && !loading" ng-click="loadComments(org, repo, issue.number)">Load {{issue.comments}} comments</button>
		<div class="loading list-loader" ng-show="loading"></div>
		<ul class="comments" ng-show="comments.length > 0" ng-class="{'loading': loading}">
			<li class="comment" ng-repeat="comment in comments| orderBy: 'created_at'">
				<img width="30" height="30" src="{{comment.user.avatar_url}}size=30" />
				<a href="{{comment.user.html_url}}" target="_blank" rel="author">{{comment.user.login}}</a>
				commented
				<time pubdate datetime="{{comment.isodate}}" title="{{comment.date}}">{{comment.reldate}}</time>
				<div class="body" ng-bind-html="comment.body"></div>
				</a>
			</li>
		</ul>
		<!-- Add comment box here -->
	</section>
</article>
