# PRD: Application Configuration

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C5 (dari [product vision](./_index.md))

**One-liner:** Menyediakan konfigurasi runtime yang dikelola administrator untuk pengaturan tahun Hijriah, nominal fitrah, info rekening bank, periode tampilan pembayaran, dan batas laju pencarian.

**Dependencies:**
- Requires: C1 (User Authentication & Role Management)
- Enables: C3 (Zakat Transaction Management — tahun Hijriah, nominal fitrah, tampilan pembayaran, konteks nominal unik)

---

## Problem

**What:** Parameter pengumpulan zakat berubah setiap tahun — tahun Hijriah bertambah, nominal fitrah dapat diperbarui, rekening bank bisa berubah, dan opsi pembayaran QRIS/transfer perlu ditampilkan atau disembunyikan berdasarkan waktu periode pengumpulan. Pengaturan ini harus dapat disesuaikan tanpa perubahan kode.

**Who:** Administrator.

**Current State:** Pengaturan akan di-hardcode atau dikelola melalui pengeditan database langsung, yang mengharuskan keterlibatan pengembang untuk perubahan tahunan yang rutin.

---

## Solution

### Key Features

1. **Hijri Year Settings** — Tahun Hijriah aktif saat ini dan tahun awal (mendefinisikan rentang yang dapat dipilih untuk laporan dan transaksi).
2. **Fitrah Amount Options** — Beberapa nominal Rp yang dapat dikonfigurasi untuk dipilih muzakki saat memasukkan zakat fitrah (disimpan sebagai beberapa baris dengan kunci `fitrah_amount`).
3. **Bank Account Info** — Nomor rekening bank yang ditampilkan pada detail transaksi untuk instruksi transfer daring.
4. **Payment Display Periods** — Rentang tanggal yang mengontrol kapan opsi pembayaran QRIS dan transfer bank ditampilkan/disembunyikan pada detail transaksi.
5. **Contact Information** — Nomor telepon konfirmasi (WhatsApp) yang ditampilkan bagi muzakki untuk menghubungi UPZIS.
6. **KK Lookup Rate Limit** — Jumlah maksimum pencarian nomor KK yang diperbolehkan per pengguna.

### Configuration Keys

| Key | Description | Example Value | Multi-value |
|-----|-------------|---------------|-------------|
| `hijri_year` | Tahun Hijriah aktif saat ini | `1447` | No |
| `hijri_year_beginning` | Tahun Hijriah paling awal yang dapat dipilih | `1444` | No |
| `fitrah_amount` | Nominal fitrah Rp yang dapat dipilih | `35000`, `40000`, `45000` | Yes |
| `bank_account` | Nomor rekening bank untuk transfer | `1234567890 (BSI)` | No |
| `confirmation_phone` | Nomor kontak WhatsApp | `08123456789` | No |
| `remove_qr_start_date` | Tanggal mulai menyembunyikan opsi QRIS | `2026-03-20` | No |
| `remove_qr_end_date` | Tanggal akhir menyembunyikan opsi QRIS | `2026-04-05` | No |
| `remove_transfer_start_date` | Tanggal mulai menyembunyikan opsi transfer | `2026-03-28` | No |
| `remove_transfer_end_date` | Tanggal akhir menyembunyikan opsi transfer | `2026-04-05` | No |
| `check_kk_limit` | Maks pencarian KK per pengguna | `5` | No |

### User Workflows

1. **Memperbarui pengaturan** — Admin membuka halaman Konfigurasi Aplikasi → melihat tabel semua kunci dan nilai konfigurasi → mengedit nilai secara langsung → klik simpan → nilai diperbarui seketika.
2. **Pergantian tahunan** — Di awal periode pengumpulan baru, admin memperbarui `hijri_year`, meninjau nilai `fitrah_amount`, dan menyesuaikan rentang tanggal tampilan pembayaran.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Perubahan konfigurasi | Tidak perlu deployment kode | Admin dapat memperbarui semua pengaturan melalui antarmuka |
| Waktu pergantian tahunan | < 10 menit | Waktu admin memperbarui semua pengaturan spesifik tahun |

---

## Scope

### In Scope (v1.0)

