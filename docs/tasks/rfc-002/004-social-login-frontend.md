# Task 004: Social Login Frontend — Vue Components

| Phase | Status | Story |
|-------|--------|-------|
| 2 | Done | [social-login-registrasi](../../stories/simplified-self-service/social-login-registrasi.md) |

---

## Objective

Social login buttons (Google and Facebook) are displayed on both login and registration pages, providing one-click OAuth authentication.

---

## Preconditions

- [x] Task rfc-002/003: Social login backend routes exist

---

## Scope

**Implement:**
- Create social login button components (or inline in existing pages):
  - Google button with official Google branding icon
  - Facebook button with official Facebook branding icon
- Add social login buttons to the Login page (existing Vue component)
  - Position: above or below email/password form, separated by "atau" divider
  - Each button links to `/auth/{provider}/redirect`
- Add social login buttons to the Register page (existing Vue component)
  - Same layout as login page
- Display flash error messages from social login failures (redirected from SocialLoginController)
- Ensure buttons are responsive and work well on mobile (PWA context)

**Boundaries:**
- No backend changes (handled in Task 003)
- No new routes
- No changes to existing email/password auth flow UI
- No social account management UI (linking/unlinking from profile)

---

## Implementation Notes

**Approach:**
Modify existing Login.vue and Register.vue pages (Inertia/Vue 3). Add social buttons as standard `<a>` links to OAuth redirect routes (not AJAX — full page redirect). Use Inertia flash messages for error display.

**Key Files:**
- `resources/js/Pages/Auth/Login.vue` (modify)
- `resources/js/Pages/Auth/Register.vue` (modify)
- `resources/js/Components/SocialLoginButtons.vue` (new — optional reusable component)

**Patterns to Follow:**
- Follow existing Vue component style and Tailwind CSS usage in the project
- Follow Google/Facebook brand guidelines for button styling
- Button styling: Use branded colors (Google: white bg with colored G icon; Facebook: blue bg with F icon). Both should have text "Masuk dengan Google" / "Masuk dengan Facebook"
- Divider: Simple "atau" text between horizontal lines

---

## Done Criteria

- [ ] Login page shows "Masuk dengan Google" and "Masuk dengan Facebook" buttons
- [ ] Register page shows same social login buttons
- [ ] Clicking Google button navigates to `/auth/google/redirect`
- [ ] Clicking Facebook button navigates to `/auth/facebook/redirect`
- [ ] Error flash messages from failed social login are displayed on login page
- [ ] Buttons are responsive and usable on mobile screens
- [ ] "atau" divider separates social login from email/password form
- [ ] Social buttons follow brand guidelines (Google/Facebook icons and colors)

---

## Verification

```bash
npm run dev
# Manual: open /login in browser, verify buttons visible
# Manual: open /register in browser, verify buttons visible
# Manual: click Google button, verify redirect to OAuth
# Manual: test on mobile viewport
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#31-social-login`
- **Story:** `docs/stories/simplified-self-service/social-login-registrasi.md`
- **Related Tasks:** 003
