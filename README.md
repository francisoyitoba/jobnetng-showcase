# Jobnet.ng — Procedural PHP Showcase (Community Edition)

A trimmed, **sanitized** version of the Jobnet.ng codebase to demonstrate architecture and coding style.
This edition focuses on core flows for **Seekers** and **Employers**, with pluggable AI stubs for CV analysis,
job recommendations, and cover letter generation.

> **Heads up:** This is a *showcase* repo structure meant for GitHub. Replace stubs with your real code as needed
> and keep secrets out of version control.

## Features (Showcase)
**Seekers**
- Email login/register (OAuth stubs for Google/LinkedIn included)
- Profile setup: personal info, experience, education, languages, awards
- Upload CV → AI scoring (stub) + recommendations (stub)
- Job search & apply (cover letter assist — stub)
- Track applications by status
  
**Employers**
- Email login/register
- Business verification: upload docs (stub workflow)
- Post jobs (free; premium stubs optional)
- AI assists job description & summary (stub)
- View jobs and manage incoming applications (shortlist/reject)

## Tech Stack
- PHP 8.2 (procedural style, modular includes)
- MySQL 8.x (PDO)
- Minimal routing (front controller)
- Optional Docker (php-apache + mysql)
- Optional CI: PHP_CodeSniffer + PHPStan

## Quick Start (no Docker)
1. Create a MySQL database (e.g., `jobnet_demo`).
2. Copy `.env.example` → `.env` and set DB creds.
3. Import schema & seeds:
   ```bash
   mysql -u root -p jobnet_demo < sql/schema.sql
   mysql -u root -p jobnet_demo < sql/seed-demo.sql
   ```
4. Run the PHP dev server:
   ```bash
   php -S 127.0.0.1:8080 -t public
   ```
5. Try endpoints (examples):
   - `GET  /api/health`
   - `POST /api/auth/register` (email/password JSON)
   - `POST /api/auth/login`
   - `GET  /api/jobs`
   - `POST /api/jobs/apply`
   - `GET  /api/applications/mine`

Demo accounts (from seeds):
- **Seeker:** `seeker@example.com` / `password123`
- **Employer:** `employer@example.com` / `password123`

## (Optional) Docker
```bash
docker compose up --build -d
# App:    http://localhost:8080
# MySQL:  127.0.0.1:3306 (user: root / pass: rootpass / db: jobnet_demo)
```
> If MySQL init runs before the app is ready, re-run the SQL imports once the DB is up.

## Configuration
Environment is read from `.env`:

```ini
APP_ENV=local
APP_DEBUG=1
APP_URL=http://127.0.0.1:8080

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=jobnet_demo
DB_USER=root
DB_PASS=

MAIL_FROM=hello@example.com

AI_PROVIDER=mock           # mock|openai|custom
OPENAI_API_KEY=

OAUTH_GOOGLE_CLIENT_ID=
OAUTH_GOOGLE_CLIENT_SECRET=
OAUTH_LINKEDIN_CLIENT_ID=
OAUTH_LINKEDIN_CLIENT_SECRET=

STORAGE_PATH=storage
```

## Project Layout
```
.
├─ README.md
├─ LICENSE
├─ .gitignore
├─ .env.example
├─ public/
│  ├─ .htaccess
│  └─ index.php
├─ routes/
│  └─ web.php
├─ app/
│  ├─ bootstrap.php
│  ├─ config.sample.php
│  ├─ db.php
│  ├─ helpers.php
│  └─ services/
│     ├─ Auth.php
│     ├─ AIAdapter.php
│     └─ Recommendation.php
├─ sql/
│  ├─ schema.sql
│  └─ seed-demo.sql
├─ storage/            # ignored — for uploads, generated files
├─ .github/
│  └─ workflows/
│     └─ php.yml
├─ composer.json
├─ docker-compose.yml
├─ Dockerfile
└─ .docker/
   └─ vhost.conf
```

## Security Notes
- **Never** commit `.env`, credentials, or API keys.
- Sanitise user input, use prepared statements (PDO).
- Include CSRF/nonce checks in real forms (omitted in this showcase for brevity).
- For OAuth, plug a proper library and secure redirect URIs.

## 👤 Author
**Francis Oyitoba**  
[Portfolio](https://francisoyitoba.com) · [LinkedIn](https://www.linkedin.com/in/francis-oyitoba-85a89bb9/) · [GitHub](https://github.com/francisoyitoba)

## License
MIT for your original code. Third‑party licenses remain with their owners.