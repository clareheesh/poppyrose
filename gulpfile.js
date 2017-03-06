var gulp = require('gulp');
var sass = require('gulp-sass');
var minify = require('gulp-clean-css');
var uglify = require('gulp-uglify');
var include = require('gulp-include');

gulp.task('sass', function () {
    return gulp.src('./assets/css/development/style.scss')
        .pipe(sass())
        .pipe(gulp.dest('.'));
});

gulp.task('minify-css', ['sass'], function() {
    return gulp.src('style.css')
        .pipe(minify())
        .pipe(gulp.dest('.'));
});

gulp.task('watch', function() {
    gulp.watch('./assets/css/development/style.scss', ['sass']);
});

gulp.task('default', ['minify-css']);