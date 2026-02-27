# PRD: User Authentication & Role Management

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C1 (dari [product vision](./_index.md))

**One-liner:** Menyediakan pendaftaran pengguna, autentikasi berbasis sesi, verifikasi email, reset kata sandi, dan kontrol akses berbasis peran untuk tiga tingkatan pengguna.

**Dependencies:**
- Requires: Tidak ada — kapabilitas fondasi yang berdiri sendiri
- Enables: C2 (Family & Muzakki Registration), C3 (Zakat Transactions), C4 (Reporting), C5 (App Configuration)

---

## Problem

**What:** Sistem menangani data keuangan sensitif (pembayaran zakat) dan harus memastikan hanya pengguna yang berwenang yang dapat mengajukan, mengkonfirmasi, dan membatalkan transaksi. Jenis pengguna yang berbeda membutuhkan tingkat akses yang berbeda.

**Who:** Semua pengguna — muzakki, petugas UPZIS, dan administrator.

**Current State:** Pengumpulan berbasis kertas tidak memiliki kontrol akses. Siapa pun di gerai dapat menulis di buku catatan. Tidak ada cara bagi muzakki untuk melayani diri sendiri.

---

## Solution

### Key Features

1. **Registrasi & Verifikasi Email Pengguna** — Pendaftaran mandiri dengan verifikasi email wajib sebelum mengakses dashboard.
2. **Session-Based Login with Password Reset** — Autentikasi email/kata sandi standar dengan alur lupa kata sandi melalui token email.
3. **Three-Tier Role System** — Muzakki (bawaan, tanpa peran eksplisit), petugas UPZIS, dan administrator dengan kontrol akses yang diberlakukan oleh middleware.
4. **Menu untuk Administrator** — Administrator dapat mencari pengguna dan menetapkan/mengubah peran melalui halaman pengelolaan khusus.

### User Workflows

1. **Pendaftaran mandiri muzakki** — Daftar → verifikasi email → masuk → akses dashboard, pendaftaran keluarga, dan pengajuan zakat.
2. **Penetapan peran oleh admin** — Buka halaman pengelolaan peran → cari pengguna → tetapkan peran (upzis atau administrator) → pengguna mendapatkan akses lebih tinggi pada pemuatan halaman berikutnya.
3. **Pemulihan kata sandi** — Klik lupa kata sandi → terima email reset (token berlaku 60 menit, dibatasi 1 per 60 detik) → atur kata sandi baru → masuk.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Tingkat penyelesaian pendaftaran | > 90% dari pendaftaran yang dimulai | Pengguna terverifikasi / total pengguna terdaftar |
| Kesalahan penetapan peran | 0 | Tidak ada pengguna yang salah mendapat peran |

---

## Scope

### In Scope (v1.0)

- [x] Pendaftaran pengguna (nama, email, kata sandi)
- [x] Verifikasi email (wajib verifikasi sebelum akses dashboard)
- [x] Login / logout dengan autentikasi berbasis sesi
- [x] Reset kata sandi melalui token email
- [x] Konfirmasi kata sandi untuk tindakan sensitif
- [x] Tiga peran: administrator, upzis, muzakki (implisit — tidak ada catatan peran)
- [x] Middleware `EnsureUserHasRole` untuk perlindungan rute
- [x] Halaman pengelolaan peran khusus admin dengan pencarian pengguna
- [x] Penerapan satu peran per pengguna saat penetapan (lepas semua, lampirkan satu)
- [x] Peran di-eager-load dan dibagikan ke semua halaman Inertia melalui `HandleInertiaRequests`

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| OAuth / social login | Kesederhanaan; pengguna komunitas lebih memilih email | TBD |
| Multi-factor authentication | Konteks berisiko rendah (donasi masjid) | TBD |
| Granularitas izin di luar peran | Tiga peran sudah cukup untuk kebutuhan saat ini | TBD |
| Pengeditan profil pengguna | Tidak diperlukan untuk alur kerja v1 | TBD |
| Penghapusan akun / deaktivasi mandiri | Tidak diwajibkan regulasi untuk skala ini | TBD |
| API token authentication | Sanctum terpasang tetapi tidak digunakan untuk rute web | TBD |

### Future (This Capability)

- Tidak ada yang direncanakan — kapabilitas sudah stabil.

---

## User Stories

### Registration & Login

| Story | Priority | Link |
|-------|----------|------|
| Muzakki mendaftar dan memverifikasi email | P0 | — |
| Pengguna masuk dengan email dan kata sandi | P0 | — |
| Pengguna mereset kata sandi yang terlupakan melalui email | P0 | — |

### Role Management

| Story | Priority | Link |
|-------|----------|------|
| Admin menetapkan peran upzis kepada pengguna | P0 | — |
| Admin mencari pengguna berdasarkan nama | P1 | — |
| Admin melihat semua pengguna vs hanya pengguna yang memiliki peran | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Kedaluwarsa token reset kata sandi | 60 menit | Menyeimbangkan keamanan dengan kemudahan penggunaan |
| Throttle reset kata sandi | 1 permintaan per 60 detik | Mencegah spam email |
| Batas waktu konfirmasi kata sandi | 3 jam (10.800 detik) | Mengurangi hambatan untuk sesi admin |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Role | Tingkatan akses: `administrator` (akses penuh), `upzis` (operasi petugas), atau muzakki implisit (bawaan, tidak ada catatan peran) |
| Panitia | Sinonim untuk petugas UPZIS dalam konteks antarmuka |

---

## Technical Considerations

- Dibangun di atas Laravel Breeze (autentikasi sesi, tumpukan Inertia/Vue)
- Laravel Sanctum terpasang (`HasApiTokens` pada model User) tetapi hanya autentikasi sesi yang digunakan
- Pengecekan peran dilakukan di level aplikasi (bukan gates/guards Laravel) — metode `User::hasRole()` dan `User::hasAnyRole()`
- Tabel pivot `role_user` dengan primary key komposit dan cascade delete
- Notifikasi `MailResetPasswordToken` kustom untuk email reset kata sandi

---

## Open Questions

Tidak ada — kapabilitas sudah dikirim dan stabil.

---

## RFCs

- [RFC-001: V1 System Design](../rfc/001-system-design.md) — Arsitektur keseluruhan sistem termasuk autentikasi dan otorisasi

---

## Changelog

### Version 1.0 — 2026-02-27
- Dokumentasi retroaktif dari kapabilitas yang sudah dikirim

---

## References

- [Laravel Breeze documentation](https://laravel.com/docs/8.x/starter-kits#laravel-breeze)
- [Product vision](./_index.md)
