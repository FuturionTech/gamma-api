# Gamma Neutral API - Laravel GraphQL Backend

A complete GraphQL API backend built with Laravel 11, Lighthouse GraphQL, and PostgreSQL to power the Gamma Neutral Consulting website.

## Features

- **Complete GraphQL API** with 19+ entity types
- **Modular Schema Organization** - Each entity has its own GraphQL file
- **Authentication** - Sanctum-based API token authentication for administrators
- **File Upload** - AWS S3 integration with automatic URL resolution
- **Email Notifications** - Queue-based email system for contact requests
- **Database Seeders** - Pre-populated with sample data from company specifications
- **Comprehensive Models** - With relationships, scopes, and accessors

## Tech Stack

- **Laravel 11** - PHP Framework
- **Lighthouse GraphQL 6** - GraphQL Server
- **PostgreSQL** - Database
- **AWS S3** - File Storage
- **Laravel Sanctum** - API Authentication
- **Redis/Predis** - Caching and Queue
- **Spatie Laravel Permission** - Role/Permission management

## System Requirements

- PHP 8.2+
- Composer
- PostgreSQL 12+
- Redis (optional, recommended for production)
- AWS S3 Account

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd gamma-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

### 4. Configure Database

Update your `.env` file with PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gamma_api
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Configure AWS S3

Add your AWS credentials to `.env`:

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
FILESYSTEM_DISK=s3
```

### 6. Configure Mail

For development, you can use log driver:

```env
MAIL_MAILER=log
```

For production, configure SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@gammaneutral.com
MAIL_FROM_NAME="Gamma Neutral"
```

### 7. Generate Application Key

```bash
php artisan key:generate
```

### 8. Run Migrations

```bash
php artisan migrate
```

### 9. Seed the Database

```bash
php artisan db:seed
```

This will create:
- Gamma Neutral Application record
- Default administrator (admin@gammaneutral.com / password)
- 7 Services (AI, Data Engineering, Cybersecurity, etc.)
- 6 Industry Solutions with features and benefits
- Social media platforms and certification categories
- Sample banners, stats, and FAQs

## API Usage

### GraphQL Endpoint

```
POST /graphql
```

### GraphQL Playground (Development)

Visit `/graphiql` in your browser for an interactive GraphQL playground.

### Authentication

#### Login

```graphql
mutation {
  login(input: {
    email: "admin@gammaneutral.com"
    password: "password"
  }) {
    token
    administrator {
      id
      name
      email
    }
  }
}
```

#### Using the Token

Include the token in your request headers:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### Logout

```graphql
mutation {
  logout {
    success
    message
  }
}
```

## GraphQL Schema Organization

The GraphQL schema is organized into modular files:

```
graphql/
├── schema.graphql              # Main schema (imports all)
├── common/
│   ├── scalars.graphql         # DateTime, JSON, Upload
│   ├── enums.graphql           # JobType, JobStatus, etc.
│   └── responses.graphql       # Common response types
└── entities/
    ├── application.graphql     # Application type and queries
    ├── auth.graphql            # Authentication mutations
    ├── service.graphql         # Services CRUD
    ├── solution.graphql        # Solutions with features/benefits
    ├── partner.graphql         # Partners/sponsors
    ├── client.graphql          # Client logos
    ├── testimonial.graphql     # Customer testimonials
    ├── banner.graphql          # Hero banners
    ├── team.graphql            # Team members with social links
    ├── certification.graphql   # Certifications and categories
    ├── job.graphql             # Job positions/careers
    ├── contact.graphql         # Contact form submissions
    ├── faq.graphql             # FAQs
    ├── blog.graphql            # Blog posts
    ├── project.graphql         # Case studies/portfolio
    └── stat.graphql            # Homepage statistics
```

## API Examples

### Query Services

```graphql
query {
  services(application_id: 1, is_active: true) {
    id
    title
    description
    icon
    category
    slug
  }
}
```

### Query Solutions with Features

