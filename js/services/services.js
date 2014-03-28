angular.module('Issues.services', []).value('version', '0.1');
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

angular.module('Issues').service('OC', function () {
	return OC;
});

angular.module('Issues').service('marked', function () {
	return marked;
});

