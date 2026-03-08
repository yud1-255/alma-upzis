# Task 002: HijriYearHelper + Refactor Hijri Year References + AppConfig Admin UI

| Phase | Status | Story |
|-------|--------|-------|
| 1 | Not Started | [auto-hijri-year](../../stories/simplified-self-service/auto-hijri-year.md), [override-hijri-year](../../stories/simplified-self-service/override-hijri-year.md) |

---

## Objective

Hijri year is determined automatically system-wide via `HijriYearHelper::current()`, with admin override capability through AppConfig toggle UI.

---

## Preconditions

- [x] RFC-002 accepted
- [ ] Task rfc-002/001: Database migrations completed (for `families.email`, needed for full Phase 1 exit)

---

## Scope

**Implement:**
- Install `geniusts/hijri-dates` package via Composer
- Create `app/Helpers/HijriYearHelper.php` with:
  - `public static function current(): string` -- returns override value from AppConfig if set, otherwise auto-detect via HijriDate
  - `public static function autoDetect(): string` -- returns current Hijri year from library
- Refactor ALL existing references to `AppConfig::getConfigValue('hijri_year')` to use `HijriYearHelper::current()` instead. Affected locations (from RFC):
  - `ZakatDomain::submitAsMuzakki()` -- hijri_year tag on new transactions
  - `ZakatDomain::submitAsUpzis()` -- hijri_year tag on new transactions
  - Controllers providing default hijri year filter for report pages (`transaction list`, `transaction_recap`, `daily_recap`, `muzakki_recap`, `daily_muzakki_recap`, `muzakki_list`, `online_payments`)
- Update AppConfig admin page (Vue component) to:
  - Show auto-detect value alongside override field
  - Add toggle on/off for override -- when off, field is disabled and shows auto-detect as placeholder
  - Validate: only 4-digit numeric value when override is active

**Boundaries:**
- No database migration (done in Task 001)
- No new routes
- No changes to transaction creation logic beyond swapping the hijri_year source

---

## Implementation Notes

**Approach:**
Static helper class (per RFC decision -- not DI service). Install package, create helper, then grep codebase for all `getConfigValue('hijri_year')` and replace.

**Key Files:**
- `app/Helpers/HijriYearHelper.php` (new)
- `app/Domains/ZakatDomain.php` (modify -- swap hijri_year source)
- `app/Http/Controllers/ZakatController.php` (modify -- swap default filter)
- Vue AppConfig page component (modify -- add toggle UI)

**Patterns to Follow:**
- Follow existing helper/domain patterns in codebase
- Library usage: `HijriDate::now()->year` from `geniusts/hijri-dates`
- Error handling: If library fails, fallback to last stored AppConfig value, log error

---

## Done Criteria

- [ ] Package `geniusts/hijri-dates` installed: `composer show geniusts/hijri-dates`
- [ ] `HijriYearHelper::current()` returns correct Hijri year (1447 for March 2026)
- [ ] `HijriYearHelper::autoDetect()` returns current Hijri year from library
- [ ] No remaining direct references to `AppConfig::getConfigValue('hijri_year')` in domain/controller code (all go through helper)
- [ ] AppConfig admin page shows auto-detect value and toggle for override
- [ ] When override is set in AppConfig, `HijriYearHelper::current()` returns override value
- [ ] When override is cleared, `HijriYearHelper::current()` returns auto-detect value
- [ ] Existing transaction creation (V1) still works correctly with new helper
- [ ] Report page filters default to correct Hijri year

---

## Verification

```bash
composer show geniusts/hijri-dates
php artisan tinker --execute="echo App\Helpers\HijriYearHelper::current();"
php artisan tinker --execute="echo App\Helpers\HijriYearHelper::autoDetect();"
grep -r "getConfigValue.*hijri_year" app/ --include="*.php" | grep -v HijriYearHelper
# Should return 0 results (all references migrated)
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#34-auto-hijri-year`
- **Stories:** `docs/stories/simplified-self-service/auto-hijri-year.md`, `docs/stories/simplified-self-service/override-hijri-year.md`
- **Related Tasks:** 001
