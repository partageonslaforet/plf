docker container stop dev-mysql
docker container stop dev-php-apache

docker container rm dev-mysql
docker container rm dev-php-apache

docker image remove dev-mysql:0.1
docker image remove dev-php-apache:0.1

docker build -t dev-mysql:0.1 -f ./mysql/dockerfile.mysql .
docker build -t dev-php-apache:0.1 -f php-apache/dockerfile.php-apache ../../../

docker compose -f ./docker-compose-dev.yml up -d

#docker run -d --name dev-mysql dev-mysql 