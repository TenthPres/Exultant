#!/usr/bin/env bash

# Assumes PHP CLI, curl, and zip are already installed.

composer install

# assume NPM is already installed

npm update

rm -r build
#rm exultant.zip
mkdir build
mkdir build/assets
mkdir build/assets/js

# install uglify and uglify the JS files.
npm install uglifyjs-folder -g

# uglify all files in assets/js
uglifyjs-folder assets/js -o build/assets/js/
cp -r assets build
cd ./build || exit
cd ..

# run lessc and copy to build
npm install -g less
npm install -g csso-cli
lessc style.less style.css --source-map-include-source --source-map=style.css.map
csso -i style.css -o style.min.css -s style.min.css.map --input-source-map style.css.map

cp style.min.css build/style.css
cp style.min.css.map build/style.min.css.map

# compile translations
#if [ ! -f wp-cli.phar ]; then
#    wget -O wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
#fi
#
#cp -r ./i18n ./build/i18n
#
#php ./wp-cli.phar i18n make-json ./build/i18n
#php ./wp-cli.phar i18n make-mo ./build/i18n
#cp ./wpml-config.xml ./build/wpml-config.xml
#cp ./composer.json ./build/composer.json


cp -r ./classes ./build/classes
cp -r ./views ./build/views
cp -r ./template-parts ./build/template-parts
cp -r ./vendor ./build/vendor

find . -maxdepth 1 -iname "*.php" -exec cp {} build/ \;
find . -maxdepth 1 -iname "*.md" -exec cp {} build/ \;
find . -maxdepth 1 -iname "*.json" -exec cp {} build/ \;

cd ./build || exit
#find . -exec zip ../exultant.zip {} \;
cd ..
