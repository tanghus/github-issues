<article id="issue" ng-class="{'loading': !initialized}">
	<header ng-class="{'hidden': !initialized}">
		<h1>
			<a class="back-arrow navigation" title="Back to issue list" href="#/{{org}}/{{repo}}">&lt;</a>
			<span class="state {{issue.state}}">{{issue.state}}</span>
			<span class="title">{{issue.title}}</span>
			<a class="number navigation" href="{{issue.html_url}}" target="_blank" title="View on Github">#{{issue.number}}</a>
		</h1>
		<time pubdate datetime="{{issue.isodate}}" title="{{issue.date}}">{{issue.reldate}}</time>
			by <a href="{{issue.user.html_url}}" rel="author" target="_blank">{{issue.user.login}}</a>
	</header>
	<div class="body" ng-bind-html="issue.body"></div>
	<footer ng-class="{'hidden': !initialized}">
		<button>Load comments</button>
	</footer>
	<section class="comments"></section>
</article>
