<table id="issues" ng-class="{'loading': !initialized}">
	<thead>
		<tr id="issuesHeader" ng-class="{'hidden': !initialized}">
			<td class="name" colspan="2">
				<h1><a class="back-arrow navigation" title="Back" href="#/">&lt;&nbsp;</a>{{org}}&nbsp;/&nbsp;{{repo}}<h1>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr class="issue" ng-repeat="issue in issues">
			<td>
				<a href="#/{{org}}/{{repo}}/{{issue.number}}">
				<span class="state {{issue.state}}">{{issue.state}}</span>
				<span class="title" title="{{issue.title}}">{{issue.title}}</span><br />
				<time pubdate datetime="{{issue.isodate}}" title="{{issue.date}}">{{issue.reldate}}</time> -
				</a>
				<a href="{{issue.user.html_url}}" target="_blank" rel="author">{{issue.user.login}}</a>
			</td>
			<td class="number"><a href="{{issue.html_url}}" target="_blank" title="View on Github">#{{issue.number}}</a></td>
		</tr>
	</tbody>
	<tfoot>
		<tr id="issuesFooter" ng-class="{'hidden': !initialized}">
			<td colspan="2">
				<button ng-click="prevPage()" ng-class="{'hidden': page == 1}">&lt; Previous</button>
				<button ng-click="nextPage()">Next &gt;</button>
			</td>
		</tr>
	</tfoot>
</table>
