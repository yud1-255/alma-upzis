# Story: Penentuan Tahun Hijriah Otomatis

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** sistem (atas nama admin UPZIS)
**I want** tahun Hijriah ditentukan secara otomatis dari tanggal server saat ini
**So that** admin tidak perlu mengkonfigurasi tahun Hijriah secara manual setiap tahun dan risiko lupa/salah tagging transaksi dihilangkan

---

## Acceptance Criteria

```gherkin
Scenario: Transaksi baru mendapat tahun Hijriah otomatis
  GIVEN tidak ada override tahun Hijriah di AppConfig (toggle off atau nilai kosong)
    AND tanggal server saat ini adalah 6 Maret 2026 (Hijriah: 1447)
  WHEN muzakki mengajukan transaksi zakat baru
  THEN transaksi mendapat tag tahun Hijriah 1447
    AND admin tidak perlu melakukan konfigurasi apapun

Scenario: Auto-detect berlaku system-wide — filter daftar transaksi
  GIVEN tidak ada override tahun Hijriah di AppConfig
    AND tahun Hijriah saat ini adalah 1447
  WHEN admin membuka daftar transaksi
  THEN filter tahun Hijriah default menunjukkan 1447

Scenario: Auto-detect berlaku system-wide — laporan
  GIVEN tidak ada override tahun Hijriah di AppConfig
    AND tahun Hijriah saat ini adalah 1447
  WHEN admin membuka halaman laporan
  THEN tahun Hijriah default untuk laporan adalah 1447

Scenario: Pergantian tahun Hijriah otomatis
  GIVEN tahun Hijriah saat ini adalah 1447
    AND tanggal server berubah melewati 1 Muharram (awal tahun Hijriah baru)
  WHEN transaksi baru dibuat setelah pergantian tahun
  THEN transaksi mendapat tag tahun Hijriah 1448
    AND tidak perlu intervensi admin

Scenario: Override aktif — auto-detect tidak digunakan
  GIVEN admin telah meng-set override tahun Hijriah = 1447 di AppConfig
    AND tahun Hijriah auto-detect saat ini sebenarnya 1448
  WHEN transaksi baru dibuat
  THEN transaksi mendapat tag tahun Hijriah 1447 (dari override, bukan auto-detect)

Scenario: AppConfig hijri_year kosong — fallback ke auto-detect
  GIVEN admin pernah meng-set override tetapi kemudian menghapus/mengosongkan nilai
  WHEN transaksi baru dibuat
  THEN sistem menggunakan tahun Hijriah dari auto-detect
```

---

## UI/UX Notes

- Tidak ada perubahan UI khusus untuk pengguna/muzakki — ini adalah perubahan backend
- Admin melihat tahun Hijriah aktif di halaman AppConfig (menunjukkan apakah auto-detect atau override)

---

## Technical Notes

- Logika: `AppConfig::getConfigValue('hijri_year') ?? HijriDate::currentYear()`
- Library konversi: `geniusts/hijri-dates` (Carbon-based, cocok dengan Laravel)
- Perbedaan algoritmik +/- 1-2 hari di batas tahun tidak signifikan — admin override sebagai safety net
- Semua titik yang membaca tahun Hijriah harus melalui satu helper/method yang menerapkan logika fallback
- `hijri_year_beginning` tetap dikonfigurasi manual (untuk rentang dropdown filter)

---

## Out of Scope

- Konversi tanggal Hijriah presisi (hanya tahun yang diperlukan)
- Kalender Hijriah interaktif di UI
- Notifikasi otomatis saat pergantian tahun Hijriah

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Task:** [rfc-002/002](../../tasks/rfc-002/002-hijri-year-helper.md)
- **Related Stories:** [override-hijri-year](./override-hijri-year.md)
