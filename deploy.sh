#!/bin/bash

# AIU Portfolio - Deployment Script for Shared Server
# This script handles the deployment process for production environment

echo "========================================="
echo "AIU Portfolio - Deployment Script"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    echo "Please create .env file from .env.production.example and configure it."
    exit 1
fi

echo -e "${YELLOW}[1/8] Enabling maintenance mode...${NC}"
php artisan down || true

echo ""
echo -e "${YELLOW}[2/8] Pulling latest changes from repository...${NC}"
git pull origin main

echo ""
echo -e "${YELLOW}[3/8] Installing/updating PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

echo ""
echo -e "${YELLOW}[4/8] Installing/updating Node dependencies...${NC}"
npm ci --production

echo ""
echo -e "${YELLOW}[5/8] Building frontend assets...${NC}"
npm run build

echo ""
echo -e "${YELLOW}[6/8] Running database migrations...${NC}"
php artisan migrate --force

echo ""
echo -e "${YELLOW}[7/8] Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo -e "${YELLOW}[8/8] Disabling maintenance mode...${NC}"
php artisan up

echo ""
echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}Deployment completed successfully!${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo "Don't forget to:"
echo "  - Restart queue workers if you're using them"
echo "  - Clear any external caches (CDN, Redis, etc.)"
echo ""
