php composer.phar install &&
php composer.phar require doctrine/dbal &&
php artisan migrate &&
chmod -R 0777 ./vendor &&
chmod -R 0777 ./storage