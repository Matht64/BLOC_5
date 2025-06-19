#!/bin/bash

# Check if the environment argument is provided and is one of the allowed values (dev, preprod, prod)
if [ -z "$1" ] || ! [[ "$1" =~ ^(dev|preprod|prod)$ ]]; then
    echo "Usage: $0 <environment>"
    echo "Environment must be one of: dev, preprod, prod"
    exit 1
fi

# Stop the container for the specified environment
ENVIRONMENT=$1
docker compose -p videgrenierenligne-$ENVIRONMENT -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml down

# Check if the docker compose command was successful
if [ $? -ne 0 ]; then
    echo "Failed to stop the application in $ENVIRONMENT environment."
    exit 1
fi

echo "Application stopped successfully in $ENVIRONMENT environment."