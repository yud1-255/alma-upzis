# PRD: Application Configuration

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C5 (from [product vision](./_index.md))

**One-liner:** Provides administrator-managed runtime configuration for hijri year settings, fitrah amounts, bank account info, payment display periods, and rate limits.

**Dependencies:**
- Requires: C1 (User Authentication & Role Management)
- Enables: C3 (Zakat Transaction Management — hijri year, fitrah amounts, payment display, unique number context)

---

## Problem

**What:** Zakat collection parameters change each year — the hijri year advances, fitrah amounts may be updated, bank accounts can change, and QRIS/transfer payment options need to be shown or hidden based on collection period timing. These settings must be adjustable without code changes.

**Who:** Administrators.

**Current State:** Settings would be hardcoded or managed through direct database edits, requiring developer involvement for routine annual changes.

---

## Solution

### Key Features

1. **Hijri Year Settings** — Current active hijri year and beginning year (defines the selectable range for reports and transactions).
2. **Fitrah Amount Options** — Multiple configurable Rp amounts that muzakki can select when entering fitrah zakat (stored as multiple rows with key `fitrah_amount`).
3. **Bank Account Info** — Bank account number displayed on transaction detail for online transfer instructions.
4. **Payment Display Periods** — Date ranges controlling when QRIS and bank transfer payment options are shown/hidden on transaction detail.
5. **Contact Information** — Confirmation phone number (WhatsApp) displayed for muzakki to contact UPZIS.
6. **KK Lookup Rate Limit** — Maximum number of KK number lookups allowed per user.

### Configuration Keys

| Key | Description | Example Value | Multi-value |
|-----|-------------|---------------|-------------|
| `hijri_year` | Current active hijri year | `1447` | No |
| `hijri_year_beginning` | Earliest selectable hijri year | `1444` | No |
| `fitrah_amount` | Selectable fitrah Rp amounts | `35000`, `40000`, `45000` | Yes |
| `bank_account` | Bank account number for transfers | `1234567890 (BSI)` | No |
| `confirmation_phone` | WhatsApp contact number | `08123456789` | No |
| `remove_qr_start_date` | Start date to hide QRIS option | `2026-03-20` | No |
| `remove_qr_end_date` | End date to hide QRIS option | `2026-04-05` | No |
| `remove_transfer_start_date` | Start date to hide transfer option | `2026-03-28` | No |
| `remove_transfer_end_date` | End date to hide transfer option | `2026-04-05` | No |
| `check_kk_limit` | Max KK lookups per user | `5` | No |

### User Workflows

1. **Update settings** — Admin navigates to App Configuration page → sees table of all config keys and values → edits a value inline → clicks save → value is updated immediately.
2. **Annual rollover** — At start of new collection period, admin updates `hijri_year`, reviews `fitrah_amount` values, and adjusts payment display date ranges.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Configuration changes | No code deployment needed | Admin can update all settings via UI |
| Annual rollover time | < 10 minutes | Time for admin to update all year-specific settings |

---

## Scope

### In Scope (v1.0)

- [x] `app_config` key-value table (key, value — not unique key, allows multiple rows per key)
- [x] Admin-only configuration management page (table with inline edit per row)
- [x] `hijri_year` — current active hijri year for transactions
- [x] `hijri_year_beginning` — defines start of selectable hijri year range
- [x] `fitrah_amount` — multiple values for fitrah Rp amount selector (multiple rows with same key)
- [x] `bank_account` — bank account displayed on transaction detail
- [x] `confirmation_phone` — WhatsApp contact displayed for muzakki
- [x] `remove_qr_start_date` / `remove_qr_end_date` — date range to hide QRIS payment option
- [x] `remove_transfer_start_date` / `remove_transfer_end_date` — date range to hide bank transfer option
- [x] `check_kk_limit` — per-user rate limit for KK number lookups
- [x] `AppConfig::getConfigValue()` — retrieve single value by key
- [x] `AppConfig::getConfigValues()` — retrieve all values for a key (multi-value support)
- [x] AppConfigPolicy restricting all operations to administrator role
- [x] Delete operation blocked at policy level (returns false)

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Config versioning / history | Low change frequency doesn't justify audit trail on config | TBD |
| Config validation rules per key | Values are simple strings; admin is trusted | TBD |
| Config import/export | Small number of settings; manual management is fine | TBD |
| Environment-based config override | Single deployment environment | Never |
| Config grouping or categories in UI | Flat list is sufficient for ~10 keys | TBD |

### Future (This Capability)

- None planned — capability is stable.

---

## User Stories

### Configuration Management

| Story | Priority | Link |
|-------|----------|------|
| Admin updates hijri year for new collection period | P0 | — |
| Admin updates fitrah amount options | P0 | — |
| Admin sets payment display date ranges | P1 | — |
| Admin updates bank account information | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Access control | Administrator-only, no delete | Prevents accidental removal of required config keys |
| Availability | Config values cached per-request via static model methods | Avoids repeated DB queries within single request |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Hijri Year | Islamic lunar calendar year used as the primary time dimension for zakat collection periods |
| Fitrah Amount | Standardized Rp amounts representing the monetary equivalent of zakat fitrah (e.g., 35000, 40000, 45000) |
| Payment Display Period | Date range during which a payment method (QRIS or bank transfer) is hidden from the transaction detail page |

---

## Technical Considerations

- `app_config` table allows multiple rows with the same key — this is intentional for `fitrah_amount` which has multiple selectable values
- No unique constraint on `key` column by design
- `getConfigValue()` returns a single string; `getConfigValues()` returns an array (plucked values)
- Payment display logic compares current date against `remove_*_start_date` / `remove_*_end_date` in the ZakatController show method
- Config values are all stored as strings; type coercion happens at consumption point

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
