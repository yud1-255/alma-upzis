# Story Map

## Overview

Stories grouped by capability. Priority indicates implementation order.

**Priority Key:** P0 = Must have V1 | P1 = Should have V1 | P2 = Nice to have | V2 = Future

---

## [Capability 1: e.g., Authentication]

| Priority | Story | Status | Task |
|----------|-------|--------|------|
| P0 | [register](./auth/register.md) | Not Started | |
| P0 | [login](./auth/login.md) | Not Started | |
| P1 | [password-reset](./auth/password-reset.md) | Not Started | |
| V2 | [oauth](./auth/oauth.md) | Backlog | |

## [Capability 2: e.g., Billing]

| Priority | Story | Status | Task |
|----------|-------|--------|------|
| P1 | [subscribe](./billing/subscribe.md) | Not Started | |
| P1 | [cancel](./billing/cancel.md) | Not Started | |

---

## Status Values

- **Backlog** — Defined but not scheduled
- **Not Started** — Scheduled for current phase
- **In Progress** — Actively being implemented
- **Done** — Implemented and tested
- **Deferred** — Moved out of current scope

## Adding Stories

1. Create capability folder if needed: `docs/stories/[capability]/`
2. Copy `000-template.md` into folder
3. Add to index above with priority
4. Link to task when implementation starts
