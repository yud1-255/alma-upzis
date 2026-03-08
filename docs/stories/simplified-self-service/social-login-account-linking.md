# Story: Login via Social Login untuk Pengguna Lama

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P1 | Not Started |

---

## User Story

**As a** pengguna yang sudah memiliki akun email/password
**I want** masuk menggunakan akun Google atau Facebook yang memiliki email yang sama
**So that** saya bisa login dengan cara yang lebih mudah tanpa kehilangan data yang sudah ada

---

## Acceptance Criteria

```gherkin
Scenario: Account linking berhasil — email cocok
  GIVEN pengguna sudah memiliki akun dengan email "ali@example.com" yang didaftarkan via email/password
    AND pengguna berada di halaman login
  WHEN pengguna menekan tombol "Masuk dengan Google"
    AND akun Google pengguna menggunakan email "ali@example.com" yang terverifikasi
  THEN sistem menghubungkan akun Google ke akun yang sudah ada
    AND pengguna masuk ke akun yang sudah ada (bukan akun baru)
    AND semua data sebelumnya (keluarga, transaksi) tetap utuh

Scenario: Login berikutnya setelah account linking
  GIVEN pengguna sudah pernah melakukan account linking (email cocok)
  WHEN pengguna login via social login
  THEN pengguna langsung masuk tanpa proses linking ulang

Scenario: Email social login tidak cocok dengan akun manapun
  GIVEN pengguna berada di halaman login
    AND tidak ada akun dengan email yang dikembalikan oleh Google
  WHEN pengguna menekan tombol "Masuk dengan Google"
  THEN sistem membuat akun baru (perilaku registrasi baru standar)

Scenario: Social provider mengembalikan email yang belum terverifikasi — akun dengan email tersebut sudah ada
  GIVEN pengguna sudah memiliki akun dengan email "ali@example.com"
    AND pengguna login via Facebook
    AND akun Facebook mengembalikan email "ali@example.com" tetapi dengan status email_verified=false
  WHEN autentikasi Facebook selesai
  THEN sistem TIDAK menghubungkan ke akun yang sudah ada (risiko keamanan)
    AND sistem meminta pengguna memverifikasi email terlebih dahulu melalui alur V1

Scenario: Pengguna lama login via provider berbeda dari yang pertama
  GIVEN pengguna sudah melakukan account linking via Google
  WHEN pengguna login via Facebook dengan email yang sama
  THEN sistem menghubungkan akun Facebook ke akun yang sama
    AND pengguna masuk ke akun yang sudah ada
```

---

## UI/UX Notes

- Proses account linking terjadi secara otomatis (transparent) — pengguna tidak perlu melakukan aksi tambahan
- Tidak ada UI konfirmasi khusus untuk linking; pengguna langsung masuk ke dashboard

---

## Technical Notes

- Account linking berdasarkan kecocokan email — hanya jika email dari social provider sudah terverifikasi oleh provider
- Jika email belum diverifikasi oleh provider, JANGAN auto-link ke akun yang sudah ada (risiko pengambilalihan akun)
- Simpan social provider ID di tabel terpisah untuk mendukung multiple provider per akun

---

## Out of Scope

- UI untuk mengelola social accounts yang terhubung
- Melepas (unlink) social account dari akun
- Resolusi konflik jika email berubah di sisi provider

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Task:** [rfc-002/003](../../tasks/rfc-002/003-social-login-backend.md)
- **Related Stories:** [social-login-registrasi](./social-login-registrasi.md)
