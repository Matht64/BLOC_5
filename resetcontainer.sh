#!/bin/bash

# Check if the environment argument is provided and is one of the allowed values (dev, preprod, prod)
if [ -z "$1" ] || ! [[ "$1" =~ ^(dev|preprod|prod)$ ]]; then
    echo "Usage: $0 <environment>"
    echo "Environment must be one of: dev, preprod, prod"
    exit 1
fi

# Remove the container and volumes for the specified environment
ENVIRONMENT=$1
docker compose -p videgrenierenligne-$ENVIRONMENT -f docker-compose.yml -f docker-compose.$ENVIRONMENT.yml down -v

# Check if the docker compose command was successful
if [ $? -ne 0 ]; then
    echo "Failed to remove the application in $ENVIRONMENT environment."
    exit 1
fi

echo "Application removed successfully in $ENVIRONMENT environment."

# Restart the application with the specified environment
./startcontainer.sh $ENVIRONMENT