var gulp = require('gulp');
var htmlmin = require('gulp-htmlmin');
var elixir = require('laravel-elixir');

gulp.task('compress', function() {
    var opts = {
        collapseWhitespace: true,
        removeAttributeQuotes: true,
        removeComments: true,
        minifyJS: true
    };

    return gulp.src('./storage/framework/views/*')
               .pipe(htmlmin(opts))
               .pipe(gulp.dest('./storage/framework/views/'));
});

gulp.task("copyfiles", function() {

  gulp.src("node_modules/vue/dist/vue.min.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("node_modules/axios/dist/axios.min.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("node_modules/vue-simple-spinner/dist/vue-simple-spinner.min.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("node_modules/vue-sticky/dist/vue-sticky.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery/dist/jquery.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/bootstrap/less/**")
    .pipe(gulp.dest("resources/assets/less/vendor/bootstrap"));

  gulp.src("resources/vendor/bootstrap/dist/js/bootstrap.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/bootstrap/dist/fonts/**")
    .pipe(gulp.dest("public/assets/fonts"));

  gulp.src("resources/vendor/selectize/dist/js/standalone/selectize.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/datepair.js/dist/datepair.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/datepair.js/dist/jquery.datepair.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/selectize/dist/less/**")
    .pipe(gulp.dest("resources/assets/less/vendor/selectize"));

  gulp.src("resources/vendor/jquery.inputmask/dist/inputmask/inputmask.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery.inputmask/dist/inputmask/inputmask.numeric.extensions.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery.inputmask/dist/inputmask/inputmask.date.extensions.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery.inputmask/dist/inputmask/jquery.inputmask.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery-auto-grow-input/jquery.auto-grow-input.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/history.js/scripts/bundled-uncompressed/html4+html5/jquery.history.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/spin.js/spin.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/spin.js/jquery.spin.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery-highlight/jquery.highlight.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery-sticky/jquery.sticky.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/slick-carousel/slick/slick.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));
    
  gulp.src("resources/vendor/jquery-validation/dist/jquery.validate.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));
    
  gulp.src("resources/vendor/jquery-validation/dist/additional-methods.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/Likely/release/likely.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/dropzone/dist/min/dropzone.min.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/chart.js/dist/Chart.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery-bar-rating/jquery.barrating.js")
    .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/jquery-slimscroll/jquery.slimscroll.min.js")
      .pipe(gulp.dest("resources/assets/js/vendor/"));

  gulp.src("resources/vendor/ion.rangeSlider/js/ion.rangeSlider.min.js")
      .pipe(gulp.dest("resources/assets/js/vendor/"));

});

elixir(function(mix) {

  mix.scripts([
      'js/vendor/vue.min.js',
      'js/vendor/axios.min.js',
      'js/vendor/vue-simple-spinner.min.js',
      'js/vendor/vue-sticky.js',
      'js/vue/indicators.js',
      'js/vendor/jquery.js',
      'js/vendor/bootstrap.js',
      //'js/vendor/mobile-detect.js',
      'js/libs/bootstrap-datepicker.js',
      'js/libs/jquery.timepicker.js',
      'js/vendor/jquery.history.js',
      'js/vendor/dropzone.min.js',
      'js/vendor/inputmask.js',
      'js/vendor/jquery.inputmask.js',
      'js/vendor/inputmask.numeric.extensions.js',
      'js/vendor/inputmask.date.extensions.js',
      'js/vendor/jquery.auto-grow-input.js',
      'js/vendor/spin.js',
      'js/vendor/jquery.spin.js',
      'js/vendor/jquery.highlight.js',
      'js/vendor/jquery.sticky.js',
      'js/vendor/slick.js',
      'js/libs/jquery.popupoverlay.js',
      'js/libs/json-forms/underscore.js',
      'js/libs/json-forms/jsv.js',
      'js/libs/json-forms/jsonform.js',
      'js/libs/selectize.js',
      'js/blocks/**/*.js',
      'js/stars.js',
      'js/forms.js',
      'js/reviews.js',
      'js/vendor/jquery.validate.js',
      'js/vendor/additional-methods.js',
      'js/vendor/likely.js',
      'js/vendor/Chart.js',
      'js/vendor/jquery.barrating.js',
      'js/vendor/jquery.slimscroll.min.js',
      'js/vendor/ion.rangeSlider.min.js',
      'js/app.js'
    ],
    'public/assets/js/app.js',
    'resources/assets'
  );

  mix.styles([
    "libs/bootstrap-datepicker.standalone.css",
    "./resources/vendor/dropzone/dist/min/dropzone.min.css",
    "./resources/vendor/Likely/release/likely.css",
    "./resources/vendor/jquery-bar-rating/dist/themes/fontawesome-stars-o.css",
    "bootstrap-social.css",
    "./resources/assets/css/font-awesome.css",
    "./resources/assets/css/slick.css",
    "./resources/vendor/slick-carousel/slick/slick-theme.css",
    './resources/vendor/ion.rangeSlider/css/ion.rangeSlider.css',
    "app.css",
    "sb.css",
    "blocks.css"
  ],'public/assets/css/site.css');

  mix.less([
    'vendor/selectize/selectize.default.less',
    'app.less'
  ], 'public/assets/css/app.css');
  
  mix.version(['public/assets/css/site.css', 'public/assets/css/app.css', 'public/assets/js/app.js']);
});