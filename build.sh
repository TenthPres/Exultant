#!/usr/bin/env bash

# Assumes PHP CLI, curl, and zip are already installed.

# assume NPM is already installed

npm update

rm -r build
rm exultant.zip
mkdir build
mkdir build/assets
mkdir build/assets/js

# install uglify and uglify the JS files.
echo $(npm install -g uglify-js)

# uglify all files in assets/js
uglifyjs assets/js/*.js -o build/assets/js/
cp -r assets build
cd ./build || exit
cd ..

# run lessc and copy to build
lessc style.less > style.css --source-map-map-inline
cp style.less > build/style.less
cp style.css build/style.css

# use csso to minify the css and create a sourcemap
csso style.css -o build/style.min.css -s file


# compile translations
if [ ! -f wp-cli.phar ]; then
    wget -O wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
fi

cp -r ./i18n ./build/i18n

php ./wp-cli.phar i18n make-json ./build/i18n
php ./wp-cli.phar i18n make-mo ./build/i18n
cp ./wpml-config.xml ./build/wpml-config.xml
cp ./composer.json ./build/composer.json

cp -r ./ext ./build/ext
cp -r ./src ./build/src
cp -r ./classes ./build/classes
cp -r ./views ./build/views
cp -r ./template-parts ./build/template-parts
cp -r ./vendor ./build/vendor  # check that this is really what we want to do

find . -maxdepth 1 -iname "*.php" -exec cp {} build/ \;
find . -maxdepth 1 -iname "*.md" -exec cp {} build/ \;
find . -maxdepth 1 -iname "*.json" -exec cp {} build/ \;

cd ./build || exit
#find . -exec zip ../exultant.zip {} \;
cd ..
