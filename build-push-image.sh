#!/bin/bash

# Script để build và đẩy Docker image lên Docker Hub
# Sử dụng: ./build-push-image.sh [version]

VERSION=${1:-latest}
IMAGE_NAME="assassincreed2k1/hanaya-shop"

echo "======================================================"
echo "     BUILD VÀ PUSH DOCKER IMAGE"
echo "======================================================"
echo "Image: $IMAGE_NAME:$VERSION"
echo "======================================================"

# Build Docker image
echo "Building Docker image..."
docker build -t $IMAGE_NAME:$VERSION .

# Tag phiên bản latest
if [ "$VERSION" != "latest" ]; then
    docker tag $IMAGE_NAME:$VERSION $IMAGE_NAME:latest
fi

# Đẩy lên Docker Hub
echo "Pushing to Docker Hub..."
docker push $IMAGE_NAME:$VERSION

# Đẩy phiên bản latest
if [ "$VERSION" != "latest" ]; then
    docker push $IMAGE_NAME:latest
fi

echo "======================================================"
echo "     HOÀN TẤT"
echo "======================================================"
echo "Image đã được đẩy lên Docker Hub:"
echo "- $IMAGE_NAME:$VERSION"
if [ "$VERSION" != "latest" ]; then
    echo "- $IMAGE_NAME:latest"
fi
echo "======================================================"
