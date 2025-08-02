#!/bin/bash

# Quick Update Script for Hanaya Shop on Ubuntu
echo "🔄 Updating Hanaya Shop..."
echo "=========================="

# Navigate to project directory
cd ~/Hanaya-Shop

# Stop containers
echo "⏹️ Stopping containers..."
docker-compose -f docker-compose.production.yml down

# Pull latest image
echo "📥 Pulling latest image..."
docker pull assassincreed2k1/hanaya-shop:latest

# Remove old images
echo "🧹 Cleaning up..."
docker image prune -f

# Start containers
echo "▶️ Starting containers..."
docker-compose -f docker-compose.production.yml up -d

# Wait and check status
echo "⏳ Waiting for startup..."
sleep 30

echo "📊 Container status:"
docker-compose -f docker-compose.production.yml ps

echo ""
echo "✅ Update completed!"
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')
echo "🌐 Website: http://$SERVER_IP"
