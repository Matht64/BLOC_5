#!/bin/bash

# Define the image name and tag
IMAGE_NAME="localhost:5000/videgrenierenligne"
IMAGE_TAG="latest"

# Build the Docker image with the target prod
docker build --target prod -t "${IMAGE_NAME}:${IMAGE_TAG}" .

# Push the Docker image to the local registry
docker push "${IMAGE_NAME}:${IMAGE_TAG}"

# Check if the image was pushed successfully
if docker pull "${IMAGE_NAME}:${IMAGE_TAG}"; then
    echo "Image ${IMAGE_NAME}:${IMAGE_TAG} pushed successfully."
else
    echo "Failed to push image ${IMAGE_NAME}:${IMAGE_TAG}."
    exit 1
fi
