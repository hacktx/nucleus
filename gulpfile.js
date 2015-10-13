'use strict';

var browserify  = require('browserify');
var gulp        = require('gulp');
var reactify    = require('reactify');
var source      = require('vinyl-source-stream');
var xhpjs       = require('xhpjs');

gulp.task('build', function () {
  var b = browserify();

  b.require('xhpjs')
    .require('./react/members.js')
    .transform(reactify);

  return b.bundle()
    .pipe(source('bundle.js'))
    .pipe(gulp.dest('./public/js/'));
});

gulp.task('default', ['build']);
