# RFC-002: Simplified Self-Service Zakat — Tasks

**RFC:** [002-simplified-self-service.md](../../rfc/002-simplified-self-service.md)
**Capability:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
**Status:** Not Started
**Total Tasks:** 8

---

## Phase 1: Auto Hijri Year + Database Migrations

**Exit Criteria:** `HijriYearHelper::current()` returns correct Hijri year; all V1 hijri_year references go through helper; database schema supports C6 fields (social login columns, family email, nullable address fields).

| Task | Title | Status | Depends On | Story |
|------|-------|--------|------------|-------|
| [001](./001-database-migrations.md) | Database Migrations for C6 | Not Started | — | N/A |
| [002](./002-hijri-year-helper.md) | HijriYearHelper + Refactor + AppConfig UI | Not Started | 001 | [auto-hijri-year](../../stories/simplified-self-service/auto-hijri-year.md), [override-hijri-year](../../stories/simplified-self-service/override-hijri-year.md) |

## Phase 2: Social Login

**Exit Criteria:** Users can register and login via Google and Facebook OAuth; account linking works for existing emails; social login buttons visible on login and register pages.
**Requires:** Phase 1 complete (migration for `social_id`/`social_type` columns)

| Task | Title | Status | Depends On | Story |
|------|-------|--------|------------|-------|
| [003](./003-social-login-backend.md) | Social Login Backend — Socialite + Controller + Routes | Not Started | 001 | [social-login-registrasi](../../stories/simplified-self-service/social-login-registrasi.md), [social-login-account-linking](../../stories/simplified-self-service/social-login-account-linking.md) |
| [004](./004-social-login-frontend.md) | Social Login Frontend — Vue Components | Not Started | 003 | [social-login-registrasi](../../stories/simplified-self-service/social-login-registrasi.md) |

## Phase 3: Formulir Zakat Sederhana

**Exit Criteria:** Muzakki baru can submit zakat via simplified form in < 3 minutes; Family and Muzakki auto-created and compatible with V1; transactions identical to V1; UPZIS can confirm payments.
**Requires:** Phase 1 complete (nullable fields, family email)

| Task | Title | Status | Depends On | Story |
|------|-------|--------|------------|-------|
| [005](./005-simple-zakat-backend.md) | SubmitSimpleZakat Use Case + Controller | Not Started | 001, 002 | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [auto-create-keluarga](../../stories/simplified-self-service/auto-create-keluarga.md) |
| [006](./006-simple-zakat-form-frontend.md) | SimpleZakat/Create.vue Form Page | Not Started | 005 | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [tambah-anggota-formulir](../../stories/simplified-self-service/tambah-anggota-formulir.md), [pengajuan-prefill](../../stories/simplified-self-service/pengajuan-prefill.md) |
| [007](./007-dashboard-integration.md) | Dashboard Integration + E2E Verification | Not Started | 005, 006 | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [konfirmasi-pembayaran-sederhana](../../stories/simplified-self-service/konfirmasi-pembayaran-sederhana.md) |

## Phase 4: PWA

**Exit Criteria:** Lighthouse PWA score > 80; app installable to home screen; standalone mode; splash screen; static asset caching; session auth unaffected.
**Requires:** No strict dependency — can run in parallel with Phase 2/3

| Task | Title | Status | Depends On | Story |
|------|-------|--------|------------|-------|
| [008](./008-pwa-setup.md) | PWA Setup — Manifest, Service Worker, Icons | Not Started | — | [pwa-instal](../../stories/simplified-self-service/pwa-instal.md) |

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

The longest dependency chain runs through Phase 1 into Phase 3. Phase 2 (003→004) and Phase 4 (008) can proceed in parallel once Task 001 is complete.

---

## Story Coverage

| Story | Task(s) |
|-------|---------|
| auto-hijri-year | 002 |
| override-hijri-year | 002 |
| social-login-registrasi | 003, 004 |
| social-login-account-linking | 003 |
| pengajuan-zakat-sederhana | 005, 006, 007 |
| tambah-anggota-formulir | 006 |
| pengajuan-prefill | 006 |
| auto-create-keluarga | 005 |
| konfirmasi-pembayaran-sederhana | 007 |
| pwa-instal | 008 |
