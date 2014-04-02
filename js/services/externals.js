//angular.module('Issues.services', []).value('version', '0.1');
/*
issues.service('OC', function () {
	return OC;
});

angular.module('BreakfastApp').factory(
  'bagelApiService',
  function($resource) {
    return $resource('bagels.json');
  }
);
*/

(function() {
	angular.module('Issues')
	.service('OC', function () {
		return OC;
	})
	.config([
		'$httpProvider', function($httpProvider) {
		return $httpProvider.defaults.headers.common['requesttoken'] = oc_requesttoken;
		}
	]);
}).call(this);

