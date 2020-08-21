'use strict';
var gulp = require('gulp');
var sass = require('gulp-sass');
var postcss = require("gulp-postcss");
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');


var input = './scss/**/*.scss';
var output = './css';

var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'expanded'
};

var autoprefixerOptions = {
  overrideBrowserslist: ['last 2 versions', '> 5%', 'Firefox ESR']
};




gulp.task('default', function () {
  return gulp
    .src(input)
    .pipe(sourcemaps.init())
    .pipe(sass(sassOptions).on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(concat('styles.css'))
    .pipe(gulp.dest(output));
});



gulp.task('watch', function() {
  gulp.watch('./scss/**/*.scss', gulp.series('default'));
});
