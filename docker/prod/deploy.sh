#!/bin/bash
# Do not use latest images in a production environment, always define a version in the manifest files

# Step 2: Build the Docker image with the version tag
docker build -f docker/prod/Dockerfile -t "registry.crontech.com.br:5000/crawler-api:latest" .

# Step 3: Push the image to the registry
docker push "registry.crontech.com.br:5000/crawler-api:latest"
