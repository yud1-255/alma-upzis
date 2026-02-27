# PRD: Family & Muzakki Registration

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C2 (dari [product vision](./_index.md))

**One-liner:** Memungkinkan pendaftaran dan pengelolaan unit keluarga beserta anggota muzakki individu mereka, mendukung warga BPI maupun anggota komunitas di luar BPI.

**Dependencies:**
- Requires: C1 (User Authentication & Role Management)
- Enables: C3 (Zakat Transaction Management)

---

## Problem

**What:** Zakat dikumpulkan per individu (muzakki) namun diorganisasi per keluarga. Sistem perlu mengetahui siapa yang membayar, keluarga mana yang mereka ikuti, dan di mana mereka tinggal — khususnya bagi warga BPI yang alamatnya mengikuti skema nomor blok/rumah.

**Who:** Muzakki (mendaftarkan keluarga mereka sendiri) dan petugas UPZIS (mendaftarkan keluarga atas nama donatur yang datang ke gerai).

**Current State:** Formulir kertas mencatat informasi keluarga dan muzakki per transaksi, menyebabkan entri duplikat, nama yang tidak konsisten, dan tidak ada cara untuk melacak riwayat muzakki lintas tahun.

---

## Solution

### Key Features

1. **Family CRUD** — Membuat dan memperbarui data keluarga dengan kepala keluarga, telepon, nomor KK, dan alamat (blok/rumah BPI atau teks bebas).
2. **BPI Address System** — Entri alamat terstruktur untuk warga BPI dengan pemilih blok (A, B, C, E, F, G) dan nomor rumah yang menghasilkan format alamat otomatis.
3. **Muzakki Management** — Menambahkan beberapa muzakki per keluarga, dengan nama individu dan opsi alamat berbeda. Deaktivasi lunak (bukan hapus permanen) menjaga riwayat transaksi.
4. **KK Number Lookup** — Pencarian keluarga yang sudah ada berdasarkan nomor Kartu Keluarga, dibatasi per pengguna untuk mencegah penyalahgunaan.
5. **Address Inheritance** — Muzakki dapat mewarisi alamat keluarga (`use_family_address=true`). Perubahan alamat keluarga berdampak pada semua muzakki yang mewarisinya.
6. **Family-to-User Linking** — Menghubungkan data keluarga dengan akun pengguna, memungkinkan pengajuan zakat secara mandiri.
7. **UPZIS Family Registration** — Petugas dapat membuat keluarga baru secara langsung saat entri zakat, lalu diarahkan kembali ke formulir transaksi.

### User Workflows

1. **Muzakki mendaftarkan keluarga** — Masuk → buka halaman pendaftaran keluarga → isi kepala keluarga, telepon, alamat (pemilih BPI atau manual) → opsional isi nomor KK → tambah anggota muzakki → simpan.
2. **Muzakki menambah anggota keluarga** — Di halaman keluarga → masukkan nama → aktifkan "gunakan alamat keluarga" atau masukkan alamat kustom → simpan. Anggota muncul di daftar muzakki untuk pengajuan zakat.
3. **Pencarian KK** — Masukkan nomor KK → sistem menemukan keluarga yang sudah ada → pengguna menghubungkan diri ke keluarga tersebut (menghindari pendaftaran duplikat).
4. **Pendaftaran langsung oleh UPZIS** — Saat entri zakat, pencarian tidak menemukan kecocokan → klik daftar → buat keluarga → diarahkan kembali ke formulir zakat dengan keluarga baru yang sudah terpilih.
5. **Menonaktifkan muzakki** — Di halaman keluarga → hapus muzakki → mengatur `is_active=false` (menjaga transaksi historis).

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Keluarga duplikat | < 5% | Keluarga dengan nomor KK atau nama kepala keluarga yang sama |
| Penyelesaian pendaftaran keluarga | > 95% | Keluarga dengan setidaknya satu muzakki aktif |

---

## Scope

### In Scope (v1.0)

