# Alma UPZIS

## Vision

A digital Zakat, Infaq, and Sadaqah (ZIS) management platform for UPZIS Al Munawwarah at Masjid Al Muhajirin, Bukit Pamulang Indah (BPI). Alma UPZIS replaces manual, paper-based donation workflows with a web application that supports both self-service online submissions by muzakki and in-person booth (gerai) collection by UPZIS officers — delivering accurate records, transparent audit trails, and streamlined reporting for every Ramadhan collection period.

## Target User

**Primary:**
- **Muzakki (Donors)** — BPI residents and community members who pay zakat, infaq, and other donations. Low technical sophistication; need a simple, mobile-friendly flow.
- **UPZIS Officers (Panitia)** — Mosque committee staff who receive payments at the gerai booth, confirm online transfers, and generate reports.

**Secondary:**
- **Administrators** — Manage user roles, application settings, and have full operational oversight including transaction voiding.

## Problem Space

Manual zakat collection at community mosques relies on paper forms, handwritten ledgers, and ad-hoc spreadsheets. This leads to transcription errors, lost records, difficulty reconciling bank transfers, and tedious end-of-period reporting. Muzakki lack visibility into their payment status, and officers spend excessive time on data entry rather than community service.

---

## Capability Map

| ID | Capability | PRD | Phase | Status |
|----|------------|-----|-------|--------|
| C1 | User Authentication & Role Management | [001](./001-auth-roles.md) | V1 | Shipped |
| C2 | Family & Muzakki Registration | [002](./002-family-muzakki.md) | V1 | Shipped |
| C3 | Zakat Transaction Management | [003](./003-zakat-transactions.md) | V1 | Shipped |
| C4 | Reporting & Data Export | [004](./004-reporting-export.md) | V1 | Shipped |
| C5 | Application Configuration | [005](./005-app-config.md) | V1 | Shipped |

### Capability Dependencies

```
C1 (Auth & Roles)
 ├──▶ C2 (Family & Muzakki) ──▶ C3 (Zakat Transactions) ──▶ C4 (Reporting & Export)
 └──▶ C5 (App Config) ──────────┘
```

---

## Product Principles

1. **Simplicity for donors** — Every screen has one obvious action. Muzakki should complete a zakat submission in under 5 minutes without training.
2. **Accountability via audit trails** — Every transaction state change (submit, confirm, void) is logged with user and timestamp. No silent mutations.
3. **Islamic calendar-native** — Hijri year is the primary time dimension for all transactions and reports, matching how zakat collection periods naturally operate.
4. **Dual-channel collection** — Online self-service and physical booth (gerai) workflows are first-class citizens, not afterthoughts.
5. **Mobile-friendly** — Responsive design ensures muzakki can submit from their phones without needing a desktop.

---

## Non-Functional Defaults

| Category | Default | Rationale |
|----------|---------|-----------|
| Auth | Required for all write operations | ZIS data is sensitive financial information |
| Email verification | Required before dashboard access | Prevents fake accounts from polluting data |
| Session timeout | Laravel default (120 min) | Balance between security and convenience |
| Pagination | 10 items per page | Keeps pages fast on mobile connections |
| Data retention | Indefinite | Zakat records are permanent mosque records |

---

## Product Non-Goals

- **Not multi-mosque / multi-tenant** — Built for a single UPZIS organization at one mosque. No tenant isolation, no cross-mosque reporting.
- **Not a payment gateway** — Does not process payments directly. Bank transfers and cash are handled outside the system; the app records and confirms them.
- **Not an accounting system** — Tracks donations and generates reports but does not handle fund distribution (mustahik), balance sheets, or general ledger.
- **Not a mobile app** — Web-only, responsive design. No native iOS/Android application.

---

## Success Metrics (Product-Level)

| Metric | Target | Measurement |
|--------|--------|-------------|
| Zakat submissions digitized | 100% of mosque zakat transactions | All gerai and online transactions entered in the system |
| Report generation time | < 1 minute per export | Time from clicking export to receiving Excel file |
| Online submission adoption | > 30% of total transactions | Ratio of online vs gerai submissions per hijri year |

---

## Roadmap Overview

### V1 — Digital ZIS Collection (Shipped)
**Goal:** Fully replace paper-based zakat collection with a web application supporting online and gerai workflows, family/muzakki registration, transaction management, and reporting.

Capabilities: C1, C2, C3, C4, C5

### V1.x — Enhancements
**Goal:** TBD

### V2 — Future
**Goal:** TBD

---

## Glossary

| Term | Definition |
|------|------------|
| Zakat | Obligatory Islamic alms-giving, one of the five pillars of Islam |
| Fitrah | Zakat al-Fitr — obligatory charity paid before Eid al-Fitr, can be in money (Rp), rice by weight (kg), or rice by volume (lt) |
| Maal | Zakat on wealth/assets exceeding the nisab threshold |
| Profesi | Zakat on professional income/salary |
| Infaq | Voluntary charitable spending in the way of Allah |
| Wakaf | Endowment — donation of assets for permanent charitable use |
| Fidyah | Compensation for missed fasting, paid in money (Rp) or rice (kg) |
| Kafarat | Expiation payment for breaking an oath or violating fasting rules |
| Muzakki | A person who pays zakat |
| Mustahik | A person eligible to receive zakat (not tracked in this system) |
| UPZIS | Unit Pengumpul Zakat, Infaq, dan Shadaqah — the mosque's ZIS collection unit |
| Hijri | Islamic lunar calendar; the primary time dimension for zakat periods |
| KK | Kartu Keluarga — Indonesian family card number, used to look up existing family records |
| BPI | Bukit Pamulang Indah — the residential complex where the mosque is located |
| Gerai | Physical collection booth staffed by UPZIS officers during Ramadhan |
| Panitia | Committee member / UPZIS officer |

---

## References

- UPZIS Al Munawwarah collection procedures (internal)
- Masjid Al Muhajirin, Bukit Pamulang Indah, South Jakarta
