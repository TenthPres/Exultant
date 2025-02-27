import gulp from "gulp";
import clean from "gulp-clean";
import uglify from "gulp-uglify";
import less from "gulp-less";
import csso from "gulp-csso";
import sourcemaps from "gulp-sourcemaps";
import { exec as execCb } from "node:child_process";
// import imagemin from "gulp-imagemin";  // maybe someday when imagemin doesn't have a ton of deprecated dependencies

// Paths
const paths = {
    build: "build/",
    buildAssets: "build/assets/",
    js: "assets/js/**/*.js",
    less: "style.less",
    php: "*.php",
    md: "*.md",
    // json: "*.json",
    copyDirs: [
        "classes",
        "views",
        "template-parts",
        "vendor"
    ],
    copyFiles: [
        "composer.json",
        "composer.lock",
    ],
};

// Clean build directory
gulp.task("clean", function () {
    return gulp.src(paths.build, { allowEmpty: true, read: false }).pipe(clean());
});

// Build i18n
gulp.task("i18n", function (cb) {
    execCb("./build_i18n.sh", function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    });
});

// Copy and minify JavaScript
gulp.task("minify-js", function () {
    return gulp.src(paths.js).pipe(uglify()).pipe(gulp.dest(paths.buildAssets + "js/"));
});

// Compile and minify LESS
// TODO look at optimizing SVGs and other image references before this point.
gulp.task("styles", function () {
    return gulp
        .src(paths.less)
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(csso())
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.build));
});

// copy and optimize images from assets folder
gulp.task("images", function () {
    return gulp
        .src("assets/images/**/*", {encoding: false})
        // .pipe(imagemin())
        .pipe(gulp.dest(paths.buildAssets + "images/"));
});

// Copy files
gulp.task("copy-files", function () {
    return gulp
        .src([paths.php, paths.md, ...paths.copyFiles], { allowEmpty: true })
        .pipe(gulp.dest(paths.build));
});

// Copy specific directories
gulp.task("copy-dirs", function () {
    return gulp
        .src(paths.copyDirs.map(dir => `${dir}/**/*`), { base: "." })
        .pipe(gulp.dest(paths.build));
});

// Default task sequence
export default gulp.task(
    "default",
    gulp.series("clean", "i18n", gulp.parallel("images", "minify-js", "styles"), "copy-files", "copy-dirs")
);