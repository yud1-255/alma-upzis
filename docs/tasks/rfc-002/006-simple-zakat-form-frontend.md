# Task 006: SimpleZakat/Create.vue — Form Page with Member Management

| Phase | Status | Story |
|-------|--------|-------|
| 3 | Not Started | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [tambah-anggota-formulir](../../stories/simplified-self-service/tambah-anggota-formulir.md), [pengajuan-prefill](../../stories/simplified-self-service/pengajuan-prefill.md) |

---

## Objective

Vue form page for simplified zakat submission is implemented with contact fields, dynamic member management, zakat amount inputs per member, pre-fill from existing data, and client-side validation.

---

## Preconditions

- [ ] Task rfc-002/005: SimpleZakatController backend with create() and store() routes

---

## Scope

**Implement:**
- Create `resources/js/Pages/SimpleZakat/Create.vue`:
  - Contact section: nama lengkap, email, nomor telepon fields
  - Member list section: dynamic rows, each with nama anggota + 10 zakat type amount fields
  - First row (kepala keluarga) always present and cannot be removed
  - "Tambah Anggota" button to add new member rows
  - Delete button (X/trash icon) on each additional member row
  - Submit button "Ajukan" that POSTs to `/simple-zakat` via Inertia form
  - All 10 zakat types per member: fitrah_rp, fitrah_kg, fitrah_lt, maal_rp, profesi_rp, infaq_rp, wakaf_rp, fidyah_rp, fidyah_kg, kafarat_rp
- Pre-fill support:
  - If user has existing family, contact fields pre-filled from family data (head_of_family, email, phone)
  - If family has existing muzakkis, member rows pre-filled with muzakki names and IDs
  - New members added on top of existing ones
  - Pre-filled fields are editable (not read-only)
  - Existing muzakkis pass their muzakki_id in form submission
  - New muzakkis have muzakki_id = null
- Client-side validation:
  - Required: nama lengkap, email (format), nomor telepon
  - Required: nama for each member row that has any non-zero amount
  - At least one non-zero amount across all member rows
- Display server-side validation errors from backend
- Responsive design — mobile-first (form should work well on phone screens)

**Boundaries:**
- No backend changes (handled in Task 005)
- No transaction detail/show page changes (reuse V1)
- No dashboard button (handled in Task 007)
- No pre-fill from transaction history (V1.2)
- Members with all-zero amounts are ignored on submit (not validated as error)

---

## Implementation Notes

**Approach:**
Standard Vue 3 + Inertia page. Use `useForm()` from Inertia for form state management. Dynamic member rows managed via reactive array. Props received from `SimpleZakatController::create()` — family data and muzakkis for pre-fill.

**Key Files:**
- `resources/js/Pages/SimpleZakat/Create.vue` (new)
- Optionally: `resources/js/Components/MemberRow.vue` (new — extracted component for each member row)

**Patterns to Follow:**
- Follow existing Vue page patterns in the project (layout, form handling, error display)
- Follow Tailwind CSS conventions used in existing pages
- Use `useForm()` from `@inertiajs/vue3` for form state

**UI Details:**
- Zakat type display: Use readable labels for each type (e.g., "Zakat Fitrah (Rp)", "Zakat Maal (Rp)", etc.)
- Form data structure must match backend expectation (see Task 005 input data structure)
- Distinguish existing vs new members visually (e.g., different background color or "baru" badge)

---

## Done Criteria

- [ ] `SimpleZakat/Create.vue` page renders at `/simple-zakat/create`
- [ ] Contact fields (nama, email, telepon) display and are editable
- [ ] All 10 zakat type fields display for each member row
- [ ] "Tambah Anggota" adds a new member row
- [ ] Delete button removes member row (not available on first/head row)
- [ ] Pre-fill works: existing family data populates contact fields
- [ ] Pre-fill works: existing muzakkis appear as member rows with their IDs
- [ ] New members added alongside existing ones
- [ ] Client-side validation: required fields show error messages
- [ ] Client-side validation: at least one non-zero amount required
- [ ] Server-side validation errors displayed correctly
- [ ] Form submits successfully and redirects to transaction detail
- [ ] Form is usable on mobile viewport (responsive)
- [ ] Existing member rows carry muzakki_id; new rows have null muzakki_id

---

## Verification

```bash
npm run dev
# Manual: navigate to /simple-zakat/create
# Manual: test form with no pre-fill (new user)
# Manual: test form with pre-fill (user with existing family)
# Manual: add/remove member rows
# Manual: submit with valid data, verify redirect
# Manual: submit with invalid data, verify validation errors
# Manual: test on mobile viewport
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#33-formulir-zakat-sederhana`
- **Stories:** `docs/stories/simplified-self-service/pengajuan-zakat-sederhana.md`, `docs/stories/simplified-self-service/tambah-anggota-formulir.md`, `docs/stories/simplified-self-service/pengajuan-prefill.md`
- **Related Tasks:** 005, 007
