#!/bin/bash

# Для запуска в Linux выполните:
# chmod +x deploy-full.sh
# ./deploy-full.sh

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo
echo "========================================"
echo -e "${BLUE}🚗 shinka.kz - Полное Docker развёртывание${NC}"
echo "========================================"
echo

echo -e "${CYAN}📋 Проверяем наличие Docker...${NC}"
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker не найден! Установите Docker${NC}"
    echo "Для Ubuntu/Debian: sudo apt-get install docker.io docker-compose"
    echo "Для CentOS/RHEL: sudo yum install docker docker-compose"
    exit 1
fi
echo -e "${GREEN}✅ Docker найден${NC}"

echo
echo -e "${CYAN}🔧 Настраиваем окружение...${NC}"
if [ ! -f .env ]; then
    cp docker.env.example .env
    echo -e "${GREEN}✅ Создан файл .env${NC}"
else
    echo -e "${GREEN}✅ Файл .env уже существует${NC}"
fi

echo
echo -e "${YELLOW}🛑 Останавливаем и удаляем старые контейнеры...${NC}"
docker compose -f docker-compose-full.yml down --volumes --remove-orphans

echo
echo -e "${PURPLE}🏗️ Собираем Docker образы...${NC}"
docker compose -f docker-compose-full.yml build --no-cache

echo
echo -e "${CYAN}🚀 Запускаем контейнеры...${NC}"
docker compose -f docker-compose-full.yml up -d

echo
echo -e "${YELLOW}⏳ Ждём готовности MySQL и запуска инициализации...${NC}"
echo -e "${BLUE}🔧 Инициализация выполняется автоматически отдельным контейнером...${NC}"

echo
echo "========================================"
echo -e "${GREEN}✅ Развёртывание завершено!${NC}"
echo "========================================"
echo
echo -e "${CYAN}🌐 Приложение: http://localhost${NC}"
echo -e "${CYAN}🗄️ phpMyAdmin: http://localhost:8080${NC}"
echo
echo -e "${YELLOW}📊 Проверка статуса контейнеров:${NC}"
docker compose -f docker-compose-full.yml ps
echo
echo -e "${PURPLE}📋 Для просмотра логов инициализации:${NC}"
echo "   docker compose -f docker-compose-full.yml logs init"
echo
echo "========================================"

echo
echo -e "${GREEN}Нажмите Enter для завершения...${NC}"
read