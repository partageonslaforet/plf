docker container stop dev-mysql
docker container stop dev-app
docker container stop dev-mailcatcher-1

docker container rm dev-mysql
docker container rm dev-app
docker container rm dev-mailcatcher-1

docker image remove dev-mysql:0.1
docker image remove dev-app:0.1
docker image remove dockage/mailcatcher:0.9.0

docker volume remove dev_app
#docker volume remove dev_mysql

docker build -t dev-mysql:0.1 -f ./mysql/dockerfile.mysql .
docker build -t dev-app:0.1 -f ./app/dockerfile.app ../../.
docker build -t mailcatcher -f ./mailcatcher/dockerfile.mailcatcher .

docker compose -f ./docker-compose-dev.yml up -d

