// including plugins
var gulp = require('gulp')
var uglify = require("gulp-uglify");
var concat = require("gulp-concat");
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var autoprefixer = require('gulp-autoprefixer');

var sass_input = './content/themes/elements/sass/**/*.scss';
var sass_start = './content/themes/elements/sass/app.scss';
var sass_output = './content/themes/elements/css';

var autoprefixerOptions = {
  browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};

gulp.task('sass', function() {
    return gulp
        .src(sass_start)
        .pipe(sass())
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(gulp.dest(sass_output));
});

gulp.task('watch', function() {
  gulp.watch(sass_input, ['sass']);
});

// task
gulp.task('minify-js', function () {
    gulp.src([
      './content/themes/elements/js/vendor/imagesLoaded.js',
      './content/themes/elements/js/vendor/isotope.js',
      './content/themes/elements/js/vendor/jquery.waypoints.min.js',
      './content/themes/elements/js/vendor/skrollr.js',
      './content/themes/elements/js/vendor/slider.js',
      './content/themes/elements/js/vendor/jquery.mobile.custom.min.js',
      './content/themes/elements/js/vendor/add-to-cart-variation.min.js',

      './content/themes/elements/js/scroll.js',
      './content/themes/elements/js/hero.js',
      './content/themes/elements/js/nav.js',
      './content/themes/elements/js/init-slider.js',
      './content/themes/elements/js/init-isotope.js',
      './content/themes/elements/js/init-skrollr.js',
      './content/themes/elements/js/link-heading.js',
    ])
    .pipe(uglify())
    .pipe(gulp.dest('./content/themes/elements/js/min'));
});

// task
gulp.task('concat', function () {
    gulp.src([
      './content/themes/elements/js/min/imagesLoaded.js',
      './content/themes/elements/js/min/isotope.js',
      './content/themes/elements/js/min/jquery.waypoints.min.js',
      './content/themes/elements/js/min/skrollr.js',
      './content/themes/elements/js/min/slider.js',
      './content/themes/elements/js/min/jquery.mobile.custom.min.js',
      './content/themes/elements/js/min/add-to-cart-variation.min.js',

      './content/themes/elements/js/min/scroll.js',
      './content/themes/elements/js/min/hero.js',
      './content/themes/elements/js/min/nav.js',
      './content/themes/elements/js/min/init-slider.js',
      './content/themes/elements/js/min/init-isotope.js',
      './content/themes/elements/js/min/init-skrollr.js',
      './content/themes/elements/js/min/link-heading.js',
    ])
    .pipe(concat('app.js'))
    .pipe(gulp.dest('./content/themes/elements/js'));
});

gulp.task('default', function () {
  gulp.run('minify-js');
  gulp.run('concat');
  gulp.run('watch');
});