- [x] Pembuatan keluarga dengan head_of_family, phone, address, flag is_bpi
- [x] Pemilih blok BPI: A(1-20), B(1-23), C(1-20), E(1-24), F(1-19), G(1-11)
- [x] Pemilih nomor rumah BPI: 0-20
- [x] Format alamat BPI otomatis: "Bukit Pamulang Indah {blok} no {rumah}"
- [x] Alamat teks bebas untuk warga non-BPI
- [x] Kolom nomor KK pada keluarga (opsional)
- [x] Pencarian nomor KK dengan pembatasan per pengguna (`kk_check_count` vs konfigurasi `check_kk_limit`)
- [x] Beberapa muzakki per keluarga
- [x] Kolom muzakki: name, phone (opsional), address, is_bpi, bpi_block_no, bpi_house_no
- [x] Flag `use_family_address` dengan dampak berjenjang saat alamat keluarga diperbarui
- [x] Deaktivasi lunak muzakki (`is_active=false`)
- [x] Asosiasi keluarga-ke-pengguna (`users.family_id`)
- [x] Endpoint penugasan keluarga (menghubungkan keluarga yang ada ke pengguna saat ini)
- [x] Pencarian keluarga UPZIS (autocomplete berdasarkan head_of_family atau nama muzakki, maks 10 hasil)
- [x] Pendaftaran keluarga langsung oleh UPZIS saat entri zakat
- [x] Muzakki bawaan otomatis dibuat dari data kepala keluarga saat pendaftaran
- [x] Validasi: head_of_family wajib, phone wajib, is_bpi wajib, address wajib, bpi_block_no/bpi_house_no wajib jika BPI

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Alat penggabungan / deduplikasi keluarga | Volume rendah tidak membenarkan kompleksitas | TBD |
| Unggah foto atau KTP muzakki | Tidak diperlukan untuk alur pengumpulan saat ini | TBD |
| Pemodelan pohon keluarga / relasi | Daftar anggota datar yang sederhana sudah cukup | TBD |
| Impor massal dari spreadsheet | Migrasi satu kali dilakukan secara manual | TBD |
| Penghapusan permanen muzakki | Deaktivasi lunak menjaga jejak audit | Never |

### Future (This Capability)

- Tidak ada yang direncanakan — kapabilitas sudah stabil.

---

## User Stories

### Family Registration

| Story | Priority | Link |
|-------|----------|------|
| Muzakki membuat keluarga baru | P0 | — |
| Muzakki memperbarui alamat keluarga | P0 | — |
| Petugas UPZIS mendaftarkan keluarga saat entri gerai | P0 | — |
| Pengguna menghubungkan diri ke keluarga yang ada melalui pencarian KK | P1 | — |

### Muzakki Management

| Story | Priority | Link |
|-------|----------|------|
| Muzakki menambah anggota keluarga | P0 | — |
| Muzakki menonaktifkan anggota keluarga | P1 | — |
| Muzakki mengaktifkan/menonaktifkan pewarisan alamat | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Waktu respons pencarian | < 500ms untuk autocomplete keluarga | Alur gerai yang lancar saat puncak pengumpulan |
| Batas pencarian KK | Dapat dikonfigurasi melalui AppConfig (`check_kk_limit`) | Mencegah enumerasi KK secara brute-force |
| Batas hasil pencarian | Maks 10 hasil per kueri | Menjaga autocomplete tetap cepat dan terfokus |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Family | Unit rumah tangga yang diidentifikasi oleh head_of_family, berisi satu atau lebih anggota muzakki |
| Muzakki | Individu yang membayar zakat, selalu tergabung dalam tepat satu keluarga |
| KK (Kartu Keluarga) | Nomor kartu registrasi keluarga Indonesia, digunakan sebagai kunci pencarian |
| BPI | Kompleks perumahan Bukit Pamulang Indah dengan sistem pengalamatan blok/rumah terstruktur |
| use_family_address | Flag yang menunjukkan muzakki mewarisi alamat keluarganya; perubahan berdampak secara otomatis |

---

## Technical Considerations

- Tabel `families` memiliki kolom `kk_number` (nullable) tetapi tidak ada unique constraint — duplikasi mungkin terjadi
- Definisi blok BPI di-hardcode di `ResidenceDomain` (A-G dengan rentang rumah spesifik)
- Logika dampak berjenjang alamat ada di `ResidenceDomain::updateFamilyRegistration()`
- Pencarian keluarga mengkueri baik `families.head_of_family` maupun `muzakkis.name`
- Pembatasan pencarian KK menggunakan counter pada `users.kk_check_count`, bukan throttling level middleware

---

## Open Questions

Tidak ada — kapabilitas sudah dikirim dan stabil.

---

## RFCs

- [RFC-001: V1 System Design](../rfc/001-system-design.md) — Arsitektur keseluruhan sistem termasuk domain layer dan data model

---

## Changelog

### Version 1.0 — 2026-02-27
- Dokumentasi retroaktif dari kapabilitas yang sudah dikirim

---

## References

- [Product vision](./_index.md)
- [C1: User Authentication & Role Management](./001-auth-roles.md)
