'use strict';
const gulp = require('gulp');
const sass = require('gulp-sass');
const less = require('gulp-less');
const cleanCSS = require('gulp-clean-css');
const del = require('del');
const concat = require('gulp-concat');
const copy = require('gulp-copy');
const autoprefixer = require('gulp-autoprefixer');

// paths to all resources that need to be piped
const path = {
  main: {
    src: 'assets/stylesheets/main.scss',
    dest: 'build/css',
  },
  bsDialog: {
    src: 'assets/stylesheets/bootstrap-dialog.less',
    dest: 'build/css',
  },
  bsSelect: {
    src: 'assets/stylesheets/bootstrap-select/less/*.less',
    dest: 'build/css',
  },
  images: {
    src: 'assets/images/*',
    dest: 'build/images',
  },
  fonts: {
    src: 'assets/fonts/**/*',
    dest: 'build/fonts',
  },
};

// remove the build folder completely
function clean() {
  return del(['build']);
}

// build main stylesheet for the theme
function styleMain() {
  return gulp
    .src(path.main.src)
    .pipe(sass())
    .pipe(
      autoprefixer({
        browsers: ['last 2 versions'],
        cascade: false,
      })
    )
    .pipe(cleanCSS())
    .pipe(gulp.dest(path.main.dest));
}

// build bootstrap dialog
function styleDialog() {
  return gulp
    .src(path.bsDialog.src)
    .pipe(less())
    .pipe(cleanCSS())
    .pipe(gulp.dest(path.bsDialog.dest));
}

// build bootstrap select
function styleSelect() {
  return gulp
    .src(path.bsSelect.src)
    .pipe(less())
    .pipe(cleanCSS())
    .pipe(concat('bootstrap-select.css'))
    .pipe(gulp.dest(path.bsSelect.dest));
}

// copy over all images
function images() {
  return gulp
    .src(path.images.src)
    .pipe(copy('build', { prefix: 1 }).pipe(gulp.dest(path.images.dest)));
}

// copy over all fonts
function fonts() {
  return gulp
    .src(path.fonts.src)
    .pipe(copy('build', { prefix: 1 }).pipe(gulp.dest(path.fonts.dest)));
}

// define what should happen when build task is ran
const build = gulp.series(
  clean,
  styleMain,
  styleSelect,
  styleDialog,
  images,
  fonts
);

// gulp task "build"
gulp.task('build', build);
