# PRD: Zakat Transaction Management

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C3 (from [product vision](./_index.md))

**One-liner:** Manages the full lifecycle of zakat transactions — from submission through confirmation to voiding — across both online (muzakki self-service) and gerai (UPZIS booth) channels.

**Dependencies:**
- Requires: C1 (Auth & Roles), C2 (Family & Muzakki), C5 (App Config — for hijri year, fitrah amounts, payment display periods)
- Enables: C4 (Reporting & Data Export)

---

## Problem

**What:** Zakat collection involves multiple donation types per person, two submission channels (online transfer and in-person booth), and a confirmation workflow for online payments. Manual tracking leads to errors, lost payments, and difficult reconciliation.

**Who:** Muzakki (submitting donations), UPZIS officers (receiving at booth and confirming online transfers), administrators (voiding erroneous transactions).

**Current State:** Paper ledgers at the booth, WhatsApp messages for online confirmations, and manual spreadsheet tallying at end of collection period.

---

## Solution

### Key Features

1. **Dual-Channel Submission** — Online mode (muzakki self-service with unique transfer amount for bank identification) and gerai mode (UPZIS submits on behalf, auto-confirmed).
2. **8 Zakat Types Per Line** — Each muzakki line supports: fitrah (Rp, kg, lt), maal, profesi, infaq, wakaf, fidyah (Rp, kg), kafarat.
3. **Auto-Generated Transaction Numbers** — Sequential numbering via `sequence_numbers` table with configurable format template (`%year%`, `%seq%` placeholders).
4. **Payment Confirmation Workflow** — Online transactions start unconfirmed; UPZIS/admin confirms after verifying bank transfer, setting `zakat_pic` and `payment_date`.
5. **Transaction Voiding** — Admin-only soft delete (`is_active=false`) with audit log entry.
6. **Audit Trail** — `ZakatLog` records every state change: submit (1), confirm (2), void (3), with user and timestamp.
7. **Unique Transfer Amount** — Online transactions get a random 0-500 Rp added to the total, making each transfer amount unique for bank statement matching.
8. **Hijri Year Tracking** — Every transaction is tagged with the active hijri year from AppConfig.

### User Workflows

1. **Muzakki submits online** — Select family muzakkis → enter amounts per type per person → submit → receive transaction number and transfer amount (total + unique number) → wait for UPZIS confirmation.
2. **UPZIS submits at gerai** — Search/select family → enter amounts → submit as UPZIS → transaction is auto-confirmed with officer as `zakat_pic`.
3. **UPZIS confirms online payment** — View online payments list → verify bank transfer received → click confirm → `payment_date` and `zakat_pic` are set.
4. **Admin voids transaction** — View transaction → click void → transaction marked `is_active=false` → void log entry created.
5. **View transaction detail** — Shows receipt with all line items, bank account info, QRIS display (time-gated), activity log timeline.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Transaction accuracy | 100% — totals match line items | `total_rp` equals sum of all line amounts |
| Online confirmation turnaround | < 24 hours | Time between submission and confirmation |
| Voided transactions | < 2% of total | Indicates data entry quality |

---

## Scope

### In Scope (v1.0)

- [x] Online submission mode (`is_offline_submission=false`)
  - [x] `receive_from` = authenticated muzakki user
  - [x] `zakat_pic` = null (pending confirmation)
  - [x] `unique_number` = random 0-500 (0 if rice-only, no Rp amounts)
  - [x] `total_transfer_rp` = `total_rp` + `unique_number`
- [x] Gerai submission mode (`is_offline_submission=true`)
  - [x] `receive_from` = authenticated UPZIS officer
  - [x] `receive_from_name` = actual donor name (free text from form)
  - [x] `zakat_pic` = authenticated officer (auto-confirmed)
  - [x] `unique_number` = 0
  - [x] `total_transfer_rp` = `total_rp`
