# Cara run project

1. Install mongodb PHP driver
2. Run "composer update"
3. Run "composer install"
4. Run "php artisan migrate"
5. Pergi ke path /vendor/laravel/sanctum/src/PersonalAccessToken.php lalu tambahkan "use MongoDB\Laravel\Eloquent\Model;" dan hapus Model yang di import dari Illuminate
6. Run "php artisan serve"
