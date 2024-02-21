docker container stop dev-mysql
docker container stop dev-app

docker container rm dev-mysql
docker container rm dev-app

docker image remove dev-mysql:0.1
docker image remove dev-app:0.1

docker volume remove dev_app
docker volume remove dev_mysql

docker build -t dev-mysql:0.1 -f ./mysql/dockerfile.mysql .
docker build -t dev-app:0.1 -f ./app/dockerfile.app ../../.

docker compose -f ./docker-compose-dev.yml up -d

