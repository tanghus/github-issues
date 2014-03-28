<article id="issue">
	<header>
		<h1>
			<span class="state {{issue.state}}">{{issue.state}}</span>
			<span class="title">{{issue.title}}</span>
			<a class="number" href="{{issue.html_url}}" target="_blank" title="View on Github">#{{issue.number}}</a>
		</h1>
		<time pubdate datetime="{{issue.isodate}}" title="{{issue.date}}">{{issue.reldate}}</time>
			by <a href="{{issue.user.html_url}}" rel="author" target="_blank">{{issue.user.login}}</a>
	</header>
	<div class="body" ng-bind-html="issue.body"></div>
	<footer>
		<button>Load comments</button>
	</footer>
	<section class="comments"></section>
</article>
