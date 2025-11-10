# Laravel GraphQL API Backend for Gamma Neutral Website

## Database Schema Design

Create PostgreSQL database with the following tables and relationships:

### Core Application Table

- `applications` - main application/website container
  - id, name, logo_url, settings (JSON), created_at, updated_at

### Content Management Tables

1. **services** - AI, Data Engineering, Cybersecurity, etc.

   - id, application_id (FK), title, description, icon, category, slug, order, is_active, created_at, updated_at
   - Index on application_id, slug

2. **solutions** - Financial Services, Healthcare, Education industries

   - id, application_id (FK), title, subtitle, description, slug, icon, icon_color, hero_image_url, order, is_active, created_at, updated_at
   - Index on application_id, slug

3. **solution_features** - Features for each solution

   - id, solution_id (FK), title, description, icon, order, created_at, updated_at

4. **solution_benefits** - Benefits for each solution

   - id, solution_id (FK), title, description, icon, order, created_at, updated_at

5. **partners** - Company partners/sponsors

   - id, application_id (FK), name, logo_url, website_url, order, is_active, created_at, updated_at

6. **clients** - Client logos/testimonials (separate from partners)

   - id, application_id (FK), name, logo_url, industry, website_url, order, is_active, created_at, updated_at

7. **testimonials** - Client testimonials

   - id, application_id (FK), name, content, image_url, position, company, rating (1-5), order, is_active, created_at, updated_at
   - Already has partial query in services/graphql/queries.gql

8. **banners** - Hero section banners

   - id, application_id (FK), title, subtitle, image_url, cta_text, cta_url, order, is_active, created_at, updated_at
   - Already has query in homepage/graphql/queries.gql

9. **teams** - Team members/staff

   - id, application_id (FK), name, role, email, contact, biography, profile_picture_url, order, is_active, created_at, updated_at
   - Already has query in aboutus/graphql/queries.gql

10. **social_media_platforms** - Platform types (LinkedIn, Twitter, etc.)

    - id, name, icon, base_url, created_at, updated_at

11. **team_social_media_links** - Team member social links

    - id, team_id (FK), platform_id (FK), url, created_at, updated_at

12. **certifications** - Company certifications/awards

    - id, application_id (FK), title, file_url, certification_category_id (FK), issued_date, is_active, created_at, updated_at
    - Already has query in aboutus/graphql/queries.gql

13. **certification_categories** - Categories for certifications

    - id, name, created_at, updated_at

14. **job_positions** - Career opportunities

    - id, application_id (FK), title, department, location, job_type (enum: full-time, part-time, contract), is_remote, salary_range, experience_required, summary, description, responsibilities (JSON), requirements (JSON), nice_to_have (JSON), benefits (JSON), skills (JSON), posted_date, status (enum: active, closed), created_at, updated_at

15. **contact_requests** - Form submissions

    - id, application_id (FK), first_name, last_name, email, phone (nullable), subject (nullable), message, status (enum: new, in_progress, resolved), created_at, updated_at
    - Already has mutation in contactus/graphql/contact.gql

16. **faqs** - Frequently Asked Questions

    - id, application_id (FK), question, answer, category, order, is_active, created_at, updated_at

17. **blog_posts** - Blog articles

    - id, application_id (FK), title, slug, excerpt, content (text), featured_image_url, author_id (FK to teams), category, tags (JSON), status (enum: draft, published), published_at, view_count, created_at, updated_at

18. **projects** - Case studies/portfolio projects

    - id, application_id (FK), title, slug, description, challenge, solution, results, featured_image_url, gallery_images (JSON), client_name, industry, technologies (JSON), status (enum: draft, published), completion_date, created_at, updated_at

19. **stats** - Statistics/metrics displayed on homepage

    - id, application_id (FK), label, value, unit (e.g., "+", "%"), icon, order, is_active, created_at, updated_at

## Laravel Setup & Configuration

### Dependencies & Packages

```json
{
  "nuwave/lighthouse": "^6.0",
  "laravel/sanctum": "^4.0",
  "intervention/image": "^3.0",
  "spatie/laravel-permission": "^6.0"
}
```

### Environment Configuration

