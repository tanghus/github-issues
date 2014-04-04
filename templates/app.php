<div id="app" ng-app="Issues" ng-controller="AppCtrl">
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
		<div id="app-settings" ng-controller="SettingsCtrl" ng-class="{'open': isOpen}"
			oc-document-click="slideDown">
			<div id="app-settings-header">
				<button class="settings-button" tabindex="0" ng-click="toggleOpen($event)"></button>
			</div>
			<div id="app-settings-content">
				<div ng-show="!isAuthenticated || isUpdating">
					<dl>
						<dt>Login</dt>
						<dd><input ng-model="user.login" type="text" /></dd>
						<dt>Password</dt>
						<dd><input ng-model="user.password" type="password" /></dd>
					</dl>
					<button ng-click="authenticate()">Authenticate</button>
					<button ng-show="isUpdating" ng-click="isUpdating = false">Cancel</button>
				</div>
				<div ng-show="isAuthenticated && !isUpdating">
					<dl>
						<dt>Authenticated as:</dt>
						<dd>{{user.login}}</dd>
					</dl>
					<button ng-click="unAuthenticate()">Remove</button>
					<button ng-click="isUpdating = true">Update</button>
				</div>
			</div>
		</div>
	</div>
	<div id="app-content">
		<div id="app-view" ng-view></div>
	</div>
</div>


