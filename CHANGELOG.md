# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] — 2024

### Added
- HTTP Router with named URL parameters `{id}` and closure support
- Request wrapper (body, params, bearer token, IP)
- Response helpers: `json`, `success`, `error`, `paginated`
- PDO singleton database connection
- JWT HS256 encode/decode with no external dependencies
- Chainable Validator (required, email, min, max, in, numeric)
- AuthMiddleware — verifies Bearer JWT on protected routes
- CorsMiddleware — configurable origins, preflight handling
- RateLimitMiddleware — file-based per-IP rate limiting with headers
- AuthController: `register`, `login`, `me`, `refresh`
- UserController: `index` (admin), `show`, `update`, `destroy`
- User model with full CRUD and PDO prepared statements
- Role system: `admin` / `user`
- `GET /api/health` endpoint
- Apache `.htaccess` with rewrite rules and security headers
- Nginx config example in README
- `.env` file loading (no external library required)
- Migration: `001_create_users_table.sql`
- Complete documentation with examples
