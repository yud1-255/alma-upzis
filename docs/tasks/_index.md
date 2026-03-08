# Task Index

## Current Status

**Active RFC:** RFC-002 — Simplified Self-Service Zakat (C6)
**Active Phase:** Not Started
**Blocked:** None

---

## RFC Dashboard

| RFC | Title | Progress | Tasks | Done | Status |
|-----|-------|----------|-------|------|--------|
| [002](./rfc-002/_index.md) | Simplified Self-Service Zakat | 0 of 8 | 8 | 0 | Not Started |

---

## RFC-002: Simplified Self-Service Zakat (C6)

### Phase 1: Auto Hijri Year + Database Migrations

**Exit Criteria:** Database schema supports C6 fields; Hijri year is auto-detected system-wide with admin override.

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [001](./rfc-002/001-database-migrations.md) | Database Migrations for C6 | Not Started | — |
| [002](./rfc-002/002-hijri-year-helper.md) | HijriYearHelper + Refactor + AppConfig UI | Not Started | 001 |

### Phase 2: Social Login

**Exit Criteria:** Users can register and login via Google and Facebook OAuth; account linking works; social login buttons on login/register pages.
**Requires:** Task 001 complete

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [003](./rfc-002/003-social-login-backend.md) | Social Login Backend — Socialite + Controller | Not Started | 001 |
| [004](./rfc-002/004-social-login-frontend.md) | Social Login Frontend — Vue Components | Not Started | 003 |

### Phase 3: Formulir Zakat Sederhana

**Exit Criteria:** Muzakki can submit zakat via simplified form; auto-created Family/Muzakki compatible with V1; UPZIS can confirm payments.
**Requires:** Phase 1 complete

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [005](./rfc-002/005-simple-zakat-backend.md) | SubmitSimpleZakat Use Case + Controller | Not Started | 001, 002 |
| [006](./rfc-002/006-simple-zakat-form-frontend.md) | SimpleZakat/Create.vue Form Page | Not Started | 005 |
| [007](./rfc-002/007-dashboard-integration.md) | Dashboard Integration + E2E Verification | Not Started | 005, 006 |

### Phase 4: PWA

**Exit Criteria:** Lighthouse PWA score > 80; app installable; standalone mode; static asset caching.
**Requires:** No strict dependency — can run in parallel with Phase 2/3

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [008](./rfc-002/008-pwa-setup.md) | PWA Setup — Manifest, Service Worker, Icons | Not Started | — |

---

## Dependency Graph

```
Phase 1:
001 (DB Migrations) ──▶ 002 (HijriYearHelper)

Phase 2:                          Phase 4:
001 ──▶ 003 (Social Backend)     008 (PWA) [independent]
             │
             └──▶ 004 (Social Frontend)

Phase 3:
001 ──┐
      ├──▶ 005 (SimpleZakat Backend) ──▶ 006 (Form Frontend) ──▶ 007 (Dashboard + E2E)
002 ──┘
```

## Critical Path

```
001 → 002 → 005 → 006 → 007
```

---

## Status Values

- **Not Started** — Ready to begin (dependencies met)
- **Blocked** — Waiting on dependency or decision
- **In Progress** — Actively being worked
- **Review** — Implementation done, under review
- **Done** — Merged and verified
