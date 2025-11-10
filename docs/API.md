# Gamma Neutral GraphQL API Documentation

## Overview

This document provides detailed information about all available GraphQL queries and mutations in the Gamma Neutral API.

## Base URL

```
POST /graphql
```

## Authentication

Most mutations require authentication. Include the bearer token in your headers:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Scalars

- `DateTime` - Date and time in format `Y-m-d H:i:s` (e.g., "2025-01-15 14:30:00")
- `JSON` - Arbitrary JSON data
- `Upload` - File upload (multipart/form-data)

## Enums

### JobType
- `FULL_TIME`
- `PART_TIME`
- `CONTRACT`

### JobStatus
- `ACTIVE`
- `CLOSED`

### ContactRequestStatus
- `NEW`
- `IN_PROGRESS`
- `RESOLVED`

### PostStatus
- `DRAFT`
- `PUBLISHED`

---

## Authentication

### Login

**Type**: Public Mutation

**Description**: Authenticate an administrator and receive an API token.

**Mutation**:
```graphql
mutation Login($email: String!, $password: String!) {
  login(input: { email: $email, password: $password }) {
    token
    administrator {
      id
      name
      email
      created_at
    }
  }
}
```

**Variables**:
```json
{
  "email": "admin@gammaneutral.com",
  "password": "password"
}
```

**Response**:
```json
{
  "data": {
    "login": {
      "token": "1|abc123...",
      "administrator": {
        "id": "1",
        "name": "Administrator",
        "email": "admin@gammaneutral.com",
        "created_at": "2025-10-17 10:00:00"
      }
    }
  }
}
```

### Logout

**Type**: Protected Mutation

**Description**: Revoke the current access token.

**Mutation**:
```graphql
mutation {
  logout {
    success
    message
  }
}
```

---

## Application

### Get Application

**Type**: Public Query

**Query**:
```graphql
query GetApplication($id: ID!) {
  application(id: $id) {
    id
    name
    logo_url
    settings
    created_at
    updated_at
  }
}
```

**Variables**:
```json
{
  "id": "1"
}
```

---

## Services

### List Services

**Type**: Public Query

**Query**:
```graphql
query GetServices($application_id: ID!, $is_active: Boolean) {
  services(application_id: $application_id, is_active: $is_active, limit: 50) {
    id
    title
    description
    icon
    category
    slug
    order
    is_active
    created_at
  }
}
```

### Get Single Service

**Query**:
```graphql
query GetService($id: ID!) {
  service(id: $id) {
    id
    title
    description
    icon
    category
    slug
    order
    is_active
  }
}
```

### Create Service

**Type**: Protected Mutation

**Mutation**:
```graphql
mutation CreateService($input: CreateServiceInput!) {
  createService(input: $input) {
    id
    title
    slug
    created_at
  }
}
```

**Variables**:
```json
{
  "input": {
    "application_id": "1",
    "title": "Machine Learning",
    "description": "Advanced ML solutions",
    "icon": "cpu",
    "category": "AI",
    "slug": "machine-learning",
    "order": 8,
    "is_active": true
  }
}
```

### Update Service

**Type**: Protected Mutation

**Mutation**:
```graphql
mutation UpdateService($id: ID!, $input: UpdateServiceInput!) {
  updateService(id: $id, input: $input) {
    id
    title
    description
  }
}
```

### Delete Service

**Type**: Protected Mutation

**Mutation**:
```graphql
mutation DeleteService($id: ID!) {
  deleteService(id: $id) {
    success
    message
  }
}
```

---

## Solutions

### List Solutions

**Type**: Public Query

**Query**:
```graphql
query GetSolutions($application_id: ID!) {
  solutions(application_id: $application_id, is_active: true) {
    id
    title
    subtitle
    description
    slug
    icon
    icon_color
    hero_image_url
    order
    features {
      id
      title
      description
      icon
      order
    }
    benefits {
      id
      title
      description
      icon
      order
    }
  }
}
```

### Get Solution by Slug

