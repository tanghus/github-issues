<table id="issues">
	<thead>
		<tr id="issuesHeader">
			<td class="name" colspan="2">
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
		<tr id="issuesFooter">
			<td colspan="2">
				<button>&lt; Previous</button><button>Next &gt;</button>
			</td>
		</tr>
	</tfoot>
</table>
