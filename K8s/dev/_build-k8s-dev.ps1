kubectl apply -f .\plf-dev-secrets.yml
kubectl apply -f .\plf-dev-configmap.yml



kubectl apply -f .\plf-dev-mysql-persistentvolume.yml
kubectl apply -f .\plf-dev-mysql-persistentvolumeclaim.yml


kubectl apply -f .\plf-dev-mysql-deployment.yml
kubectl apply -f .\plf-dev-mysql-services.yml



kubectl apply -f .\plf-dev-phpmyadmin-deployment.yml
kubectl apply -f .\plf-dev-phpmyadmin-services.yml


kubectl apply -f .\plf-dev-postgresql-persistentvolume.yml
kubectl apply -f .\plf-dev-postgresql-persistentvolumeclaim.yml
kubectl apply -f .\plf-dev-postgresql-deployment.yml
kubectl apply -f .\plf-dev-postgresql-services.yml


kubectl apply -f .\plf-dev-pgadmin-deployment.yml
kubectl apply -f .\plf-dev-pgadmin-services.yml

kubectl apply -f .\plf-dev-mailcatcher-deployment.yml
kubectl apply -f .\plf-dev-mailcatcher-services.yml



kubectl apply -f .\plf-dev-app-deployment.yml
kubectl apply -f .\plf-dev-app-services.yml



kubectl get pods
kubectl get services

kubectl rollout restart -n default deployment plf-dev-app
