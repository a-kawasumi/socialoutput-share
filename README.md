# socialoutput-share

- Docker Image php:7.3-apache
![image](https://user-images.githubusercontent.com/45552269/92730864-52129f80-f3af-11ea-865b-5e1d99d8c2df.png)

DockerComposeはlocal確認用

実際はDockerfileから生成されるImageをContainerRegistryへPush
```
docker-compose build
```
```
docker-compose up -d
```
```
docker exec -it socialoutput-share_php_1 bash
```
```
composer dump-autoload
```
http://localhost:8080/api/sample/hello

## Deploy
詳細はデプロイファイルを参照
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
