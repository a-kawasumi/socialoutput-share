# socialoutput-share

- Docker Image php:7.3-apache
![image](https://user-images.githubusercontent.com/45552269/92730864-52129f80-f3af-11ea-865b-5e1d99d8c2df.png)

docker compose は確認用
```
docker-compose build
```
```
docker-compose up -d
```
```
composer dump-autoload
```

local 確認用
```
php artisan serve
```
http://localhost:8080/api/sample/hello

## Deploy
```
sh deploy.sh
```
→ [Google Cloud Platform](https://console.cloud.google.com/)

## Google sheets
composer install
```
composer require google/apiclient:"^2.7"
```
https://github.com/googleapis/google-api-php-client