- Database: PostgreSQL connection
- Authentication: Laravel Sanctum for admin API tokens
- File Storage: Configure S3 or local storage for uploads
- CORS: Allow frontend domain (gamma-web)

## GraphQL Schema (schema.graphql)

### Custom Scalars & Directives

```graphql
scalar DateTime
scalar JSON

directive @auth on FIELD_DEFINITION
```

### Enums

```graphql
enum JobType {
  FULL_TIME
  PART_TIME
  CONTRACT
}

enum JobStatus {
  ACTIVE
  CLOSED
}

enum ContactRequestStatus {
  NEW
  IN_PROGRESS
  RESOLVED
}

enum PostStatus {
  DRAFT
  PUBLISHED
}
```

### Types for Each Entity

Define GraphQL types matching each database table with proper relations

### Queries

```graphql
type Query {
  # Application
  application(id: ID!): Application
  
  # Services
  services(application_id: ID!, limit: Int, is_active: Boolean): [Service!]!
  service(id: ID!): Service
  
  # Solutions
  solutions(application_id: ID!, limit: Int): [Solution!]!
  solution(slug: String!): Solution
  
  # Partners
  partners(application_id: ID!): PartnerPagination
  
  # Clients
  clients(application_id: ID!, limit: Int): [Client!]!
  
  # Testimonials
  testimonials(application_id: ID!, limit: Int!): TestimonialPagination
  
  # Banners
  banners(application_id: ID!): BannerPagination
  
  # Teams
  teams(application_id: ID!): TeamPagination
  
  # Certifications
  certifications(application_id: ID!): CertificationPagination
  
  # Job Positions
  jobPositions(application_id: ID!, status: JobStatus): [JobPosition!]!
  jobPosition(id: ID!): JobPosition
  
  # FAQs
  faqs(application_id: ID!, category: String): [FAQ!]!
  
  # Blog
  blogPosts(application_id: ID!, status: PostStatus, limit: Int): [BlogPost!]!
  blogPost(slug: String!): BlogPost
  
  # Projects
  projects(application_id: ID!, limit: Int): [Project!]!
  project(slug: String!): Project
  
  # Stats
  stats(application_id: ID!): [Stat!]!
}
```

### Mutations

```graphql
type Mutation {
  # Contact Request (Public)
  createContactRequest(input: CreateContactRequestInput!): ContactRequest!
  
  # Services (Admin only - @auth)
  createService(input: CreateServiceInput!): Service! @auth
  updateService(id: ID!, input: UpdateServiceInput!): Service! @auth
  deleteService(id: ID!): DeleteResponse! @auth
  
  # Solutions (Admin only)
  createSolution(input: CreateSolutionInput!): Solution! @auth
  updateSolution(id: ID!, input: UpdateSolutionInput!): Solution! @auth
  deleteSolution(id: ID!): DeleteResponse! @auth
  
  # Partners (Admin only)
  createPartner(input: CreatePartnerInput!): Partner! @auth
  updatePartner(id: ID!, input: UpdatePartnerInput!): Partner! @auth
  deletePartner(id: ID!): DeleteResponse! @auth
  
  # Clients (Admin only)
  createClient(input: CreateClientInput!): Client! @auth
  updateClient(id: ID!, input: UpdateClientInput!): Client! @auth
  deleteClient(id: ID!): DeleteResponse! @auth
  
  # Testimonials (Admin only)
  createTestimonial(input: CreateTestimonialInput!): Testimonial! @auth
  updateTestimonial(id: ID!, input: UpdateTestimonialInput!): Testimonial! @auth
  deleteTestimonial(id: ID!): DeleteResponse! @auth
  
  # Banners (Admin only)
  createBanner(input: CreateBannerInput!): Banner! @auth
  updateBanner(id: ID!, input: UpdateBannerInput!): Banner! @auth
  deleteBanner(id: ID!): DeleteResponse! @auth
  
  # Teams (Admin only)
  createTeam(input: CreateTeamInput!): Team! @auth
  updateTeam(id: ID!, input: UpdateTeamInput!): Team! @auth
  deleteTeam(id: ID!): DeleteResponse! @auth
  
  # Certifications (Admin only)
  createCertification(input: CreateCertificationInput!): Certification! @auth
  updateCertification(id: ID!, input: UpdateCertificationInput!): Certification! @auth
  deleteCertification(id: ID!): DeleteResponse! @auth
  
  # Job Positions (Admin only)
  createJobPosition(input: CreateJobPositionInput!): JobPosition! @auth
  updateJobPosition(id: ID!, input: UpdateJobPositionInput!): JobPosition! @auth
  deleteJobPosition(id: ID!): DeleteResponse! @auth
  
  # FAQs (Admin only)
  createFAQ(input: CreateFAQInput!): FAQ! @auth
  updateFAQ(id: ID!, input: UpdateFAQInput!): FAQ! @auth
  deleteFAQ(id: ID!): DeleteResponse! @auth
  
  # Blog Posts (Admin only)
  createBlogPost(input: CreateBlogPostInput!): BlogPost! @auth
  updateBlogPost(id: ID!, input: UpdateBlogPostInput!): BlogPost! @auth
  deleteBlogPost(id: ID!): DeleteResponse! @auth
  
  # Projects (Admin only)
  createProject(input: CreateProjectInput!): Project! @auth
  updateProject(id: ID!, input: UpdateProjectInput!): Project! @auth
  deleteProject(id: ID!): DeleteResponse! @auth
  
  # Stats (Admin only)
  createStat(input: CreateStatInput!): Stat! @auth
  updateStat(id: ID!, input: UpdateStatInput!): Stat! @auth
  deleteStat(id: ID!): DeleteResponse! @auth
  
  # Authentication
  login(email: String!, password: String!): AuthPayload!
  logout: LogoutResponse! @auth
}
```

