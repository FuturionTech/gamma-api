# Quick Start Guide - Gamma Neutral API

Get up and running in 5 minutes! ⚡

## Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL
- AWS S3 account (or use local storage for testing)

## Setup Steps

### 1. Install Dependencies

```bash
composer install
```

### 2. Create Environment File

Create `.env` file with these minimum settings:

```env
APP_NAME="Gamma Neutral API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_KEY=

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gamma_api
DB_USERNAME=postgres
DB_PASSWORD=your_password

# AWS S3 (or use 'local' for testing)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket

# Mail (use 'log' for development)
MAIL_MAILER=log

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Redis
REDIS_CLIENT=predis
```

### 3. Generate App Key

```bash
php artisan key:generate
```

### 4. Create Database

```bash
createdb gamma_api
```

Or using psql:
```sql
CREATE DATABASE gamma_api;
```

### 5. Run Migrations & Seeds

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- Gamma Neutral application
- Admin user: `admin@gammaneutral.com` / `password`
- 7 services
- 6 solutions with features/benefits
- Sample data for all entities

### 6. Start Server

```bash
php artisan serve
```

### 7. Start Queue Worker (Optional)

In a new terminal:
```bash
php artisan queue:work
```

## Test the API

### Open GraphQL Playground

Visit: http://localhost:8000/graphiql

### Test Login

```graphql
mutation {
  login(input: {
    email: "admin@gammaneutral.com"
    password: "password"
  }) {
    token
    administrator {
      name
      email
    }
  }
}
```

### Query Services (Public)

```graphql
query {
  services(application_id: 1, is_active: true) {
    id
    title
    description
    icon
    category
  }
}
```

### Query Solutions with Features (Public)

```graphql
query {
  solutions(application_id: 1) {
    id
    title
    subtitle
    features {
      title
      description
    }
    benefits {
      title
      description
    }
  }
}
```

### Create Service (Admin - use token from login)

Add to HTTP Headers in GraphiQL:
```json
{
  "Authorization": "Bearer YOUR_TOKEN_HERE"
}
```

Then run:
```graphql
mutation {
  createService(input: {
    application_id: 1
    title: "DevOps"
    description: "Complete DevOps solutions"
    icon: "cog"
    category: "Infrastructure"
    slug: "devops"
    order: 8
    is_active: true
  }) {
    id
    title
    slug
  }
}
```

## Using with Local Storage (No S3)

If you don't have AWS S3 set up yet:

1. Update `.env`:
```env
FILESYSTEM_DISK=public
```

2. Create storage link:
```bash
php artisan storage:link
```

3. Files will be stored in `storage/app/public/` and accessible via `/storage/` URL

## Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# View routes
php artisan route:list

# Validate GraphQL schema
php artisan lighthouse:validate-schema

# Fresh database with seeds
php artisan migrate:fresh --seed

# View queue failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <job-id>
```

## Troubleshooting

### Redis Connection Error

Change in `.env`:
```env
REDIS_CLIENT=predis
```

Then clear config:
```bash
php artisan config:clear
```

### Database Connection Error

Verify PostgreSQL is running:
```bash
pg_isready
```

Check credentials in `.env`

### GraphQL Schema Error

Validate schema:
```bash
php artisan lighthouse:validate-schema
```

Clear caches:
```bash
php artisan config:clear
php artisan cache:clear
```

### Permission Denied on Storage

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Next Steps

1. ✅ API is running
2. 📖 Read `README.md` for detailed documentation
3. 📊 Check `docs/API.md` for all API endpoints
4. 🧪 Review `IMPLEMENTATION_SUMMARY.md` for what's been built
5. 🚀 Start integrating with your frontend!

## Default Data

After seeding, you'll have:

- **1 Application**: Gamma Neutral Consulting Inc.
- **1 Admin**: admin@gammaneutral.com / password
- **7 Services**: AI, Data Engineering, Cybersecurity, BI, Big Data, Cloud, Project Management
- **6 Solutions**: Financial, Education, Business, Government, NGO, Healthcare
- **1 Banner**: Hero section content
- **4 Stats**: Homepage statistics
- **5 FAQs**: Sample questions
- **5 Social Platforms**: LinkedIn, Twitter, Facebook, GitHub, Instagram
- **5 Certification Categories**: ISO, Security, Quality, Industry Standards, Professional

## Support

Need help? Check:
- `README.md` - Full documentation
- `docs/API.md` - Complete API reference  
- `IMPLEMENTATION_SUMMARY.md` - What's been implemented
- GraphQL Playground - Interactive API explorer

---

**🎉 You're all set! Happy coding!**

