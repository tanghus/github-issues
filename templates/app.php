<div id="app" ng-app="Issues">
	<div id="app-navigation">
		<ul id="repos" ng-controller="ReposCtrl" ng-class="{'loading': !initialized}">
			<li><input type="search" ng-model="search.name" placeholder="search"></li>
			<li class="repo" ng-repeat="repo in repos | filter:search:strict | orderBy: 'name'">
				<a href="#/{{org}}/{{repo.name}}">
					<h3>{{repo.name}}</h3>
					&#8226; {{repo.description}}
				</a>
			</li>
		</ul>
		<div id="app-settings" ng-controller="SettingsCtrl">
			<div id="app-settings-header">
				<button class="settings-button" tabindex="0"></button>
			</div>
			<div id="app-settings-content">
			</div>
		</div>
	</div>
	<div id="app-content">
		<div id="app-view" ng-view></div>
	</div>
</div>


