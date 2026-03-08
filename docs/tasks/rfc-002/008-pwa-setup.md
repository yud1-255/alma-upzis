# Task 008: PWA Setup — Manifest, Service Worker, Icons

| Phase | Status | Story |
|-------|--------|-------|
| 4 | Not Started | [pwa-instal](../../stories/simplified-self-service/pwa-instal.md) |

---

## Objective

Application is installable as a PWA with proper manifest, service worker for static asset caching, app icons, splash screen, and standalone display mode — achieving Lighthouse PWA score > 80.

---

## Preconditions

- [x] RFC-002 accepted
- [x] HTTPS deployment available (required for service worker)

---

## Scope

**Implement:**
- Create `public/manifest.json` with:
  - `name`: "Alma UPZIS"
  - `short_name`: "Alma"
  - `start_url`: "/dashboard"
  - `display`: "standalone"
  - `background_color`: "#ffffff"
  - `theme_color`: "#1a5d1a"
  - `icons`: 192x192 and 512x512 PNG icons
- Create `public/sw.js` service worker with:
  - Cache-first strategy for static assets (CSS, JS compiled, images, icons)
  - Network-first strategy for HTML pages and Inertia requests (do NOT cache page navigations or API calls)
  - Versioned cache name: `alma-cache-v1` for cache invalidation on deploy
  - Do NOT intercept cookies/sessions — Laravel session-based auth must not be affected
  - Handle QuotaExceeded by clearing old cache and retrying
  - Offline fallback: simple page saying "Tidak ada koneksi internet" for navigation requests when offline
- Create application icons:
  - `public/icons/icon-192.png` (192x192)
  - `public/icons/icon-512.png` (512x512)
  - Icons should be the application logo/branding (placeholder initially, replace with actual branding later)
- Register PWA in `resources/views/app.blade.php`:
  - Add `<link rel="manifest" href="/manifest.json">`
  - Add `<meta name="theme-color" content="#1a5d1a">`
  - Add service worker registration script: `navigator.serviceWorker.register('/sw.js')`
  - Graceful degradation: check `'serviceWorker' in navigator` before registering

**Boundaries:**
- No offline form submission (forms require internet connection)
- No push notifications
- No background sync
- No custom install prompt UI (use browser's native prompt)
- No changes to backend code
- No changes to existing routing or controllers

---

## Implementation Notes

**Approach:**
Manual implementation of manifest.json and service worker — no Laravel PWA packages (per RFC decision). This is purely a frontend/static files task. Service worker caching must be carefully configured to avoid interfering with Inertia.js page loads and Laravel session cookies.

**Key Files:**
- `public/manifest.json` (new)
- `public/sw.js` (new)
- `public/icons/icon-192.png` (new)
- `public/icons/icon-512.png` (new)
- `resources/views/app.blade.php` (modify — add manifest link, theme-color meta, SW registration)

**Patterns to Follow:**
- Standard PWA patterns using `install`, `activate`, and `fetch` events on the service worker
- Cache strategy details:
  - **Install event:** pre-cache critical static assets
  - **Fetch event:** for requests matching static asset patterns (`*.css`, `*.js`, `/icons/*`, `/images/*`), use cache-first; for everything else (HTML, Inertia XHR), use network-first with offline fallback
  - **Activate event:** clean up old cache versions
- Service worker fetch handler must pass through all POST requests and requests with cookie headers to network — never cache authenticated content

---

## Done Criteria

- [ ] `public/manifest.json` exists with correct configuration
- [ ] `public/sw.js` exists with cache-first static + network-first navigation strategy
- [ ] `public/icons/icon-192.png` and `public/icons/icon-512.png` exist
- [ ] `app.blade.php` includes manifest link, theme-color meta, and SW registration
- [ ] Browser shows install prompt when visiting the app (Chrome DevTools > Application > Manifest shows "installable")
- [ ] App installs to home screen and opens in standalone mode (no address bar)
- [ ] Splash screen displays on launch from home screen
- [ ] Static assets (CSS, JS) are cached and served from cache on subsequent visits
- [ ] Page navigation and form submissions still go through network (not cached)
- [ ] Laravel session auth is not affected (cookies pass through)
- [ ] Offline: navigation shows "Tidak ada koneksi internet" fallback page
- [ ] Lighthouse PWA score > 80
- [ ] Service worker does not break existing functionality (login, forms, navigation)

---

## Verification

```bash
# Check files exist
ls -la public/manifest.json public/sw.js public/icons/

# Run Lighthouse audit (requires Chrome)
# In Chrome DevTools: Lighthouse > Check "Progressive Web App" > Generate Report

# Manual verification:
# 1. Open app in Chrome Android (or Chrome DevTools mobile emulation)
# 2. Verify install prompt appears
# 3. Install app
# 4. Open from home screen — verify standalone mode, splash screen
# 5. Navigate around — verify normal functionality
# 6. Go offline (DevTools > Network > Offline) — verify fallback page
# 7. Go back online — verify normal functionality resumes
# 8. Login/logout — verify session auth works correctly
```

---

## References

- **RFC:** `docs/rfc/002-simplified-self-service.md#32-pwa`
- **Story:** `docs/stories/simplified-self-service/pwa-instal.md`
- **Related Tasks:** None (independent of other phases)
