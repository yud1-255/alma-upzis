# Task 003: Social Login Backend — Socialite + Controller + Routes

| Phase | Status | Story |
|-------|--------|-------|
| 2 | Not Started | [social-login-registrasi](../../stories/simplified-self-service/social-login-registrasi.md), [social-login-account-linking](../../stories/simplified-self-service/social-login-account-linking.md) |

---

## Objective

Users can authenticate via Google and Facebook OAuth using Laravel Socialite, with automatic account creation for new users and account linking for existing users.

---

## Preconditions

- [x] RFC-002 accepted
- [ ] Task rfc-002/001: Database migrations completed (social_id, social_type columns exist)

---

## Scope

**Implement:**
- Install `laravel/socialite` package via Composer
- Configure Google and Facebook providers in `config/services.php` (reading from .env)
- Add `.env.example` entries for `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URL`, `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`, `FACEBOOK_REDIRECT_URL`
- Create `SocialLoginController` with:
  - `redirectToProvider(string $provider)` — redirect to OAuth consent screen via Socialite
  - `handleProviderCallback(string $provider)` — process callback, create/link user, login
  - Private `findOrCreateUser(SocialiteUser, provider)` — account linking logic:
    1. Search user by email
    2. If found — update social_id, social_type — login
    3. If not found — create new user with social credentials, random hashed password, set email_verified_at if provider says email verified
- Add routes in `routes/web.php`:
  - `GET /auth/{provider}/redirect` — with constraint `where('provider', 'google|facebook')`
  - `GET /auth/{provider}/callback` — same constraint
- Update User model `$fillable` to include `social_id`, `social_type`
- Error handling: redirect to login with flash messages for OAuth errors, missing email, etc.

**Boundaries:**
- No Vue/frontend changes (handled in Task 004)
- No changes to existing auth flow (email/password registration remains unchanged)
- Password for social-login users is random hash — no password-based login possible until password reset

---

## Implementation Notes

**Approach:**
Laravel Socialite with stateless disabled (use default session-based). SocialLoginController in `app/Http/Controllers/Auth/`. Routes added to guest middleware group.

**Key Files:**
- `app/Http/Controllers/Auth/SocialLoginController.php` (new)
- `config/services.php` (modify — add google/facebook config)
- `routes/web.php` (modify — add 2 routes)
- `app/Models/User.php` (modify — add social_id, social_type to fillable)
- `.env.example` (modify — add social login env vars)

**Patterns to Follow:**
- Follow existing Auth controllers pattern (Laravel Breeze)
- Follow existing route registration style
- Account linking security: Only auto-link when provider returns email_verified=true. If email not verified by provider, create new account but don't set email_verified_at (fallback to V1 verification)
- User created via social login gets implicit muzakki role (same as regular registration — no entry in roles table)
- Password field: `Hash::make(Str::random(32))` — random, unusable password

---

## Done Criteria

- [ ] Package `laravel/socialite` installed: `composer show laravel/socialite`
- [ ] Route `GET /auth/google/redirect` redirects to Google OAuth
- [ ] Route `GET /auth/facebook/redirect` redirects to Facebook OAuth
- [ ] Invalid provider (e.g., `/auth/twitter/redirect`) returns 404
- [ ] New user via social login — account created, logged in, redirected to /dashboard
- [ ] Existing user (same email) via social login — account linked (social_id/social_type updated), logged in
- [ ] Social login with verified email — email_verified_at is set
- [ ] Social login without verified email — email_verified_at is NOT set, user needs V1 verification
- [ ] OAuth error/cancellation — redirected to login with error flash message
- [ ] User model $fillable includes social_id, social_type
- [ ] `.env.example` has all social login environment variables

---

## Verification

```bash
composer show laravel/socialite
php artisan route:list | grep auth
# Manual test: navigate to /auth/google/redirect in browser (requires .env config)
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#31-social-login`
- **Stories:** `docs/stories/simplified-self-service/social-login-registrasi.md`, `docs/stories/simplified-self-service/social-login-account-linking.md`
- **Related Tasks:** 001, 004
