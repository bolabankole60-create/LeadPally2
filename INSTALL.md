# LeadPally Installation

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+
- npm 10+
- Docker and Docker Compose

## Local Setup

```bash
git clone https://github.com/bolabankole60-create/LeadPally2.git
cd LeadPally2

docker compose up -d

cd leadpally-api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

cd ../leadpally-frontend
cp .env.local.example .env.local
npm install
npm run dev
```

## Local URLs

- Frontend: http://localhost:3000
- API health: http://localhost:8000/api/health
- Mailpit: http://localhost:8025
