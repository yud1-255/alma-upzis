# ADR-007: Laravel Socialite dengan Penyimpanan Kolom di Tabel Users

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-03-08 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

C6 (Simplified Self-Service) mensyaratkan social login via Google dan Facebook OAuth 2.0 untuk menurunkan hambatan registrasi bagi muzakki baru. Dua keputusan arsitektural perlu dibuat:

1. **Integrasi OAuth:** Bagaimana mengimplementasikan alur OAuth 2.0 (redirect, callback, token handling) di stack Laravel?
2. **Penyimpanan data social:** Di mana menyimpan informasi social account (provider ID, tipe provider) — di tabel `users` yang ada atau di tabel terpisah?

Konteks tambahan:
- PRD C6 hanya mensyaratkan Google dan Facebook (Apple ID out of scope, planned V2)
- Skala pengguna kecil (satu masjid/lingkungan)
- Autentikasi V1 menggunakan Laravel Breeze (session-based, email/password)
- Account linking diperlukan: jika email dari social provider sudah terdaftar, hubungkan ke akun yang ada

---

## Decision

**We will:**
- Menggunakan **Laravel Socialite** sebagai library OAuth 2.0 untuk integrasi Google dan Facebook login
- Menyimpan data social account sebagai **dua kolom di tabel `users`**: `social_id` (nullable string) dan `social_type` (nullable string: `'google'` | `'facebook'` | `null`)
- Melakukan account linking berdasarkan exact email match — jika email dari social provider sudah ada di `users`, update `social_id` dan `social_type` pada akun tersebut

**We will not:**
- Mengimplementasikan OAuth 2.0 flow secara manual (state parameter, token validation, provider quirks)
- Membuat tabel `social_accounts` terpisah (one-to-many) untuk menyimpan multiple social accounts per user

---

## Rationale

**Chose this because:**

*Laravel Socialite:*
- Paket resmi Laravel untuk OAuth; digunakan oleh ratusan ribu proyek
- Menangani semua security concerns OAuth (CSRF state, token validation, provider-specific quirks) out of the box
- Driver bawaan untuk Google dan Facebook — tidak perlu menulis adapter
- Integrasi natural dengan session-based auth Laravel Breeze yang sudah ada

*Kolom di tabel `users`:*
- Sederhana — tidak ada JOIN tambahan saat login; query tetap single-table
- Cukup untuk 2 provider; tidak ada kebutuhan menyimpan multiple social accounts per user
- Account linking berbasis email, bukan `social_id` — kolom social hanya menyimpan provider terakhir yang digunakan

**Deciding factor:**
- Hanya butuh 2 provider. Tabel terpisah memberikan skalabilitas yang belum dibutuhkan, dengan biaya kompleksitas yang langsung terasa.

**Accepted trade-offs:**
- Hanya menyimpan satu social account per user — jika user login via Google lalu via Facebook, hanya Facebook yang tersimpan di `social_id`/`social_type`. Ini tidak menjadi masalah karena account linking berbasis email, bukan social ID
- Jika Apple ID ditambahkan di V2 dan kebutuhan multi-provider per user muncul, migrasi dari kolom ke tabel terpisah diperlukan — namun skala data satu masjid membuat migrasi ini trivial
- Dependensi paket tambahan (Socialite) — namun ini paket resmi Laravel yang di-maintain aktif

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Manual OAuth 2.0 implementation | Reinventing the wheel; banyak edge cases (state parameter, token refresh, provider API changes) yang sudah ditangani Socialite |
| Tabel `social_accounts` terpisah (one-to-many) | Over-engineering untuk 2 provider; JOIN tambahan di setiap login; kompleksitas account linking lebih tinggi; skala satu masjid tidak memerlukan ini |
| Laravel Passport / Sanctum | Ini untuk API token-based auth, bukan social OAuth; aplikasi menggunakan session-based auth |

---

## Consequences

**Positive:**
- Implementasi social login < 100 baris kode (controller + config)
- Login flow tetap session-based — tidak ada perubahan pada mekanisme auth V1
- Tidak ada tabel baru — hanya 2 kolom nullable ditambahkan ke `users`

**Negative:**
- Terbatas pada provider yang didukung Socialite (namun Google, Facebook, dan 20+ provider lain sudah tersedia)
- Satu social account per user — jika V2 memerlukan multi-provider, perlu migrasi ke tabel terpisah
- Password diisi random hash untuk user social login — mereka tidak bisa login via password tanpa reset terlebih dahulu

**Neutral:**
- OAuth credentials (client ID/secret) disimpan di `.env` — perlu setup per environment (development, production)
- Jika social provider down, user tetap bisa registrasi/login via email/password V1 — social login bukan satu-satunya jalur

---

## References

- [RFC-002: Simplified Self-Service Zakat — Section 3.1 & 6.1-6.2](../rfc/002-simplified-self-service.md)
- [PRD C6: Simplified Self-Service Zakat](../prd/006-simplified-self-service.md)
- [ADR-001: Laravel + PHP Framework](./001-laravel-php-framework.md) — stack yang diperluas
- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
