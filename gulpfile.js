const gulp = require('gulp');
const concat = require('gulp-concat');
const strip = require("gulp-strip-banner");
const uglify = require("gulp-uglify");

function js() {

    return gulp.src('./script.js')

        .pipe(strip()) // Enlever les commentaires

        .pipe(uglify()) // Minify JS

        .pipe(concat('script.min.js')) // Concat JS files

        .pipe(gulp.dest('./'));

}

exports.js = js;

exports.watch = function() {

    gulp.watch('./script.js', js);

};











