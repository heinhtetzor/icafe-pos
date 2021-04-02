php artisan down

git pull

# update PHP dependencies
composer install --no-interaction --no-dev --prefer-dist

php artisan migrate --force

php artisan up