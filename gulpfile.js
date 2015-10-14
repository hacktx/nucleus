'use strict';

var browserify  = require('browserify');
var buffer      = require('vinyl-buffer');
var gulp        = require('gulp');
var reactify    = require('reactify');
var source      = require('vinyl-source-stream');
var uglify      = require('gulp-uglify');
var xhpjs       = require('xhpjs');

gulp.task('build', function () {
  var b = browserify({
    debug: true,
    transform: [[reactify, {"es6": true}]],
  });

  b.require('./react/MembersTable.js', { expose: 'MembersTable' });
  b.require('./react/Settings.js', { expose: 'Settings' });
  b.require('xhpjs');

  return b.bundle()
    .pipe(source('bundle.js'))
    .pipe(buffer())
    .pipe(uglify())
    .pipe(gulp.dest('./public/js/'));
});

gulp.task('default', ['build']);
