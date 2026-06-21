# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.x     | ✅        |

## Reporting a Vulnerability

**Do not open a public GitHub issue for security vulnerabilities.**

Email **blascooscar36@gmail.com** with:

- A description of the vulnerability
- Steps to reproduce
- Potential impact

You will receive a response within 48 hours.

## Security considerations when using this project

- **Change `JWT_SECRET`** to a random 32+ byte value before deploying: `php -r "echo bin2hex(random_bytes(32));"`
- **Never commit `.env`** — it is listed in `.gitignore`
- **Use HTTPS** in production — JWT tokens must be transmitted securely
- **Set `APP_DEBUG=false`** in production to avoid leaking stack traces
- **Run migrations** as a restricted DB user — do not use `root` in production
- **Set `CORS_ORIGINS`** to your actual frontend domain — avoid `*` in production
