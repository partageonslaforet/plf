kubectl delete deployment  plf-dev-mysql
kubectl delete deployment  plf-dev-phpmyadmin
kubectl delete deployment  plf-dev-app

kubectl delete service plf-dev-mysql-svc-ip
kubectl delete service plf-dev-phpmyadmin-ip
kubectl delete service plf-dev-app-ip

kubectl delete configmap plf-dev-configmap
kubectl delete secrets  plf-dev-secrets

kubectl delete pvc plf-dev-mysql-pvc
kubectl delete pv plf-dev-mysql-pv

