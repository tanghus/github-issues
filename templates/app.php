<div id="app" ng-app="Issues">
	<div id="app-navigation">
		<ul id="repos" ng-controller="ReposCtrl" ng-class="{'loading': !initialized}">
			<li class="repo" ng-repeat="repo in repos">
				<a href="#/{{org}}/{{repo.name}}">
					<h3>{{repo.name}}</h3>
					{{repo.description}}
				</a>
			</li>
		</ul>
	</div>
	<div id="app-content">
		<div id="app-view" ng-view></div>
	</div>
</div>


