# 🚀 QUICK DEPLOY TO UBUNTU

## One Command Deployment
```bash
curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-deploy.sh | bash
```

## Quick Update (if already deployed)
```bash
cd ~/Hanaya-Shop
./quick-update-ubuntu.sh
```

## Manual Update Commands
```bash
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml down
docker pull assassincreed2k1/hanaya-shop:latest
docker-compose -f docker-compose.production.yml up -d
```

Your site will be available at: `http://YOUR_SERVER_IP`
