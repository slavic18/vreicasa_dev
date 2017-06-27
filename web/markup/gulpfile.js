/*******************************************************************************
 0. DEPENDENCIES
 *******************************************************************************/

var gulp = require('gulp'), // gulp core
    lr = require('tiny-lr'),
    livereload = require('gulp-livereload'),
    imagemin = require('gulp-imagemin'), // imagemin
    concat = require('gulp-concat'), // concatinate js
    uglify = require('gulp-uglify'), // uglifies the js
    haml = require('gulp-haml'), // haml support
    prettify = require('gulp-prettify'), // prettify spaces and tabs
    fileinclude = require('gulp-file-include'),
    spritesmith = require('gulp.spritesmith'), // generate sprite
    changed = require('gulp-changed'), //Only pass through changed files
    sass = require('gulp-sass'), // gulp sass
    server = lr(); //live reload
var d = new Date();

var EXPRESS_PORT = 4000;
var EXPRESS_ROOT = 'build';
var LIVERELOAD_PORT = 35729;

// The server will be available at http://localhost:4000
function startExpress() {
    var express = require('express');
    var app = express();
    app.use(require('connect-livereload')());
    app.use(express.static(EXPRESS_ROOT));
    app.listen(EXPRESS_PORT);
}
/*******************************************************************************
 1. FILE DESTINATIONS (RELATIVE TO ASSSETS FOLDER)
 *******************************************************************************/

var main = {
    haml_src: 'haml/**/*.haml',
    haml_html_src: 'haml/',
    js_concat_src: [ // all js files that should be concatinated
        'js/*.js'
    ],
    js_dest: 'build/js/', // where to put minified js
    img_src: 'images/*', // folder with original images
    img_dest: 'build/images/' // folder with optimized images
};
var wpSrc = {
    css: '../app/themes/project/assets/css',
    js: '../app/themes/project/assets/js',
    images: '../app/themes/project/assets/images',
    fonts: '../app/themes/project/assets/fonts',
    html: '../app/themes/project/templates/html',
};


/*******************************************************************************
 2. HAML FILES
 *******************************************************************************/
gulp.task('hamlify_main', function () {
    // Haml main domain.
    gulp.src(main.haml_src)
        .pipe(changed('haml/*.haml'))
        .pipe(haml())
        .pipe(prettify({
            indentSize: 2
        }))
        .pipe(gulp.dest('build/html/'))
        .pipe(livereload(server));
});

gulp.task('fileinclude', function () {
    gulp.src('build/html/*.html')
        .pipe(changed('build/*.html'))
        .pipe(fileinclude({
            prefix: '@@',
            basepath: '@file'
        }))
        .pipe(gulp.dest('build/'))
        .pipe(gulp.dest(wpSrc.html));
});
/*******************************************************************************
 3. JS TASKS
 *******************************************************************************/
// processing sass files
gulp.task('sass', function () {
    return gulp.src('./scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest('./build/css'))
        .pipe(gulp.dest(wpSrc.css));
});

// minify & concatinate js
gulp.task('js-concat', function () {
    gulp.src(main.js_concat_src) // get the files
    // .pipe(uglify()) // uglify the files
        .pipe(concat('scripts.min.js')) // concatinate to one file
        .pipe(gulp.dest(main.js_dest)) // where to put the files
        .pipe(gulp.dest(wpSrc.js))
        .pipe(livereload(server));
});

/*******************************************************************************
 4. IMAGES TASKS
 *******************************************************************************/
gulp.task('sprite', function () {
    var spriteData = gulp.src('images/sprite/*.png').pipe(spritesmith({
        imgName: 'sprite.png',
        cssName: 'sprite.css',
        padding: 10,
        cssOpts: {
            cssClass: function (item) {
                return '.sp-' + item.name;
            }
        }
    }));
    spriteData.img.pipe(gulp.dest('images/'));
    spriteData.css.pipe(gulp.dest('css/'));

    var spriteDataStyl = gulp.src('images/sprite/*.png').pipe(spritesmith({
        imgName: 'sprite.png',
        cssName: 'sprite.scss',
        padding: 10,
        cssOpts: {
            cssClass: function (item) {
                return '.sp-' + item.name;
            }
        }
    }));
    spriteData.img.pipe(gulp.dest('build/css/'));
    spriteDataStyl.css.pipe(gulp.dest('scss/'));
});

// optimize images and paste them into build images folder
gulp.task('imagemin', function () {
    return gulp.src(main.img_src) // get the images
        .pipe(changed(main.img_dest))
        // .pipe(imagemin({
        //     progress: true,
        //     progressive: true,
        //     interlaced: true,
        //     optimizationLevel: 4
        // }))
        .pipe(gulp.dest(main.img_dest)) //save images
        .pipe(livereload(server));
});

/*******************************************************************************
 5. GULP TASKS
 *******************************************************************************/

gulp.task('default', function () {
    startExpress();
    // on gulp start, run these tasks
    // complile html on haml change
    gulp.watch('haml/*.haml', ['hamlify_main']);
    gulp.watch('build/html/*.html', ['fileinclude']);

    gulp.watch('scss/**/*.scss', ['sass']);
    // concat and minify js when it's changed
    gulp.watch(main.js_concat_src, ['js-concat']);

    // optimize images
    gulp.watch('images/**/*.jpg', ['imagemin']);
});
