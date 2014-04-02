<table id="issues" ng-class="{'loading': !initialized}">
	<thead>
		<tr id="issuesHeader" ng-show="initialized">
			<td class="name" colspan="2">
				<h1><a class="back-arrow navigation" title="Back" href="#/">&lang;&nbsp;{{org}}</a>&nbsp;/&nbsp;{{repo}}<h1>
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
	<tfoot ng-show="issues.length > 0">
		<tr id="issuesFooter" ng-show="initialized">
			<td colspan="2">
				<a href="#/{{org}}/{{repo}}/?page={{navigation.first}}" class="button" ng-hide="navigation.page == 1">
					&Lang; First
				</a>
				<a href="#/{{org}}/{{repo}}/?page={{navigation.prev}}" class="button" ng-hide="navigation.page == 1">
					&lang; Previous
				</a>
				<a href="#/{{org}}/{{repo}}/?page={{navigation.next}}" class="button" ng-hide="navigation.page == navigation.last">
					Next &rang;
				</a>
				<a href="#/{{org}}/{{repo}}/?page={{navigation.last}}" class="button" ng-hide="!navigation.last || navigation.page == navigation.last">
					Last &Rang;
				</a>
			</td>
		</tr>
	</tfoot>
</table>
