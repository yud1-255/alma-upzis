# Task 005: SubmitSimpleZakat Use Case + SimpleZakatController Backend

| Phase | Status | Story |
|-------|--------|-------|
| 3 | Done | [pengajuan-zakat-sederhana](../../stories/simplified-self-service/pengajuan-zakat-sederhana.md), [auto-create-keluarga](../../stories/simplified-self-service/auto-create-keluarga.md) |

---

## Objective

Backend use case and controller for simplified zakat submission are implemented — auto-creating Family and Muzakki records, then delegating to existing ZakatDomain for transaction creation.

---

## Preconditions

- [x] RFC-002 accepted
- [x] Task rfc-002/001: Database migrations completed (families.email, nullable fields)
- [x] Task rfc-002/002: HijriYearHelper available

---

## Scope

**Implement:**
- Create `app/UseCases/SubmitSimpleZakat.php` use case class:
  - Constructor: inject `ZakatDomain`
  - `execute(User $user, array $data): Zakat` — orchestrator method
  - `findOrCreateFamily(User $user, array $contactData): Family` — if user has family_id, return existing; otherwise create new Family with head_of_family, phone, email (address=null, is_bpi=false, kk_number=null), link to user
  - `syncMuzakkis(Family $family, array $members): Collection` — for each member: if muzakki_id is set and belongs to family, use existing; if null, create new Muzakki linked to family
  - Entire `execute()` wrapped in `DB::transaction()`
- Create `app/Http/Controllers/SimpleZakatController.php`:
  - `create()` — return Inertia view for form, with pre-fill data if user has family (load family + muzakkis)
  - `store(Request $request)` — validate input, call use case, redirect to transaction detail
  - Validation rules: head_of_family required|string, email required|email, phone required|string, members array, members.*.name required|string, members.*.zakat object with all 10 zakat type fields as numeric|min:0, custom validation: at least one non-zero amount across all members
  - muzakki_id ownership validation: if provided, verify muzakki belongs to user's family
- Add routes in `routes/web.php`:
  - `GET /simple-zakat/create` — auth middleware
  - `POST /simple-zakat` — auth middleware
  - `GET /simple-zakat/{id}` — auth middleware (reuse V1 transaction detail view)
- Use case calls `ZakatDomain::submitAsMuzakki()` for actual transaction creation (existing method)
- Transaction fields set by use case before delegation: `is_offline_submission = false`, `receive_from = $user->id`, `family_head = $family->head_of_family`, hijri_year via `HijriYearHelper::current()`

**Input data structure from form:**
```php
$data = [
    'head_of_family' => 'Nama Lengkap',
    'email' => 'email@example.com',
    'phone' => '081234567890',
    'members' => [
        [
            'muzakki_id' => null,  // new member
            'name' => 'Nama',
            'zakat' => [
                'fitrah_rp' => 50000, 'fitrah_kg' => 0, 'fitrah_lt' => 0,
                'maal_rp' => 0, 'profesi_rp' => 0, 'infaq_rp' => 0,
                'wakaf_rp' => 0, 'fidyah_rp' => 0, 'fidyah_kg' => 0, 'kafarat_rp' => 0,
            ]
        ],
    ]
];
```

**Boundaries:**
- No Vue/frontend (handled in Task 006)
- No changes to ZakatDomain logic (only calling existing methods)
- No changes to confirmation flow (uses existing V1 flow)
- No pre-fill from transaction history (V1.2)
- Dashboard button addition (handled in Task 007)

---

## Implementation Notes

**Approach:**
Create new `app/UseCases/` directory (first use case in codebase). Controller follows existing controller patterns. Use case is the application-layer orchestrator — it is NOT a domain class. This establishes the pattern for separating application orchestration from domain logic.

**Key Files:**
- `app/UseCases/SubmitSimpleZakat.php` (new)
- `app/Http/Controllers/SimpleZakatController.php` (new)
- `routes/web.php` (modify — add 3 routes)
- `app/Domains/ZakatDomain.php` (read — understand submitAsMuzakki interface, do NOT modify)

**Patterns to Follow:**
- Follow existing controller validation style
- Use `DB::transaction()` for atomicity
- Follow `ZakatDomain::submitAsMuzakki()` parameter expectations

**Error Handling:**
- Validation errors return back with errors (standard Laravel behavior)
- muzakki_id ownership check in use case throws exception caught by controller
- DB transaction rollback on any failure

**Auto-create defaults:**
- Family: head_of_family, phone, email, address=null, kk_number=null, is_bpi=false
- Muzakki: family_id, name, phone=null, address=null, is_bpi=false, use_family_address=true, is_active=true

---

## Done Criteria

- [ ] `app/UseCases/SubmitSimpleZakat.php` exists and implements execute()
- [ ] `app/Http/Controllers/SimpleZakatController.php` exists with create() and store()
- [ ] Routes registered: `php artisan route:list | grep simple-zakat` shows 3 routes
- [ ] Use case auto-creates Family when user has no family_id
- [ ] Use case auto-creates Muzakki for new members
- [ ] Use case reuses existing Family when user already has one
- [ ] Use case reuses existing Muzakki when muzakki_id is provided
- [ ] Use case rejects muzakki_id that doesn't belong to user's family
- [ ] Validation rejects submission when all amounts are zero
- [ ] Validation rejects missing required fields (head_of_family, email, phone)
- [ ] Transaction created via ZakatDomain with correct data (family_id, zakat_lines, hijri_year)
- [ ] Entire operation is atomic (DB::transaction rollback on failure)
- [ ] `create()` returns pre-fill data from existing family/muzakkis

---

## Verification

```bash
php artisan route:list | grep simple-zakat
php artisan tinker --execute="new App\UseCases\SubmitSimpleZakat(app(App\Domains\ZakatDomain::class));"
# Manual: POST to /simple-zakat with test data, verify transaction created
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#33-formulir-zakat-sederhana`
- **Stories:** `docs/stories/simplified-self-service/pengajuan-zakat-sederhana.md`, `docs/stories/simplified-self-service/auto-create-keluarga.md`
- **Related Tasks:** 001, 002, 006, 007
