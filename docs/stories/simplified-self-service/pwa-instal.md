# Story: Instal dan Gunakan Aplikasi sebagai PWA

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** pengguna yang mengakses aplikasi melalui browser ponsel
**I want** menginstal aplikasi ke layar utama dan membukanya seperti aplikasi native
**So that** saya dapat mengakses aplikasi dengan cepat tanpa perlu mengetik URL dan mendapat pengalaman layar penuh

---

## Acceptance Criteria

```gherkin
Scenario: Browser menampilkan prompt instalasi PWA
  GIVEN pengguna membuka aplikasi melalui browser ponsel yang mendukung PWA (Chrome, Edge, Samsung Internet)
    AND aplikasi memenuhi kriteria installability (manifest valid, service worker aktif, HTTPS)
  WHEN halaman selesai dimuat
  THEN browser menampilkan prompt "Tambahkan ke layar utama" atau banner instalasi
    AND prompt menampilkan nama dan ikon aplikasi

Scenario: Pengguna menginstal PWA berhasil
  GIVEN browser menampilkan prompt instalasi
  WHEN pengguna menekan tombol instal/tambahkan
  THEN aplikasi terinstal di layar utama ponsel
    AND ikon aplikasi muncul di layar utama dengan nama aplikasi
    AND pengguna dapat membuka aplikasi dari ikon tersebut

Scenario: Pengguna membuka aplikasi dari layar utama
  GIVEN pengguna sudah menginstal PWA
  WHEN pengguna mengetuk ikon aplikasi di layar utama
  THEN aplikasi terbuka dalam mode standalone (tanpa address bar browser)
    AND splash screen ditampilkan saat loading
    AND tema warna status bar sesuai branding aplikasi

Scenario: Pengguna menolak prompt instalasi
  GIVEN browser menampilkan prompt instalasi
  WHEN pengguna menutup atau menolak prompt
  THEN pengguna tetap dapat menggunakan aplikasi di browser seperti biasa
    AND prompt tidak muncul lagi dalam sesi yang sama

Scenario: Browser tidak mendukung PWA
  GIVEN pengguna membuka aplikasi di browser yang tidak mendukung PWA (misalnya Safari iOS versi lama)
  WHEN halaman selesai dimuat
  THEN aplikasi tetap berfungsi normal sebagai web biasa
    AND tidak ada error atau degradasi fungsionalitas

Scenario: Aset statis tersedia dari cache setelah instalasi
  GIVEN pengguna sudah menginstal PWA
    AND service worker telah meng-cache aset statis (CSS, JS, gambar)
  WHEN pengguna membuka aplikasi dengan koneksi lambat
  THEN aset statis dimuat dari cache lokal
    AND halaman tetap berfungsi untuk navigasi
    AND fitur yang memerlukan API tetap membutuhkan koneksi internet
```

---

## UI/UX Notes

- Splash screen menampilkan logo dan nama aplikasi dengan warna branding
- Mode standalone menghilangkan address bar browser untuk pengalaman immersive
- Ikon aplikasi harus tersedia dalam resolusi 192x192 dan 512x512

---

## Technical Notes

- Web app manifest (`manifest.json`) harus dikonfigurasi dengan `display: "standalone"`, nama, ikon, dan `theme_color`
- Service worker hanya untuk caching aset statis — bukan untuk offline form submission
- Service worker harus dikonfigurasi agar tidak mengganggu session-based auth (cookie handling)
- HTTPS sudah menjadi prasyarat (dipenuhi oleh deployment)
- Target Lighthouse PWA score > 80

---

## Out of Scope

- Offline form submission (pengajuan zakat tetap memerlukan koneksi internet)
- Push notifications via PWA (planned V2)
- Background sync

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** Tidak ada
