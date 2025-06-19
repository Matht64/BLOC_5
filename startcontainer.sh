#!/bin/bash

# Check if the environment argument is provided and is one of the allowed values (dev, preprod, prod)
if [ -z "$1" ] || ! [[ "$1" =~ ^(dev|preprod|prod)$ ]]; then
    echo "Usage: $0 <environment>"
    echo "Environment must be one of: dev, preprod, prod"
    exit 1
fi

# Start the container with the specified environment
ENVIRONMENT=$1
docker compose -p videgrenierenligne-$ENVIRONMENT -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml up -d --build

# Check if the docker compose command was successful
if [ $? -ne 0 ]; then
    echo "Failed to start the application in $ENVIRONMENT environment."
    exit 1
fi

echo "Application started successfully in $ENVIRONMENT environment."