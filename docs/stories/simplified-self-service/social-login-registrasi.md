# Story: Registrasi dan Login via Social Login

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** pengguna baru yang belum memiliki akun
**I want** mendaftar dan masuk menggunakan akun Google atau Facebook saya
**So that** saya tidak perlu membuat password baru dan bisa langsung menggunakan aplikasi dalam 1 klik

---

## Acceptance Criteria

```gherkin
Scenario: Registrasi dan login via Google berhasil
  GIVEN pengguna belum memiliki akun di sistem
    AND pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Google"
    AND pengguna menyelesaikan autentikasi di popup Google OAuth
    AND Google mengembalikan email yang terverifikasi
  THEN sistem membuat akun baru dengan email dari Google
    AND pengguna mendapat peran muzakki secara implisit
    AND pengguna diarahkan ke dashboard
    AND proses dari klik tombol hingga masuk dashboard memakan waktu < 3 detik

Scenario: Registrasi dan login via Facebook berhasil
  GIVEN pengguna belum memiliki akun di sistem
    AND pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Facebook"
    AND pengguna menyelesaikan autentikasi di popup Facebook Login
    AND Facebook mengembalikan email yang terverifikasi
  THEN sistem membuat akun baru dengan email dari Facebook
    AND pengguna mendapat peran muzakki secara implisit
    AND pengguna diarahkan ke dashboard

Scenario: Login berulang via social login
  GIVEN pengguna sudah pernah mendaftar via Google
    AND pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Google"
    AND autentikasi Google berhasil
  THEN pengguna langsung masuk ke akun yang sudah ada
    AND tidak ada akun baru yang dibuat

Scenario: Pengguna membatalkan autentikasi di popup OAuth
  GIVEN pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Google"
    AND pengguna menutup popup autentikasi tanpa menyelesaikan proses
  THEN pengguna tetap berada di halaman login
    AND tidak ada akun yang dibuat
    AND tidak ada pesan error yang mengganggu

Scenario: Social provider tidak mengembalikan email terverifikasi
  GIVEN pengguna mendaftar via Facebook
    AND akun Facebook pengguna tidak memiliki email terverifikasi (misalnya akun berbasis nomor telepon)
  WHEN pengguna menyelesaikan autentikasi Facebook
  THEN sistem meminta pengguna memverifikasi email melalui alur verifikasi email standar (V1)
    AND akun dibuat dengan status email belum terverifikasi

Scenario: OAuth provider mengembalikan error
  GIVEN pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Google"
    AND Google mengembalikan error (misalnya server error)
  THEN pengguna diarahkan kembali ke halaman login
    AND sistem menampilkan pesan "Login gagal. Silakan coba lagi."

Scenario: Tombol social login tersedia di halaman registrasi
  GIVEN pengguna berada di halaman registrasi
  WHEN halaman selesai dimuat
  THEN tombol "Masuk dengan Google" dan "Masuk dengan Facebook" ditampilkan
    AND pengguna dapat mendaftar melalui salah satu tombol tersebut
```

---

## UI/UX Notes

- Tombol social login ditampilkan di halaman login DAN halaman registrasi
- Gunakan ikon resmi Google dan Facebook sesuai brand guidelines masing-masing
- Posisi tombol social login di atas atau di bawah form login email/password dengan pemisah "atau"

---

## Technical Notes

- Implementasi menggunakan OAuth 2.0 melalui Laravel Socialite
- Percayai klaim `email_verified` dari provider; jika `false` atau tidak ada, fallback ke alur verifikasi email V1
- Peran muzakki diberikan secara implisit saat akun dibuat (sama dengan registrasi email biasa)

---

## Out of Scope

- Login via Apple ID (planned V2)
- Manajemen multiple social accounts per user
- Unlinking social account dari profil pengguna

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [social-login-account-linking](./social-login-account-linking.md)
