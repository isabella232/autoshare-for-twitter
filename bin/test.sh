if [ '5.6' == $PHP_VERSION ]; then
    php -v
    rm composer.lock
    mv composer.json composer.json.bak
    cp bin/composer-5.6.json composer.json
    composer install
    composer run lint
    composer run setup-local-tests
    composer run test
    rm composer.lock composer.json
    mv composer.json.bak composer.json
    composer install
fi;

if [[ '7.0' == $PHP_VERSION || '7.1' == $PHP_VERSION ]]; then
    php -v
    rm composer.lock
    mv composer.json composer.json.bak
    cp bin/composer-7.1.json composer.json
    composer install
    composer run setup-local-tests
    composer run test
    rm composer.lock composer.json
    mv composer.json.bak composer.json
    composer install
fi;

if [[ '7.2' == $PHP_VERSION || '7.3' == $PHP_VERSION ]]; then
    php -v
    composer install
    composer run lint
    composer run setup-local-tests
    composer run test
    WP_SNAPSHOTS_DIR=${{ github.workspace }}/.wpsnapshots/ ./vendor/bin/wpsnapshots configure --aws_key=${{ secrets.AWS_ACCESS_KEY }} --aws_secret=${{ secrets.SECRET_ACCESS_KEY }} --user_name="wp-acceptance" --user_email=travis@10up.com 10up
    composer run test:acceptance
fi;