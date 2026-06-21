# Contributing

Thank you for your interest in contributing to PHP API Kit!

## Getting started

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/my-feature`
3. Make your changes
4. Open a Pull Request

## Branch naming

| Type | Prefix | Example |
|------|--------|---------|
| Feature | `feat/` | `feat/oauth-support` |
| Bug fix | `fix/` | `fix/rate-limit-reset` |
| Documentation | `docs/` | `docs/nginx-example` |
| Refactor | `refactor/` | `refactor/router` |

## Commit messages

Use short, imperative messages:

```
feat: add OAuth2 support
fix: reset rate limit counter on window expiry
docs: add Docker Compose example
```

## Code style

- PHP 8.1+
- `declare(strict_types=1)` in every file
- Classes in `PascalCase`, methods in `camelCase`
- 4-space indentation
- No trailing whitespace

## Before submitting

- [ ] Run `find . -name "*.php" | xargs php -l` — zero syntax errors
- [ ] Zero new external dependencies
- [ ] Update `CHANGELOG.md` under `[Unreleased]`
- [ ] Update `README.md` if you added or changed an endpoint

## Reporting bugs

Use the [bug report template](.github/ISSUE_TEMPLATE/bug_report.md).