```graphql
query {
  solutions(application_id: 1) {
    id
    title
    subtitle
    description
    features {
      title
      description
      icon
    }
    benefits {
      title
      description
      icon
    }
  }
}
```

### Create Service (Admin Only)

```graphql
mutation {
  createService(input: {
    application_id: 1
    title: "Machine Learning"
    description: "Advanced ML solutions"
    icon: "cpu"
    category: "AI"
    slug: "machine-learning"
    order: 8
    is_active: true
  }) {
    id
    title
    slug
  }
}
```

### Submit Contact Request (Public)

```graphql
mutation {
  createContactRequest(input: {
    application_id: 1
    first_name: "John"
    last_name: "Doe"
    email: "john@example.com"
    phone: "+1234567890"
    subject: "Inquiry about AI services"
    message: "I would like to know more about your AI offerings."
  }) {
    id
    status
    created_at
  }
}
```

## Database Schema

### Core Tables
- `applications` - Application/website container
- `administrators` - Admin users with Sanctum tokens
- `social_media_platforms` - Social platform types
- `certification_categories` - Certification categories

### Content Tables (all with application_id FK)
- `services` - Service offerings
- `solutions` - Industry solutions
- `solution_features` - Features per solution
- `solution_benefits` - Benefits per solution
- `partners` - Partners/sponsors
- `clients` - Client logos
- `testimonials` - Customer testimonials
- `banners` - Hero section banners
- `teams` - Team members
- `team_social_media_links` - Team social links
- `certifications` - Company certifications
- `job_positions` - Career opportunities
- `contact_requests` - Form submissions
- `faqs` - FAQ entries
- `blog_posts` - Blog articles
- `projects` - Case studies/portfolio
- `stats` - Homepage statistics

## File Uploads

Files are automatically uploaded to AWS S3 with public access. The models automatically convert S3 paths to full URLs using accessors.

### Upload Example

When creating/updating entities with file fields, you can either:
1. Provide a full URL (external)
2. Provide a relative S3 path
3. Upload a file using the Upload scalar (requires multipart/form-data)

## Queue Configuration

The application uses database queues by default. To process queued jobs:

```bash
php artisan queue:work
```

For production, set up a supervisor or use Laravel Horizon:

```bash
php artisan horizon
```

## Testing

Run the test suite:

```bash
php artisan test
```

With coverage:

```bash
php artisan test --coverage
```

## Rate Limiting

- Contact form: 5 requests per 10 minutes per IP
- Authentication: Standard Laravel rate limits

## CORS Configuration

CORS is configured via Sanctum middleware. Update `SANCTUM_STATEFUL_DOMAINS` in `.env`:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,gamma-web.vercel.app
FRONTEND_URL=http://localhost:3000
```

## Development

### Start Development Server

```bash
php artisan serve
```

### Watch Queue Jobs

```bash
php artisan queue:listen
```

### Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure Redis for cache and queue
3. Set up queue workers with Supervisor
4. Enable OPcache
5. Configure proper AWS S3 bucket policies
6. Set up SSL/TLS for HTTPS
7. Configure proper CORS domains

## Frontend Integration

### Nuxt.js Configuration

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      graphqlEndpoint: 'https://api.gammaneutral.com/graphql',
      applicationId: '1',
    }
  }
})
```

### Apollo Client Example

```typescript
const client = new ApolloClient({
  uri: 'https://api.gammaneutral.com/graphql',
  headers: {
    Authorization: `Bearer ${token}`,
  },
})
```

## API Documentation

For detailed API documentation, import the Postman collection from `docs/gamma-api.postman_collection.json` (to be created).

## Support & Contact

- **Email**: info@gammaneutral.com
- **Address**: 108 Redpath Ave, Suite 19, Toronto, ON M4S 2J7, Canada

## License

Proprietary - Gamma Neutral Consulting Inc.

## Credits

Developed for Gamma Neutral Consulting Inc. using Laravel, Lighthouse GraphQL, and modern web technologies.
