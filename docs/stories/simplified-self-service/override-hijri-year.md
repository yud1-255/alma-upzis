# Story: Override Tahun Hijriah oleh Admin

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P1 | Not Started |

---

## User Story

**As a** admin UPZIS
**I want** meng-override tahun Hijriah yang digunakan sistem melalui AppConfig
**So that** saya dapat mengoreksi tahun Hijriah jika auto-detect tidak sesuai dengan kalender resmi

---

## Acceptance Criteria

```gherkin
Scenario: Admin mengaktifkan override tahun Hijriah
  GIVEN admin berada di halaman AppConfig
    AND toggle override tahun Hijriah dalam kondisi off (auto-detect aktif)
  WHEN admin mengaktifkan toggle override
    AND admin memasukkan tahun Hijriah "1447"
    AND admin menyimpan perubahan
  THEN sistem menggunakan tahun 1447 untuk semua transaksi baru, filter, dan laporan
    AND auto-detect tidak digunakan selama override aktif

Scenario: Admin menonaktifkan override — kembali ke auto-detect
  GIVEN admin telah mengaktifkan override tahun Hijriah = 1447
  WHEN admin menonaktifkan toggle override
    AND admin menyimpan perubahan
  THEN sistem kembali menggunakan auto-detect
    AND tahun Hijriah yang ditampilkan sesuai dengan hasil konversi tanggal saat ini

Scenario: Validasi input — tahun Hijriah tidak valid
  GIVEN admin berada di halaman AppConfig
    AND toggle override aktif
  WHEN admin memasukkan nilai bukan angka (misalnya "abc")
  THEN sistem menampilkan pesan validasi "Tahun Hijriah harus berupa angka"
    AND perubahan tidak disimpan

Scenario: Validasi input — tahun Hijriah di luar rentang wajar
  GIVEN admin berada di halaman AppConfig
    AND toggle override aktif
  WHEN admin memasukkan tahun Hijriah yang tidak wajar (misalnya "1000" atau "2000")
  THEN sistem menampilkan pesan peringatan bahwa nilai di luar rentang wajar
    AND admin tetap dapat menyimpan jika yakin (warning, bukan blocking)

Scenario: Override berlaku system-wide
  GIVEN admin telah meng-set override tahun Hijriah = 1447
  WHEN muzakki mengajukan zakat via formulir sederhana
    AND muzakki lain mengajukan zakat via alur V1
    AND admin melihat laporan
  THEN semua menggunakan tahun Hijriah 1447
```

---

## UI/UX Notes

- Toggle on/off sederhana di halaman AppConfig yang sudah ada
- Saat toggle off, field input tahun Hijriah di-disable dan menampilkan nilai auto-detect sebagai placeholder
- Operasi ini jarang dilakukan (sekali per tahun jika ada koreksi)

---

## Technical Notes

- Override disimpan di AppConfig dengan key `hijri_year`
- Jika toggle off, nilai `hijri_year` di AppConfig di-set null/kosong
- Logika fallback: `AppConfig::getConfigValue('hijri_year') ?? HijriDate::currentYear()`

---

## Out of Scope

- Override per-transaksi (override berlaku global)
- Audit log perubahan override
- Notifikasi ke admin lain saat override berubah

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [auto-hijri-year](./auto-hijri-year.md)
