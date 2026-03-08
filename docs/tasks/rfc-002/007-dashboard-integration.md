# Task 007: Dashboard Integration + Confirmation Verification

| Phase | Status | Story |
|-------|--------|-------|
| 3 | Not Started | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [konfirmasi-pembayaran-sederhana](../../stories/simplified-self-service/konfirmasi-pembayaran-sederhana.md) |

---

## Objective

"Bayar Zakat" button is added to the dashboard linking to the simplified form, and end-to-end flow is verified — from form submission through to UPZIS confirmation using existing V1 confirmation flow.

---

## Preconditions

- [ ] Task rfc-002/005: SimpleZakat backend working
- [ ] Task rfc-002/006: SimpleZakat form page working

---

## Scope

**Implement:**
- Add "Bayar Zakat" button/link to dashboard page (existing Vue component):
  - Prominent position — primary call-to-action for muzakki
  - Links to `/simple-zakat/create`
  - Coexists with V1 navigation (V1 menu/links remain accessible)
- Verify transaction detail page (V1) works for simple zakat transactions:
  - Transaction number, family name, zakat_lines with member names and amounts display correctly
  - Receipt/kwitansi renders correctly for auto-created data
- Verify UPZIS confirmation flow works for simple zakat transactions:
  - Transactions from simplified form appear in Online Payments list (V1)
  - Admin can confirm payment (same V1 flow)
  - Status transitions correctly (Pending -> Confirmed)
- Verify unique number mechanism works (nominal transfer unik)
- Verify auto-created Family/Muzakki data is valid for V1 usage:
  - Family appears in family management page
  - Muzakkis appear in family detail
  - V1 zakat submission works with auto-created family

**Boundaries:**
- No new backend logic (use case + controller from Task 005)
- No changes to V1 confirmation flow code
- No changes to transaction detail/receipt templates (should work as-is)
- No progressive profiling (V1.2)

---

## Implementation Notes

**Approach:**
Primarily a UI integration task (dashboard button) plus end-to-end verification. The button is a simple addition to the dashboard. The verification ensures that the auto-created data from Tasks 005/006 is compatible with all existing V1 views and flows.

**Key Files:**
- `resources/js/Pages/Dashboard.vue` (modify — add "Bayar Zakat" button)
- V1 transaction views (read/verify — no modifications expected)
- V1 online payments page (read/verify — no modifications expected)

**Patterns to Follow:**
- Follow dashboard UI conventions
- Button styling should match existing primary actions

---

## Done Criteria

- [ ] Dashboard shows "Bayar Zakat" button linking to `/simple-zakat/create`
- [ ] V1 navigation (menu items) remains accessible alongside new button
- [ ] Transaction detail page renders correctly for simple zakat transactions (nomor transaksi, family, zakat_lines)
- [ ] Receipt/kwitansi renders correctly for simple zakat transactions
- [ ] Transactions from simplified form appear in Online Payments list for admin
- [ ] Admin can confirm payment from simplified form (V1 confirmation flow)
- [ ] Unique number (nominal transfer unik) is generated and displayed
- [ ] Auto-created Family appears in V1 family management page
- [ ] Auto-created Muzakkis appear in V1 family detail
- [ ] V1 zakat submission works with auto-created family data
- [ ] End-to-end flow: login -> dashboard -> "Bayar Zakat" -> fill form -> submit -> see confirmation -> admin confirms -> status changes

---

## Verification

```bash
npm run dev
# Manual E2E test flow:
# 1. Login as new user (no family)
# 2. Click "Bayar Zakat" on dashboard
# 3. Fill simplified form with member data
# 4. Submit -> verify transaction number and unique amount shown
# 5. Login as admin -> Online Payments -> find transaction
# 6. Confirm payment -> verify status changes
# 7. Check Family and Muzakki records in admin views
# 8. Try V1 zakat submission with the auto-created family
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#33-formulir-zakat-sederhana`
- **Stories:** `docs/stories/simplified-self-service/pengajuan-zakat-sederhana.md`, `docs/stories/simplified-self-service/konfirmasi-pembayaran-sederhana.md`
- **Related Tasks:** 005, 006
