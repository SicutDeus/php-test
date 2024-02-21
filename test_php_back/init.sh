php composer.phar install &&
php composer.phar require doctrine/dbal:3.8 &&
php artisan migrate &&
chmod -R 0777 ./vendor &&
chmod -R 0777 ./storage
