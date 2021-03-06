module.exports = function(config) {
	config.set({
		basePath : '../',
		exclude: [
			'services/externals.js',
		],
		files : [
			'bower_components/angular/angular.js',
			'bower_components/angular-mocks/angular-mocks.js',
			'bower_components/angular-ui/build/angular-ui.js',
			'bower_components/angular-route/angular-route.js',
			//'vendor/jquery/dist/jquery.js',
			//'vendor/jquery-ui/ui/jquery-ui.js',
			//'vendor/momentjs/moment.js',
			//'build/app/directives/*.js',
			'app.js',
			'services/**/*.js',
			'controllers/**/*.js',
			'tests/**/*Spec.js',
			'tests/**/*.js',
			'tests/unit/**/*.js'
		],
		autoWatch : false,
		frameworks: ['jasmine'],
			browsers : ['PhantomJS'],
			plugins : [
				/*'karma-junit-reporter',
				'karma-chrome-launcher',
				'karma-firefox-launcher',*/
				'karma-phantomjs-launcher',
				'karma-jasmine'
			]
	})
}
