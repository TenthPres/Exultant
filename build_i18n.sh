#!/usr/bin/env bash

# compile translations
if [ ! -f wp-cli.phar ]; then
    wget -O wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
fi

chmod +x wp-cli.phar

mkdir -p ./build/i18n
cp -r ./i18n ./build/i18n

php ./wp-cli.phar i18n make-json ./build/i18n
php ./wp-cli.phar i18n make-mo ./build/i18n
#cp ./wpml-config.xml ./build/wpml-config.xml