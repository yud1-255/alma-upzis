# Architecture Decision Records

## Decisions

| ADR | Decision | Status | Date |
|-----|----------|--------|------|
| [001](./001-laravel-php-framework.md) | Laravel 8 + PHP sebagai Backend Framework | Accepted | 2026-02-27 |
| [002](./002-mysql-database.md) | MySQL sebagai Database | Accepted | 2026-02-27 |
| [003](./003-inertia-vue-frontend.md) | Inertia.js + Vue 3 sebagai Frontend Strategy | Accepted | 2026-02-27 |
| [004](./004-domain-layer-pattern.md) | Domain Layer untuk Pemisahan Logika Bisnis | Accepted | 2026-02-27 |
| [005](./005-soft-delete-audit-logging.md) | Soft Delete dan Audit Logging untuk Transaksi | Accepted | 2026-02-27 |
| [006](./006-use-case-layer.md) | Use Case Layer untuk Orkestrasi Lintas-Domain | Accepted | 2026-03-08 |
| [007](./007-social-login-socialite.md) | Laravel Socialite dengan Penyimpanan Kolom di Tabel Users | Accepted | 2026-03-08 |

## Superseded

| ADR | Decision | Superseded By |
|-----|----------|---------------|
| | | |

---

## What Goes Here

ADRs capture significant technical decisions:
- Language/framework selection
- Database/infrastructure choices
- Architectural patterns adopted
- Security approaches
- Third-party service selections

**Not ADRs:** Implementation details, bug fixes, refactoring approaches.

## Creating New ADR

1. Copy `000-template.md` to `[NNN]-[slug].md`
2. Fill in context and decision
3. Add to index
4. Review with team (if applicable)
5. Update status when decided