**Query**:
```graphql
query GetSolution($slug: String!) {
  solution(slug: $slug) {
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

### Create Solution

**Type**: Protected Mutation

**Mutation**:
```graphql
mutation CreateSolution($input: CreateSolutionInput!) {
  createSolution(input: $input) {
    id
    title
    slug
  }
}
```

---

## Partners

### List Partners

**Type**: Public Query

**Query**:
```graphql
query GetPartners($application_id: ID!, $page: Int, $first: Int) {
  partners(application_id: $application_id, is_active: true, page: $page, first: $first) {
    id
    name
    logo_url
    website_url
    order
  }
}
```

### CRUD Operations

Similar structure to Services with `createPartner`, `updatePartner`, `deletePartner`.

---

## Clients

### List Clients

**Type**: Public Query

**Query**:
```graphql
query GetClients($application_id: ID!) {
  clients(application_id: $application_id, is_active: true, limit: 100) {
    id
    name
    logo_url
    industry
    website_url
    order
  }
}
```

---

## Testimonials

### List Testimonials

**Type**: Public Query

**Query**:
```graphql
query GetTestimonials($application_id: ID!, $page: Int) {
  testimonials(application_id: $application_id, is_active: true, page: $page, first: 10) {
    id
    name
    content
    image_url
    position
    company
    rating
    order
  }
}
```

---

## Banners

### List Banners

**Type**: Public Query

**Query**:
```graphql
query GetBanners($application_id: ID!) {
  banners(application_id: $application_id, is_active: true, first: 10) {
    id
    title
    subtitle
    image_url
    cta_text
    cta_url
    order
  }
}
```

---

## Team Members

### List Team Members

**Type**: Public Query

**Query**:
```graphql
query GetTeam($application_id: ID!) {
  teams(application_id: $application_id, is_active: true, first: 20) {
    id
    name
    role
    email
    contact
    biography
    profile_picture_url
    order
    socialMediaLinks {
      id
      url
      platform {
        name
        icon
        base_url
      }
    }
  }
}
```

---

## Certifications

### List Certifications

**Type**: Public Query

**Query**:
```graphql
query GetCertifications($application_id: ID!) {
  certifications(application_id: $application_id, is_active: true) {
    id
    title
    file_url
    issued_date
    category {
      id
      name
    }
  }
}
```

---

## Job Positions

### List Job Positions

**Type**: Public Query

**Query**:
```graphql
query GetJobPositions($application_id: ID!, $status: JobStatus) {
  jobPositions(application_id: $application_id, status: $status) {
    id
    title
    department
    location
    job_type
    is_remote
    salary_range
    experience_required
    summary
    posted_date
    status
  }
}
```

### Get Job Position Details

**Query**:
```graphql
query GetJobPosition($id: ID!) {
  jobPosition(id: $id) {
    id
    title
    department
    location
    job_type
    is_remote
    salary_range
    experience_required
    summary
    description
    responsibilities
    requirements
    nice_to_have
    benefits
    skills
    posted_date
    status
  }
}
```

---

## Contact Requests

### Submit Contact Request

**Type**: Public Mutation

**Mutation**:
```graphql
mutation CreateContactRequest($input: CreateContactRequestInput!) {
  createContactRequest(input: $input) {
    id
    first_name
    last_name
    email
    status
    created_at
  }
}
```

**Variables**:
```json
{
  "input": {
    "application_id": "1",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "subject": "Inquiry about services",
    "message": "I would like to know more about your AI offerings."
  }
}
```

---

## FAQs

### List FAQs

**Type**: Public Query

**Query**:
```graphql
query GetFAQs($application_id: ID!, $category: String) {
  faqs(application_id: $application_id, category: $category, is_active: true) {
    id
    question
    answer
    category
    order
  }
}
```

---

## Blog Posts

### List Blog Posts

**Type**: Public Query

**Query**:
```graphql
query GetBlogPosts($application_id: ID!, $status: PostStatus) {
  blogPosts(application_id: $application_id, status: $status, limit: 50) {
    id
    title
    slug
    excerpt
    featured_image_url
    category
    tags
    published_at
    view_count
    author {
      id
      name
      profile_picture_url
    }
  }
}
```

### Get Blog Post by Slug

**Query**:
```graphql
query GetBlogPost($slug: String!) {
  blogPost(slug: $slug) {
    id
    title
    slug
    excerpt
    content
    featured_image_url
    category
    tags
    published_at
    view_count
    author {
      name
      profile_picture_url
      biography
    }
  }
}
```

---

## Projects

### List Projects

**Type**: Public Query

**Query**:
```graphql
query GetProjects($application_id: ID!) {
  projects(application_id: $application_id, status: PUBLISHED, limit: 50) {
    id
    title
    slug
    description
    featured_image_url
    client_name
    industry
    technologies
    completion_date
  }
}
```

### Get Project by Slug

**Query**:
```graphql
query GetProject($slug: String!) {
  project(slug: $slug) {
    id
    title
    slug
    description
    challenge
    solution
    results
    featured_image_url
    gallery_images
    client_name
    industry
    technologies
    completion_date
  }
}
```

---

## Stats

### List Stats

**Type**: Public Query

**Query**:
```graphql
query GetStats($application_id: ID!) {
  stats(application_id: $application_id, is_active: true) {
    id
    label
    value
    unit
    icon
    order
  }
}
```

---

## Error Handling

All errors follow the GraphQL error format:

```json
{
  "errors": [
    {
      "message": "Invalid credentials",
      "extensions": {
        "category": "authentication"
      }
    }
  ]
}
```

Common error categories:
- `validation` - Input validation errors
- `authentication` - Authentication required or failed
- `authorization` - Insufficient permissions
- `graphql` - GraphQL syntax or schema errors

---

## Pagination

Paginated queries support these arguments:
- `first`: Number of items per page (default: 10-15 depending on entity)
- `page`: Page number (1-based)

Paginated responses include:
- `data`: Array of items
- `paginatorInfo`: Pagination metadata (if using @paginate directive)

---

## File Uploads

File fields accept:
1. Full URLs (for external resources)
2. S3 paths (for existing files)
3. Upload scalar (for new file uploads via multipart/form-data)

All file URL fields in responses return full S3 URLs automatically.

---

## Rate Limits

- Contact form: 5 requests per 10 minutes per IP
- General API: Laravel's default rate limiting applies

---

## Support

For API support, contact: dev@gammaneutral.com