### Input Types

Define all necessary input types for create and update operations

## Laravel Models & Relationships

Create Eloquent models for each table with:

- Proper fillable fields
- Casts for JSON columns
- Relationships (hasMany, belongsTo, belongsToMany)
- Scopes for filtering (active, byApplication)
- Accessors for computed fields (imageUrl with full path)

## Authentication & Authorization

### Admin Authentication

- Use Laravel Sanctum for API token authentication
- Create Admin model/table or use User with role
- Implement login mutation returning token
- Create @auth directive in Lighthouse
- Protect all mutations except createContactRequest

### Middleware

- CORS middleware for frontend domain
- Sanctum authentication middleware
- Rate limiting for contact form

## File Upload Handling

### Image Storage

- Store in `storage/app/public/uploads/{entity}/`
- Create symbolic link for public access
- Validate file types (jpg, png, svg, pdf)
- Resize/optimize images using Intervention Image
- Return full URL in GraphQL responses via accessor

### Implementation

- Create FileUploadService for reusable upload logic
- Handle file cleanup on update/delete
- Support both direct file uploads and URL references

## Database Seeders

Create seeders for:

1. Application (Gamma Neutral)
2. Sample Services (7 items from gamma_company.md)
3. Sample Solutions (6 industries from solutions.ts)
4. Social Media Platforms
5. Certification Categories
6. Admin User

## API Testing

### GraphQL Playground

- Enable in local/dev environment
- Configure endpoint: `/graphql`
- Add authentication header support

### Test Data

- Seed database with realistic test data
- Match structure from current static data files

## Documentation Requirements

### README.md sections:

1. Installation & Setup
2. Database migrations
3. Environment variables
4. GraphQL endpoints
5. Authentication flow
6. File upload format
7. API examples for each entity

### API Documentation:

- Document all queries with examples
- Document all mutations with input examples
- Document authentication process
- Include Postman/Insomnia collection export

## Deployment Considerations

- Queue jobs for email notifications (contact form)
- Cache frequently accessed data (services, solutions)
- Optimize database queries with eager loading
- Index foreign keys and frequently queried columns
- Configure backup strategy for PostgreSQL
- Set up logging for GraphQL errors

## Migration Strategy

Provide SQL export or seeder to populate initial data from:

- `domains/services/pages/services.vue` (7 services)
- `domains/solutions/data/solutions.ts` (6 solutions with full data)
- `domains/careers/data/positions.ts` (job positions)

## Frontend Integration Notes

Update Nuxt.js GraphQL client configuration:

- Point to new Laravel API endpoint
- Update APPLICATION_ID environment variable
- Extend existing queries with new fields
- Create new queries for entities that don't exist yet (clients, faqs, blog, projects, stats)