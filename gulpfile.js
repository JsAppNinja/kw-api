var elixir = require('laravel-elixir');
var gulp = require('gulp');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    //mix.sass('app.scss');
    mix.browserify('admin.js', 'public/admin.bundle.js');
});

gulp.task('ng-admin', function(options) {
	options.src = options.src || 'node_modules/ng-admin/build/*';
	options.dest = options.dest || 'public/vendor/ng-admin';

	return gulp.src(options.src)
    	.pipe(gulp.dest(options.dest));

    // browserify admin.js
});
