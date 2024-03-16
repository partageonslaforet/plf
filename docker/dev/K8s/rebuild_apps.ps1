cd C:\Users\chris\OneDrive\IT\PartageonsLaForet\github_plf\plf\docker\dev
docker build -t plf-dev-app:1.0 -f ./app/dockerfile.app ../../.
cd K8s
kubectl apply -f .\plf-dev-app-deployment.yml
kubectl rollout restart -n default deployment plf-dev-app
kubectl get pods