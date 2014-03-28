<div id="app" ng-app="Issues">
	<div id="app-navigation" class="loading">
		<ul id="repos" ng-controller="ReposCtrl">
			<li class="repo" ng-repeat="repo in repos">
				<a href="#/{{org}}/{{repo.name}}">
					<h3>{{repo.name}}</h3>
					{{repo.description}}
				</a>
			</li>
		</ul>
	</div>
	<div id="app-content" class="loading">
		<div ng-view></div>
	</div>
</div>


