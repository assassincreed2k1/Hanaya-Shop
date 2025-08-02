#!/bin/bash

# Hanaya Shop Update Script for Ubuntu
# This script updates the Hanaya Shop application to the latest version

set -e

echo "🔄 Updating Hanaya Shop to latest version..."

# Navigate to project directory
PROJECT_DIR="$HOME/hanayashop"
if [ ! -d "$PROJECT_DIR" ]; then
    echo "❌ Project directory not found: $PROJECT_DIR"
    echo "Please run the setup script first!"
    exit 1
fi

cd "$PROJECT_DIR"

# Check if docker-compose.yml exists
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ docker-compose.yml not found in $PROJECT_DIR"
    echo "Please run the setup script first!"
    exit 1
fi

echo "📁 Working in directory: $PROJECT_DIR"

# Pull the latest image
echo "📥 Pulling latest Hanaya Shop image..."
docker-compose pull app

# Stop the application gracefully
echo "🛑 Stopping application..."
docker-compose stop app

# Remove the old container
echo "🗑️  Removing old container..."
docker-compose rm -f app

# Start the updated application
echo "🚀 Starting updated application..."
docker-compose up -d app

# Wait for the application to start
echo "⏳ Waiting for application to start..."
sleep 30

# Check if the application is healthy
echo "🏥 Checking application health..."
if docker-compose exec app curl -f http://localhost/health > /dev/null 2>&1; then
    echo "✅ Application is healthy!"
else
    echo "⚠️  Application might not be fully ready yet, please check logs"
fi

# Show current status
echo "📊 Current status:"
docker-compose ps

echo ""
echo "🎉 Update completed successfully!"
echo ""
echo "🌐 Access your application at: http://localhost"
echo "📝 View logs: docker-compose logs -f app"
echo ""
