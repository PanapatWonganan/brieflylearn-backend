#!/bin/bash

# ============================================================
# BrieflyLearn Backend Deployment Script
# ============================================================
# Usage: ./deploy.sh
# Run as: deploy user on production server
# ============================================================

set -e  # Exit on error

echo "🚀 Starting BrieflyLearn Backend Deployment..."
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/brieflylearn"
BRANCH="main"

# Check if we're in the right directory
if [ ! -d "$PROJECT_DIR" ]; then
    echo -e "${RED}❌ Error: Project directory not found at $PROJECT_DIR${NC}"
    exit 1
fi

cd $PROJECT_DIR

# 1. Put application in maintenance mode
echo -e "${YELLOW}🔧 Putting application in maintenance mode...${NC}"
php artisan down --retry=60

# 2. Pull latest changes from Git
echo -e "${YELLOW}📥 Pulling latest changes from Git...${NC}"
git fetch origin $BRANCH
git reset --hard origin/$BRANCH

# 3. Install/update Composer dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

# 4. Install npm dependencies and build assets
echo -e "${YELLOW}🎨 Building frontend assets...${NC}"
npm install --production
npm run build

# 5. Run database migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force --no-interaction

# 6. Clear and optimize caches
echo -e "${YELLOW}🧹 Clearing old caches...${NC}"
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo -e "${YELLOW}⚡ Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Optimize Composer autoloader
echo -e "${YELLOW}🔧 Optimizing Composer autoloader...${NC}"
composer dump-autoload --optimize

# 8. Set proper permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R deploy:www-data storage bootstrap/cache

# 9. Bring application back up
echo -e "${YELLOW}✅ Bringing application back online...${NC}"
php artisan up

echo ""
echo -e "${GREEN}=============================================="
echo -e "✨ Deployment completed successfully!"
echo -e "=============================================="
echo -e "Deployed at: $(date)"
echo -e "Git commit: $(git rev-parse --short HEAD)"
echo -e "Branch: $BRANCH"
echo -e "===============================================${NC}"
