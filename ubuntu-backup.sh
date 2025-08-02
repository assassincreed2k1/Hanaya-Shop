#!/bin/bash

# Hanaya Shop Backup Script for Ubuntu
# This script creates backups of database and storage files

set -e

echo "💾 Creating backup for Hanaya Shop..."

# Navigate to project directory
PROJECT_DIR="$HOME/hanayashop"
if [ ! -d "$PROJECT_DIR" ]; then
    echo "❌ Project directory not found: $PROJECT_DIR"
    exit 1
fi

cd "$PROJECT_DIR"

# Create backup directory with timestamp
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📁 Creating backup in: $BACKUP_DIR"

# Backup database
echo "🗄️  Backing up database..."
docker-compose exec -T db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > "$BACKUP_DIR/database_backup.sql"

# Backup storage files (if volume is mounted locally)
echo "📂 Backing up storage files..."
docker-compose exec -T app tar czf - -C /var/www/html/storage . > "$BACKUP_DIR/storage_backup.tar.gz"

# Create backup info file
cat > "$BACKUP_DIR/backup_info.txt" << EOF
Hanaya Shop Backup Information
==============================
Date: $(date)
Database: hanaya_shop
Files included:
  - database_backup.sql (MySQL dump)
  - storage_backup.tar.gz (Storage files)

Restore instructions:
  Database: docker-compose exec -T db mysql -u hanaya_user -pTrungnghia2703 hanaya_shop < database_backup.sql
  Storage: docker-compose exec -T app tar xzf - -C /var/www/html/storage < storage_backup.tar.gz
EOF

# Calculate backup size
BACKUP_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)

echo ""
echo "✅ Backup completed successfully!"
echo "📁 Backup location: $PROJECT_DIR/$BACKUP_DIR"
echo "📏 Backup size: $BACKUP_SIZE"
echo ""
echo "📋 Backup contents:"
ls -la "$BACKUP_DIR"
echo ""
echo "📖 See backup_info.txt for restore instructions"
echo ""
