# PRD: Reporting & Data Export

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C4 (from [product vision](./_index.md))

**One-liner:** Provides six report views with search, hijri year filtering, and pagination, plus five Excel export types and a print-optimized receipt for UPZIS officers and administrators.

**Dependencies:**
- Requires: C3 (Zakat Transaction Management)
- Enables: None — terminal capability in the dependency chain

---

## Problem

**What:** After collecting zakat, UPZIS officers need to generate summary reports for mosque management, reconcile daily collections, track individual muzakki contributions, and follow up on unconfirmed online payments. Paper-based reporting is tedious and error-prone.

**Who:** UPZIS officers and administrators.

**Current State:** Manual spreadsheet compilation from paper ledgers, taking hours per reporting period.

---

## Solution

### Key Features

1. **6 Report Views** — Each accessible as a dedicated page with search, hijri year filtering, and pagination.
2. **5 Excel Export Types** — One-click download of formatted spreadsheets with multi-row headers for complex breakdowns.
3. **Print-Optimized Receipt** — Dual-copy receipt layout for transaction confirmation, printable from transaction detail.

### Report Views

| # | Report | Route | Description |
|---|--------|-------|-------------|
| 1 | Transaction Recap | `/zakat/transaction_recap` | Aggregated per-transaction amounts across all zakat types |
| 2 | Daily Transaction Recap | `/zakat/daily_recap` | Same data ordered by payment_date for daily reconciliation |
| 3 | Muzakki Recap | `/zakat/muzakki_recap` | Per-muzakki line-level contribution detail |
| 4 | Daily Muzakki Recap | `/zakat/daily_muzakki_recap` | Same data ordered by payment_date |
| 5 | Muzakki List | `/zakat/muzakki_list` | All families with muzakkis and linked user accounts |
| 6 | Online Payments | `/zakat/online_payments` | Online-only transactions with contact info for follow-up |

### Excel Export Types

| # | Export | Filename | Key Columns |
|---|--------|----------|-------------|
| 1 | Summary | `zakat.xlsx` | Transaction no, dates, receive_from, officer, period, family head, channel, total |
| 2 | Transaction Recap | `transaction_recap.xlsx` | Transaction no, all 8 zakat type amounts, unique number, total, dates, receive_from |
| 3 | Muzakki List | `muzakki_list.xlsx` | Family head, address, contact, muzakki names, linked user email/name |
| 4 | Muzakki Recap | `muzakki_recap.xlsx` | Transaction no, muzakki name, officer, all zakat type amounts, date, channel |
| 5 | Online Payments | `online_payments.xlsx` | Transaction no, dates, receive_from, phone, email, confirming officer, total |

### User Workflows

1. **View report** — Navigate to report page → select hijri year → optionally search by name → browse paginated results.
2. **Export to Excel** — On any report or transaction list page → click export button → select hijri year → download Excel file.
3. **Print receipt** — View transaction detail → click "Cetak" (print) or "Cetak Rangkap" (dual copy) → browser print dialog opens with optimized layout.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Report generation time | < 5 seconds for page load | Server response time for report routes |
| Export generation time | < 30 seconds for Excel download | Time from click to file download |
| Report accuracy | 100% match with transaction data | Spot-check exported totals against transaction list |

---

## Scope

### In Scope (v1.0)

- [x] Transaction Recap report view (aggregated per transaction, searchable, hijri year filter, paginated)
- [x] Daily Transaction Recap report view (ordered by payment_date then created_at)
- [x] Muzakki Recap report view (per-muzakki line detail)
- [x] Daily Muzakki Recap report view (ordered by payment_date)
- [x] Muzakki List report view (families with muzakkis and linked users, paginated)
- [x] Online Payments report view (online-only transactions with phone/email, confirm button)
- [x] Summary Excel export (`zakat.xlsx`)
- [x] Transaction Recap Excel export (`transaction_recap.xlsx`, 2-row header)
- [x] Muzakki List Excel export (`muzakki_list.xlsx`)
- [x] Muzakki Recap Excel export (`muzakki_recap.xlsx`, 2-row header)
- [x] Online Payments Excel export (`online_payments.xlsx`)
- [x] Print-optimized transaction receipt (single and dual copy)
- [x] Search by name across all report views
- [x] Hijri year filter on all report views
- [x] All reports restricted to upzis and administrator roles
- [x] Exports use Maatwebsite Excel with auto-sized columns and styled headers

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| PDF export | Excel is the standard format for mosque reporting | TBD |
| Dashboard charts / visualizations | Reports are tabular; no charting requirement | TBD |
| Scheduled / email reports | Officers generate reports on-demand | TBD |
| Cross-year comparison reports | Single hijri year per report is sufficient | TBD |
| Custom report builder | Fixed report types cover all current needs | TBD |

### Future (This Capability)

- None planned — capability is stable.

---

## User Stories

### Report Viewing

| Story | Priority | Link |
|-------|----------|------|
| UPZIS officer views transaction recap for current hijri year | P0 | — |
| UPZIS officer views daily recap for reconciliation | P0 | — |
| UPZIS officer views online payments to follow up on unconfirmed transfers | P0 | — |
| Admin views muzakki list across all families | P1 | — |

### Export

| Story | Priority | Link |
|-------|----------|------|
| UPZIS officer exports transaction recap to Excel | P0 | — |
| UPZIS officer exports muzakki recap to Excel | P1 | — |
| UPZIS officer exports online payments for phone follow-up | P1 | — |

### Print

| Story | Priority | Link |
|-------|----------|------|
| UPZIS officer prints receipt for confirmed transaction | P0 | — |
| UPZIS officer prints dual-copy receipt | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Export file size | Handles up to 10,000 rows | Sufficient for single mosque annual collection |
| Report access | Restricted to upzis and administrator roles | Financial data is sensitive |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Transaction Recap | Aggregated view showing summed amounts per transaction across all zakat types |
| Muzakki Recap | Line-level view showing individual muzakki contributions within each transaction |
| Daily Recap | Same data as standard recap but ordered by payment_date for daily reconciliation |
| Online Payments | Filtered view showing only online (non-gerai) transactions with donor contact info |

---

## Technical Considerations

- All exports use Maatwebsite Excel package (`FromQuery`, `WithMapping`, `WithHeadings`, `WithColumnFormatting`, `WithStyles`, `ShouldAutoSize`)
- Transaction Recap and Muzakki Recap exports use 2-row headers to group sub-columns (e.g., Fitrah → Rp/Kg/Lt)
- Report data comes from `ZakatDomain` query methods, not raw SQL
- Daily recaps have no pagination (full dataset rendered) — may need attention if data volume grows
- Export route accepts optional `hijriYear` parameter; defaults to current hijri year from AppConfig

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
- [C3: Zakat Transaction Management](./003-zakat-transactions.md)
- [Maatwebsite Excel documentation](https://docs.laravel-excel.com/3.1/)
