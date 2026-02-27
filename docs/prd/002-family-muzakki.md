# PRD: Family & Muzakki Registration

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C2 (from [product vision](./_index.md))

**One-liner:** Enables registration and management of family units and their individual muzakki members, supporting both BPI residents and outside community members.

**Dependencies:**
- Requires: C1 (User Authentication & Role Management)
- Enables: C3 (Zakat Transaction Management)

---

## Problem

**What:** Zakat is collected per individual (muzakki) but organized by family. The system needs to know who is paying, which family they belong to, and where they live — particularly for BPI residents whose addresses follow a block/house numbering scheme.

**Who:** Muzakki (self-registering their family) and UPZIS officers (registering families on behalf of walk-in donors at the gerai).

**Current State:** Paper forms capture family and muzakki info per transaction, leading to duplicate entries, inconsistent names, and no way to track a muzakki's history across years.

---

## Solution

### Key Features

1. **Family CRUD** — Create and update family records with head of family, phone, KK number, and address (BPI block/house or free-text).
2. **BPI Address System** — Structured address entry for BPI residents with block (A, B, C, E, F, G) and house number selectors, auto-generating formatted addresses.
3. **Muzakki Management** — Add multiple muzakki per family, with individual names and optional address override. Soft deactivation (no hard delete) preserves transaction history.
4. **KK Number Lookup** — Search for existing families by Kartu Keluarga number, rate-limited per user to prevent abuse.
5. **Address Inheritance** — Muzakki can inherit family address (`use_family_address=true`). Address changes on the family cascade to all inheriting muzakkis.
6. **Family-to-User Linking** — Associate a family record with a user account, enabling self-service zakat submission.
7. **UPZIS Family Registration** — Officers can create new families inline during zakat entry, redirecting back to the transaction form.

### User Workflows

1. **Muzakki registers family** — Log in → navigate to family registration → fill in head of family, phone, address (BPI selector or manual) → optionally enter KK number → add muzakki members → save.
2. **Muzakki adds family member** — On family page → enter name → toggle "use family address" or enter custom address → save. Member appears in muzakki list for zakat submission.
3. **KK lookup** — Enter KK number → system finds existing family → user links to that family (avoids duplicate registration).
4. **UPZIS inline registration** — During zakat entry, search finds no match → click register → create family → redirect back to zakat form with new family pre-selected.
5. **Deactivate muzakki** — On family page → remove a muzakki → sets `is_active=false` (preserves historical transactions).

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Duplicate families | < 5% | Families with same KK number or head_of_family name |
| Family registration completion | > 95% | Families with at least one active muzakki |

---

## Scope

### In Scope (v1.0)

- [x] Family creation with head_of_family, phone, address, is_bpi flag
- [x] BPI block selector: A(1-20), B(1-23), C(1-20), E(1-24), F(1-19), G(1-11)
- [x] BPI house number selector: 0-20
- [x] Auto-generated BPI address format: "Bukit Pamulang Indah {block} no {house}"
- [x] Free-text address for non-BPI residents
- [x] KK number field on family (optional)
- [x] KK number lookup with per-user rate limiting (`kk_check_count` vs `check_kk_limit` config)
- [x] Multiple muzakkis per family
- [x] Muzakki fields: name, phone (optional), address, is_bpi, bpi_block_no, bpi_house_no
- [x] `use_family_address` flag with cascade on family address update
- [x] Muzakki soft deactivation (`is_active=false`)
- [x] Family-to-user association (`users.family_id`)
- [x] Family assign endpoint (link existing family to current user)
- [x] UPZIS family search (autocomplete by head_of_family or muzakki name, max 10 results)
- [x] UPZIS inline family registration during zakat entry
- [x] Default muzakki auto-created from family head data on family registration
- [x] Validation: head_of_family required, phone required, is_bpi required, address required, bpi_block_no/bpi_house_no required if BPI

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Family merge / deduplication tool | Low volume doesn't justify complexity | TBD |
| Muzakki photo or ID upload | Not needed for current collection workflow | TBD |
| Family tree / relationship modeling | Simple flat list of members is sufficient | TBD |
| Bulk import from spreadsheet | One-time migration done manually | TBD |
| Muzakki hard delete | Soft deactivation preserves audit trail | Never |

### Future (This Capability)

- None planned — capability is stable.

---

## User Stories

### Family Registration

| Story | Priority | Link |
|-------|----------|------|
| Muzakki creates a new family | P0 | — |
| Muzakki updates family address | P0 | — |
| UPZIS officer registers family during gerai entry | P0 | — |
| User links to existing family via KK lookup | P1 | — |

### Muzakki Management

| Story | Priority | Link |
|-------|----------|------|
| Muzakki adds a family member | P0 | — |
| Muzakki deactivates a family member | P1 | — |
| Muzakki toggles address inheritance | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Search response time | < 500ms for family autocomplete | Smooth gerai workflow during peak collection |
| KK lookup rate limit | Configurable via AppConfig (`check_kk_limit`) | Prevents brute-force KK enumeration |
| Search result limit | Max 10 results per query | Keeps autocomplete fast and focused |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Family | A household unit identified by head_of_family, containing one or more muzakki members |
| Muzakki | An individual who pays zakat, always belonging to exactly one family |
| KK (Kartu Keluarga) | Indonesian family registration card number, used as a lookup key |
| BPI | Bukit Pamulang Indah residential complex with structured block/house addressing |
| use_family_address | Flag indicating a muzakki inherits their family's address; changes cascade automatically |

---

## Technical Considerations

- `families` table has a `kk_number` field (nullable) but no unique constraint — duplicates possible
- BPI block definitions are hard-coded in `ResidenceDomain` (A-G with specific house ranges)
- Address cascade logic lives in `ResidenceDomain::updateFamilyRegistration()`
- Family search queries both `families.head_of_family` and `muzakkis.name`
- Rate limiting for KK lookup uses a counter on `users.kk_check_count`, not middleware-level throttling

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

- [Product vision](./_index.md)
- [C1: User Authentication & Role Management](./001-auth-roles.md)
