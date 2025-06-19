#!/bin/bash

# Check if the environment argument is provided and is one of the allowed values (dev, preprod, prod)
if [ -z "$1" ] || ! [[ "$1" =~ ^(dev|preprod|prod)$ ]]; then
    echo "Usage: $0 <environment>"
    echo "Environment must be one of: dev, preprod, prod"
    exit 1
fi

# Stop and restart the container for the specified environment
ENVIRONMENT=$1
./stopcontainer.sh $ENVIRONMENT
./startcontainer.sh $ENVIRONMENT