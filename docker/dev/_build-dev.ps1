docker container stop plf-dev-mysql-svc-ip
docker container stop plf-dev-app
docker container stop dev-mailcatcher-1

docker container rm plf-dev-mysql-svc-ip
docker container rm plf-dev-app
docker container rm dev-mailcatcher-1

docker image remove plf-dev-mysql:8.2
docker image remove plf-dev-app:1.0
docker image remove dockage/mailcatcher:0.9.0
docker image remove mailcatcher:latest

# docker volume remove dev_app
# docker volume remove dev_mysql


docker build -t plf-dev-mysql:8.2 -f ./mysql/dockerfile.mysql .
docker build -t plf-dev-phpmyadmin:5.2 -f ./phpmyadmin/dockerfile.phpmyadmin .
docker build -t plf-dev-app:1.0 -f ./app/dockerfile.app ../../.
docker build -t mailcatcher:latest -f ./mailcatcher/dockerfile.mailcatcher .
docker build -t plf-dev-postgresql:16 -f ./postgresql/dockerfile.postgresql .

docker compose -f ./docker-compose-dev.yml up -d