- [x] 8 zakat types per muzakki line: fitrah_rp, fitrah_kg, fitrah_lt, maal_rp, profesi_rp, infaq_rp, wakaf_rp, fidyah_rp, fidyah_kg, kafarat_rp
- [x] Configurable fitrah Rp amounts from AppConfig
- [x] Auto-generated sequential transaction numbers
- [x] Hijri year tag from AppConfig on every transaction
- [x] Validation: at least one of total_rp, total_kg, or total_lt must be non-zero
- [x] Payment confirmation endpoint (sets `zakat_pic`, `payment_date`)
- [x] Transaction voiding by administrator (`is_active=false`)
- [x] Audit log: ZakatLog with actions submit(1), confirm(2), void(3)
- [x] Transaction list — admins/upzis see all (searchable, year-filterable); muzakki sees own only
- [x] Transaction detail view with receipt, bank account, QRIS (time-gated display), activity log
- [x] Copy-to-clipboard for transfer amount on transaction detail
- [x] Family search autocomplete for gerai mode
- [x] Pagination (10 per page) on transaction list

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Direct payment gateway integration | Mosque uses existing bank accounts; no API integration needed | TBD |
| Recurring/scheduled payments | Zakat is typically annual; no recurring pattern | TBD |
| Multi-currency support | All transactions in IDR | Never |
| Partial confirmation / split payments | Transactions are atomic — confirm or don't | TBD |
| Transaction editing after submission | Void and re-create is the intended flow | Never |
| Mustahik (recipient) tracking | Out of product scope — see non-goals | Never |
| SMS/WhatsApp notifications | Manual follow-up via online payments report | TBD |

### Future (This Capability)

- None planned — capability is stable.

---

## User Stories

### Online Submission

| Story | Priority | Link |
|-------|----------|------|
| Muzakki submits zakat for family members online | P0 | — |
| Muzakki views their transaction and transfer amount | P0 | — |

### Gerai Submission

| Story | Priority | Link |
|-------|----------|------|
| UPZIS officer submits zakat on behalf of a walk-in donor | P0 | — |
| UPZIS officer searches and selects a family for gerai entry | P0 | — |
| UPZIS officer registers a new family inline during gerai entry | P1 | — |

### Confirmation & Voiding

| Story | Priority | Link |
|-------|----------|------|
| UPZIS officer confirms an online payment | P0 | — |
| Admin voids an erroneous transaction | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Data integrity | Transactions are soft-deleted, never hard-deleted | Audit trail preservation |
| Unique number range | 0-500 Rp | Small enough to not distort donation, large enough to differentiate transfers |
| Concurrency | Sequential transaction number generation uses DB-level increment | Prevents duplicate numbers under concurrent submissions |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Transaction | A zakat submission containing one or more line items (one per muzakki) |
| Line Item (ZakatLine) | A single muzakki's contribution within a transaction, with amounts per zakat type |
| Unique Number | Random 0-500 Rp added to online transactions for bank transfer identification |
| Gerai Mode | UPZIS officer submits on behalf of donor; auto-confirmed |
| Online Mode | Muzakki self-submits; requires UPZIS confirmation after bank transfer |
| Void | Soft deletion of a transaction (`is_active=false`) by an administrator |
| ZakatLog | Audit trail entry recording submit, confirm, or void actions with user and timestamp |

---

## Technical Considerations

- `SequenceNumber` model handles auto-increment with format template; `last_number` incremented atomically
- `total_rp` is a calculated field (sum of all Rp amounts across lines); stored denormalized on the transaction
- `family_head` and `receive_from_name` are denormalized strings — changes to family data don't retroactively update transactions
- QRIS and bank transfer display controlled by date-range AppConfig settings (see C5)
- ZakatPolicy governs all access: `create` is open to any auth user; `delete` is admin-only; `confirmPayment` requires upzis/admin; `submitForOthers` requires upzis/admin

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
- [C1: Auth & Roles](./001-auth-roles.md)
- [C2: Family & Muzakki](./002-family-muzakki.md)
- [C5: App Configuration](./005-app-config.md)
