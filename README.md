# LeadPally

LeadPally is a modern SaaS platform that helps businesses discover, organize, and manage high-quality business leads across Africa.

## Technology Stack

### Backend
- Laravel 12
- PHP 8.2+
- PostgreSQL
- Redis
- Laravel Sanctum

### Frontend
- Next.js 15
- React
- TypeScript
- Tailwind CSS

### Infrastructure
- Docker
- GitHub Actions
- Mailpit

---

## Quick Start

### Start infrastructure

```bash
docker compose up -d
```

### Backend

```bash
cd leadpally-api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend

```bash
cd leadpally-frontend
cp .env.local.example .env.local
npm install
npm run dev
```

---

## Local URLs

| Service | URL |
|---------|-----|
| Frontend | http://localhost:3000 |
| API | http://localhost:8000 |
| Health Check | http://localhost:8000/api/health |
| Mailpit | http://localhost:8025 |

---

## Documentation

- `ARCHITECTURE.md`
- `INSTALL.md`
- `CONTRIBUTING.md`
- `DEFINITION_OF_DONE.md`

---

## Project Status

- ✅ Sprint 1 Complete
- 🚧 Sprint 2 In Progress

---

## License

Private repository.