- [x] Tabel `app_config` berupa key-value (kunci, nilai — kunci tidak unik, memungkinkan beberapa baris per kunci)
- [x] Halaman pengelolaan konfigurasi khusus admin (tabel dengan edit langsung per baris)
- [x] `hijri_year` — tahun Hijriah aktif saat ini untuk transaksi
- [x] `hijri_year_beginning` — mendefinisikan awal rentang tahun Hijriah yang dapat dipilih
- [x] `fitrah_amount` — beberapa nilai untuk pemilih nominal fitrah Rp (beberapa baris dengan kunci yang sama)
- [x] `bank_account` — rekening bank yang ditampilkan pada detail transaksi
- [x] `confirmation_phone` — kontak WhatsApp yang ditampilkan untuk muzakki
- [x] `remove_qr_start_date` / `remove_qr_end_date` — rentang tanggal untuk menyembunyikan opsi pembayaran QRIS
- [x] `remove_transfer_start_date` / `remove_transfer_end_date` — rentang tanggal untuk menyembunyikan opsi transfer bank
- [x] `check_kk_limit` — batas laju per pengguna untuk pencarian nomor KK
- [x] `AppConfig::getConfigValue()` — mengambil satu nilai berdasarkan kunci
- [x] `AppConfig::getConfigValues()` — mengambil semua nilai untuk sebuah kunci (dukungan multi-nilai)
- [x] AppConfigPolicy yang membatasi semua operasi untuk peran administrator
- [x] Operasi delete diblokir di level policy (mengembalikan false)

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Versioning / riwayat konfigurasi | Frekuensi perubahan rendah tidak membenarkan jejak audit pada konfigurasi | TBD |
| Aturan validasi konfigurasi per kunci | Nilai berupa string sederhana; admin dipercaya | TBD |
| Impor/ekspor konfigurasi | Jumlah pengaturan sedikit; pengelolaan manual sudah cukup | TBD |
| Override konfigurasi berbasis environment | Satu lingkungan deployment | Never |
| Pengelompokan atau kategori konfigurasi di antarmuka | Daftar datar sudah cukup untuk ~10 kunci | TBD |

### Future (This Capability)

- Tidak ada yang direncanakan — kapabilitas sudah stabil.

---

## User Stories

### Configuration Management

| Story | Priority | Link |
|-------|----------|------|
| Admin memperbarui tahun Hijriah untuk periode pengumpulan baru | P0 | — |
| Admin memperbarui opsi nominal fitrah | P0 | — |
| Admin menetapkan rentang tanggal tampilan pembayaran | P1 | — |
| Admin memperbarui informasi rekening bank | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Kontrol akses | Khusus administrator, tanpa delete | Mencegah penghapusan tidak sengaja dari kunci konfigurasi yang diperlukan |
| Ketersediaan | Nilai konfigurasi di-cache per permintaan melalui metode model statis | Menghindari kueri DB berulang dalam satu permintaan |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Hijri Year | Tahun kalender lunar Islam yang digunakan sebagai dimensi waktu utama untuk periode pengumpulan zakat |
| Fitrah Amount | Nominal Rp standar yang mewakili setara uang dari zakat fitrah (mis., 35000, 40000, 45000) |
| Payment Display Period | Rentang tanggal selama metode pembayaran (QRIS atau transfer bank) disembunyikan dari halaman detail transaksi |

---

## Technical Considerations

- Tabel `app_config` mengizinkan beberapa baris dengan kunci yang sama — ini disengaja untuk `fitrah_amount` yang memiliki beberapa nilai yang dapat dipilih
- Tidak ada unique constraint pada kolom `key` secara desain
- `getConfigValue()` mengembalikan satu string; `getConfigValues()` mengembalikan array (nilai yang di-pluck)
- Logika tampilan pembayaran membandingkan tanggal saat ini dengan `remove_*_start_date` / `remove_*_end_date` di metode show ZakatController
- Semua nilai konfigurasi disimpan sebagai string; konversi tipe terjadi di titik konsumsi

---

## Open Questions

Tidak ada — kapabilitas sudah dikirim dan stabil.

---

## RFCs

- [RFC-001: V1 System Design](../rfc/001-system-design.md) — Arsitektur keseluruhan sistem termasuk runtime configuration

---

## Changelog

### Version 1.0 — 2026-02-27
- Dokumentasi retroaktif dari kapabilitas yang sudah dikirim

---

## References

- [Product vision](./_index.md)
- [C1: User Authentication & Role Management](./001-auth-roles.md)
