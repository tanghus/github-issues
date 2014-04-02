OCMock = {
	generateUrl: function(url, params) {
		var _build = function (text, vars) {
			return text.replace(/{([^{}]*)}/g,
				function (a, b) {
					var r = vars[b];
					return typeof r === 'string' || typeof r === 'number' ? r : a;
				}
			);
		};
		if (url.charAt(0) !== '/') {
			url = '/' + url;

		}
		return '/index.php' + _build(url, params);
	}
};

oc_requesttokenMock = 'xxxx';