# PHP API Kit

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)
![CI](https://github.com/Oscarr36/php-api-kit/actions/workflows/php-lint.yml/badge.svg)
![CodeQL](https://github.com/Oscarr36/php-api-kit/actions/workflows/codeql.yml/badge.svg)
![Zero dependencies](https://img.shields.io/badge/dependencies-zero-brightgreen)

A production-ready PHP REST API boilerplate with **zero Composer dependencies**. Clone, configure, and ship.

---

## Features

| Feature | Details |
|---|---|
| **Router** | Method + path matching, named URL params `{id}` |
| **JWT Auth** | HS256, configurable TTL, refresh endpoint |
| **Middleware** | Auth, CORS, Rate Limiting (file-based, per IP) |
| **Validation** | Chainable validator: required, email, min, max, in, numeric |
| **Database** | PDO singleton with prepared statements |
| **Responses** | Consistent JSON: `success`, `error`, `paginated` |
| **Security headers** | X-Content-Type-Options, X-Frame-Options, XSS-Protection |
| **Role system** | `admin` / `user` roles, enforced per endpoint |
| **Health check** | `GET /api/health` — zero-dependency uptime monitor |

---

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` OR Nginx

---

## Quickstart

```bash
# 1. Clone
git clone https://github.com/Oscarr36/php-api-kit.git
cd php-api-kit

# 2. Configure environment
cp .env.example .env
# Edit .env with your DB credentials and a strong JWT_SECRET

# 3. Run the migration
mysql -u root -p your_database < database/migrations/001_create_users_table.sql

# 4. Point your web server document root to /public
# Apache: enable mod_rewrite and AllowOverride All
# Nginx: see the Nginx config example below
```

---

## API Reference

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/auth/register` | — | Create account |
| `POST` | `/api/auth/login` | — | Login, receive token |
| `GET`  | `/api/auth/me` | Bearer | Current user |
| `POST` | `/api/auth/refresh` | Bearer | Refresh token |

### Users

| Method | Endpoint | Auth | Role |
|--------|----------|------|------|
| `GET`    | `/api/users` | Bearer | admin |
| `GET`    | `/api/users/{id}` | Bearer | admin or self |
| `PUT`    | `/api/users/{id}` | Bearer | admin or self |
| `DELETE` | `/api/users/{id}` | Bearer | admin |

### Health

| Method | Endpoint | Auth |
|--------|----------|------|
| `GET` | `/api/health` | — |

---

## Example Requests

```bash
# Register
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"secret123"}'

# Login
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"secret123"}'

# Get current user (replace TOKEN)
curl http://localhost/api/auth/me \
  -H "Authorization: Bearer TOKEN"

# List users (admin only)
curl http://localhost/api/users?page=1&per_page=20 \
  -H "Authorization: Bearer TOKEN"
```

### Response format

**Success**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "role": "user" },
    "token": "eyJ..."
  }
}
```

**Error**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The field 'email' must be a valid email address."]
  }
}
```

**Paginated**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "total": 42,
    "page": 1,
    "per_page": 20,
    "total_pages": 3,
    "has_more": true
  }
}
```

---

## Project Structure

```
php-api-kit/
├── config/
│   └── app.php                     # App + DB + JWT + CORS config
├── database/
│   └── migrations/
│       └── 001_create_users_table.sql
├── public/
│   ├── .htaccess                   # Apache rewrite rules
│   └── index.php                   # Entry point + route definitions
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php      # register / login / me / refresh
│   │   └── UserController.php      # index / show / update / destroy
│   ├── Core/
│   │   ├── App.php                 # Bootstrap, env loading, middleware
│   │   ├── Database.php            # PDO singleton
│   │   ├── Request.php             # HTTP request wrapper
│   │   ├── Response.php            # JSON response helpers
│   │   └── Router.php              # HTTP router
│   ├── Helpers/
│   │   ├── JWT.php                 # HS256 encode/decode (no deps)
│   │   └── Validator.php           # Chainable input validator
│   ├── Middleware/
│   │   ├── AuthMiddleware.php      # JWT token verification
│   │   ├── CorsMiddleware.php      # CORS headers + preflight
│   │   └── RateLimitMiddleware.php # IP-based rate limiting
│   └── Models/
│       └── User.php                # User CRUD with PDO
├── .env.example
├── .gitignore
├── CHANGELOG.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
└── SECURITY.md
```

---

## Adding a New Resource

**1. Create the model** (`src/Models/Product.php`)

```php
class Product {
    public function __construct(private readonly PDO $db) {}

    public function all(): array {
        return $this->db->query('SELECT * FROM products')->fetchAll();
    }
}
```

**2. Create the controller** (`src/Controllers/ProductController.php`)

```php
class ProductController {
    private PDO $db;
    public function __construct(array $config) {
        $this->config = $config;
        $this->db = Database::connect($config['db']);
    }
    public function index(Request $req, Response $res): void {
        $res->success((new Product($this->db))->all());
    }
}
```

**3. Register the route** (`public/index.php`)

```php
$router->get('/api/products', [ProductController::class, 'index']);
```

---

## Rate Limiting

Default: **60 requests / 60 seconds per IP**. Configure via `.env`:

```
RATE_LIMIT_MAX=100
RATE_LIMIT_WINDOW=60
```

Response headers on every request:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1719014460
```

---

## Nginx Config

```nginx
server {
    listen 80;
    server_name api.example.com;
    root /var/www/php-api-kit/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

See [SECURITY.md](SECURITY.md).

## License

MIT — see [LICENSE](LICENSE).
