# PRD: User Authentication & Role Management

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C1 (from [product vision](./_index.md))

**One-liner:** Provides user registration, session-based authentication, email verification, password reset, and role-based access control for three user tiers.

**Dependencies:**
- Requires: None — standalone foundation capability
- Enables: C2 (Family & Muzakki Registration), C3 (Zakat Transactions), C4 (Reporting), C5 (App Configuration)

---

## Problem

**What:** The system handles sensitive financial data (zakat payments) and must ensure only authorized users can submit, confirm, and void transactions. Different user types need different access levels.

**Who:** All users — muzakki, UPZIS officers, and administrators.

**Current State:** Paper-based collection has no access control. Anyone at the booth can write in the ledger. There's no way for muzakki to self-serve.

---

## Solution

### Key Features

1. **User Registration & Email Verification** — Self-service registration with mandatory email verification before accessing the dashboard.
2. **Session-Based Login with Password Reset** — Standard email/password authentication with a forgot-password flow via email token.
3. **Three-Tier Role System** — Muzakki (default, no explicit role), UPZIS officer, and administrator roles with middleware-enforced access control.
4. **Admin Role Assignment UI** — Administrators can search users and assign/change roles through a dedicated management page.

### User Workflows

1. **Muzakki self-registration** — Register → verify email → log in → access dashboard, family registration, and zakat submission.
2. **Admin role assignment** — Navigate to role management → search user → assign role (upzis or administrator) → user gains elevated access on next page load.
3. **Password recovery** — Click forgot password → receive reset email (token valid 60 min, throttled to 1 per 60s) → set new password → log in.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Registration completion rate | > 90% of started registrations | Verified users / total registered users |
| Role assignment errors | 0 | No user accidentally given wrong role |

---

## Scope

### In Scope (v1.0)

- [x] User registration (name, email, password)
- [x] Email verification (must verify before dashboard access)
- [x] Login / logout with session-based auth
- [x] Password reset via email token
- [x] Password confirmation for sensitive actions
- [x] Three roles: administrator, upzis, muzakki (implicit — no role record)
- [x] `EnsureUserHasRole` middleware for route protection
- [x] Admin-only role management page with user search
- [x] Single-role-per-user enforcement at assignment time (detach all, attach one)
- [x] Roles eager-loaded and shared to all Inertia pages via `HandleInertiaRequests`

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| OAuth / social login | Simplicity; community users prefer email | TBD |
| Multi-factor authentication | Low-risk context (mosque donations) | TBD |
| Permission granularity beyond roles | Three roles sufficient for current needs | TBD |
| User profile editing | Not needed for v1 workflows | TBD |
| Account deletion / self-service deactivation | Not required by regulation for this scale | TBD |
| API token authentication | Sanctum is installed but not used for web routes | TBD |

### Future (This Capability)

- None planned — capability is stable.

---

## User Stories

### Registration & Login

| Story | Priority | Link |
|-------|----------|------|
| Muzakki registers and verifies email | P0 | — |
| User logs in with email and password | P0 | — |
| User resets forgotten password via email | P0 | — |

### Role Management

| Story | Priority | Link |
|-------|----------|------|
| Admin assigns upzis role to a user | P0 | — |
| Admin searches users by name | P1 | — |
| Admin views all users vs only users with roles | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Password reset token expiry | 60 minutes | Balances security with usability |
| Password reset throttle | 1 request per 60 seconds | Prevents email spam |
| Password confirmation timeout | 3 hours (10,800s) | Reduces friction for admin sessions |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Role | Access tier: `administrator` (full access), `upzis` (officer operations), or implicit muzakki (default, no role record) |
| Panitia | Synonym for UPZIS officer in the UI context |

---

## Technical Considerations

- Built on Laravel Breeze (session auth, Inertia/Vue stack)
- Laravel Sanctum is installed (`HasApiTokens` on User model) but only session auth is used
- Role check is application-level (not Laravel gates/guards) — `User::hasRole()` and `User::hasAnyRole()` methods
- `role_user` pivot table with composite primary key and cascade deletes
- Custom `MailResetPasswordToken` notification for password reset emails

---

## Open Questions

None — capability is shipped and stable.

---

## RFCs

None — implemented directly.

---

## Changelog

### Version 1.0 — 2026-02-27
- Retroactive documentation of shipped capability

---

## References

- [Laravel Breeze documentation](https://laravel.com/docs/8.x/starter-kits#laravel-breeze)
- [Product vision](./_index.md)
