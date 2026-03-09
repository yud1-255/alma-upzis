# Task 001: Database Migrations for C6

| Phase | Status | Story |
|-------|--------|-------|
| 1 | Done | N/A (infrastructure) |

---

## Objective

All database schema changes required for C6 are migrated -- social login columns on users, email column on families, nullable address on families and muzakkis.

---

## Preconditions

- [x] RFC-002 accepted

---

## Scope

**Implement:**
- Migration 1: Add `social_id` (string, nullable) and `social_type` (string, nullable) columns to `users` table, with composite index on `[social_id, social_type]`
- Migration 2: Add `email` (string, nullable) column to `families` table; change `address` column to nullable
- Migration 3: Change `muzakkis.address` column to nullable (if not already)
- All migrations must have proper `down()` methods for rollback

**Boundaries:**
- No model changes (handled in subsequent tasks)
- No seed data changes
- No controller or route changes

---

## Implementation Notes

**Approach:**
Standard Laravel migrations via `php artisan make:migration`. The `->change()` method requires `doctrine/dbal` package -- verify it is installed or install it.

**Key Files:**
- `database/migrations/xxxx_xx_xx_xxxxxx_add_social_login_columns_to_users_table.php` (new)
- `database/migrations/xxxx_xx_xx_xxxxxx_add_email_and_nullable_address_to_families_table.php` (new)
- `database/migrations/xxxx_xx_xx_xxxxxx_make_address_nullable_on_muzakkis_table.php` (new)

**Patterns to Follow:**
- Follow existing migration style in codebase (e.g., `2022_04_08_183325_add_payment_date.php`)
- Existing migrations show tables: `users`, `families` (with `address` string), `muzakkis` (with `address` string)
- User model has: `name`, `email`, `password`, `remember_token`, `email_verified_at`, `family_id` (FK), `kk_checksum`
- Family model has: `head_of_family`, `phone`, `address`, `is_bpi`, `kk_number`, `bpi_block_no`, `bpi_house_no`
- Muzakki model has: `family_id`, `name`, `phone`, `address`, `is_bpi`, `use_family_address`, `is_active`

---

## Done Criteria

- [ ] Migration for `users.social_id` and `users.social_type` runs successfully: `php artisan migrate`
- [ ] Migration for `families.email` and `families.address` nullable runs successfully
- [ ] Migration for `muzakkis.address` nullable runs successfully
- [ ] All three `down()` methods rollback cleanly: `php artisan migrate:rollback`
- [ ] Existing data is not affected by migration

---

## Verification

```bash
php artisan migrate
php artisan migrate:status
php artisan migrate:rollback --step=3
php artisan migrate
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#4-data-model`
- **Related Tasks:** 002
