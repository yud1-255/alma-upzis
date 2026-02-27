# PRD: Reporting & Data Export

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C4 (dari [product vision](./_index.md))

**One-liner:** Menyediakan enam tampilan laporan dengan pencarian, filter tahun Hijriah, dan paginasi, ditambah lima jenis ekspor Excel dan kwitansi yang dioptimalkan untuk cetak, bagi petugas UPZIS dan administrator.

**Dependencies:**
- Requires: C3 (Zakat Transaction Management)
- Enables: Tidak ada — kapabilitas terminal dalam rantai ketergantungan

---

## Problem

**What:** Setelah mengumpulkan zakat, petugas UPZIS perlu membuat laporan ringkasan untuk pengurus masjid, merekonsiliasi pengumpulan harian, melacak kontribusi muzakki individu, dan menindaklanjuti pembayaran daring yang belum dikonfirmasi. Pelaporan berbasis kertas melelahkan dan rawan kesalahan.

**Who:** Petugas UPZIS dan administrator.

**Current State:** Kompilasi spreadsheet manual dari buku catatan kertas, yang memakan waktu berjam-jam per periode pelaporan.

---

## Solution

### Key Features

1. **6 Tampilan Laporan** — Masing-masing dapat diakses sebagai halaman khusus dengan pencarian, filter tahun Hijriah, dan paginasi.
2. **5 Jenis Ekspor Excel** — Unduhan sekali klik dari spreadsheet berformat dengan header multi-baris untuk rincian yang kompleks.
3. **Kwitansi Siap Cetak** — Tata letak kwitansi rangkap dua untuk konfirmasi transaksi, dapat dicetak dari detail transaksi.

### Report Views

| # | Report | Route | Description |
|---|--------|-------|-------------|
| 1 | Transaction Recap | `/zakat/transaction_recap` | Rekapitulasi nominal per transaksi di semua jenis zakat |
| 2 | Daily Transaction Recap | `/zakat/daily_recap` | Data yang sama diurutkan berdasarkan payment_date untuk rekonsiliasi harian |
| 3 | Muzakki Recap | `/zakat/muzakki_recap` | Rincian kontribusi per baris muzakki |
| 4 | Daily Muzakki Recap | `/zakat/daily_muzakki_recap` | Data yang sama diurutkan berdasarkan payment_date |
| 5 | Muzakki List | `/zakat/muzakki_list` | Semua keluarga beserta muzakki dan akun pengguna yang terhubung |
| 6 | Online Payments | `/zakat/online_payments` | Transaksi daring saja dengan info kontak untuk tindak lanjut |

### Excel Export Types

| # | Export | Filename | Key Columns |
|---|--------|----------|-------------|
| 1 | Summary | `zakat.xlsx` | Nomor transaksi, tanggal, receive_from, petugas, periode, kepala keluarga, saluran, total |
| 2 | Transaction Recap | `transaction_recap.xlsx` | Nomor transaksi, semua 8 nominal jenis zakat, nominal unik, total, tanggal, receive_from |
| 3 | Muzakki List | `muzakki_list.xlsx` | Kepala keluarga, alamat, kontak, nama muzakki, email/nama akun pengguna |
| 4 | Muzakki Recap | `muzakki_recap.xlsx` | Nomor transaksi, nama muzakki, petugas, semua nominal jenis zakat, tanggal, saluran |
| 5 | Online Payments | `online_payments.xlsx` | Nomor transaksi, tanggal, receive_from, telepon, email, petugas konfirmasi, total |

### User Workflows

1. **Lihat laporan** — Buka halaman laporan → pilih tahun Hijriah → opsional cari berdasarkan nama → telusuri hasil yang dipaginasi.
2. **Ekspor ke Excel** — Di halaman laporan atau daftar transaksi mana pun → klik tombol ekspor → pilih tahun Hijriah → unduh file Excel.
3. **Cetak kwitansi** — Lihat detail transaksi → klik "Cetak" (cetak) atau "Cetak Rangkap" (dua salinan) → dialog cetak browser terbuka dengan tata letak yang dioptimalkan.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Waktu pembuatan laporan | < 5 detik untuk pemuatan halaman | Waktu respons server untuk rute laporan |
| Waktu pembuatan ekspor | < 30 detik untuk unduhan Excel | Waktu dari klik hingga file terunduh |
| Akurasi laporan | Cocok 100% dengan data transaksi | Periksa spot total yang diekspor terhadap daftar transaksi |

---

## Scope

### In Scope (v1.0)

- [x] Tampilan laporan Transaction Recap (rekapitulasi per transaksi, dapat dicari, filter tahun Hijriah, dipaginasi)
- [x] Tampilan laporan Daily Transaction Recap (diurutkan berdasarkan payment_date lalu created_at)
- [x] Tampilan laporan Muzakki Recap (rincian baris per muzakki)
- [x] Tampilan laporan Daily Muzakki Recap (diurutkan berdasarkan payment_date)
- [x] Tampilan laporan Muzakki List (keluarga beserta muzakki dan pengguna terhubung, dipaginasi)
- [x] Tampilan laporan Online Payments (transaksi daring saja dengan telepon/email, tombol konfirmasi)
- [x] Ekspor Excel Summary (`zakat.xlsx`)
- [x] Ekspor Excel Transaction Recap (`transaction_recap.xlsx`, header 2 baris)
- [x] Ekspor Excel Muzakki List (`muzakki_list.xlsx`)
- [x] Ekspor Excel Muzakki Recap (`muzakki_recap.xlsx`, header 2 baris)
- [x] Ekspor Excel Online Payments (`online_payments.xlsx`)
- [x] Kwitansi transaksi siap cetak (salinan tunggal dan rangkap)
- [x] Pencarian berdasarkan nama di semua tampilan laporan
- [x] Filter tahun Hijriah di semua tampilan laporan
- [x] Semua laporan dibatasi untuk peran upzis dan administrator
- [x] Ekspor menggunakan Maatwebsite Excel dengan kolom yang diubah ukuran otomatis dan header berformat

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Ekspor PDF | Excel adalah format standar untuk pelaporan masjid | TBD |
| Grafik / visualisasi dashboard | Laporan bersifat tabular; tidak ada persyaratan grafis | TBD |
| Laporan terjadwal / melalui email | Petugas membuat laporan sesuai kebutuhan | TBD |
| Laporan perbandingan lintas tahun | Satu tahun Hijriah per laporan sudah cukup | TBD |
| Pembuat laporan kustom | Jenis laporan tetap mencakup semua kebutuhan saat ini | TBD |

### Future (This Capability)

- Tidak ada yang direncanakan — kapabilitas sudah stabil.

---

## User Stories

### Report Viewing

| Story | Priority | Link |
|-------|----------|------|
| Petugas UPZIS melihat rekap transaksi untuk tahun Hijriah saat ini | P0 | — |
| Petugas UPZIS melihat rekap harian untuk rekonsiliasi | P0 | — |
| Petugas UPZIS melihat pembayaran daring untuk menindaklanjuti transfer yang belum dikonfirmasi | P0 | — |
| Admin melihat daftar muzakki di semua keluarga | P1 | — |

### Export

| Story | Priority | Link |
|-------|----------|------|
| Petugas UPZIS mengekspor rekap transaksi ke Excel | P0 | — |
| Petugas UPZIS mengekspor rekap muzakki ke Excel | P1 | — |
| Petugas UPZIS mengekspor pembayaran daring untuk tindak lanjut via telepon | P1 | — |

### Print

| Story | Priority | Link |
|-------|----------|------|
| Petugas UPZIS mencetak kwitansi untuk transaksi yang dikonfirmasi | P0 | — |
| Petugas UPZIS mencetak kwitansi rangkap | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Ukuran file ekspor | Menangani hingga 10.000 baris | Cukup untuk pengumpulan tahunan satu masjid |
| Akses laporan | Dibatasi untuk peran upzis dan administrator | Data keuangan bersifat sensitif |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Transaction Recap | Tampilan teragregasi yang menunjukkan jumlah nominal per transaksi di semua jenis zakat |
| Muzakki Recap | Tampilan tingkat baris yang menunjukkan kontribusi muzakki individu dalam setiap transaksi |
| Daily Recap | Data yang sama dengan rekap standar tetapi diurutkan berdasarkan payment_date untuk rekonsiliasi harian |
| Online Payments | Tampilan terfilter yang menampilkan hanya transaksi daring (non-gerai) beserta info kontak donatur |

---

## Technical Considerations

- Semua ekspor menggunakan paket Maatwebsite Excel (`FromQuery`, `WithMapping`, `WithHeadings`, `WithColumnFormatting`, `WithStyles`, `ShouldAutoSize`)
- Ekspor Transaction Recap dan Muzakki Recap menggunakan header 2 baris untuk mengelompokkan sub-kolom (mis., Fitrah → Rp/Kg/Lt)
- Data laporan berasal dari metode kueri `ZakatDomain`, bukan SQL mentah
- Rekap harian tidak memiliki paginasi (seluruh dataset dirender) — perlu perhatian jika volume data bertumbuh
- Rute ekspor menerima parameter `hijriYear` opsional; default ke tahun Hijriah saat ini dari AppConfig

---

## Open Questions

Tidak ada — kapabilitas sudah dikirim dan stabil.

---

## RFCs

- [RFC-001: V1 System Design](../rfc/001-system-design.md) — Arsitektur keseluruhan sistem termasuk export system

---

## Changelog

### Version 1.0 — 2026-02-27
- Dokumentasi retroaktif dari kapabilitas yang sudah dikirim

---

## References

- [Product vision](./_index.md)
- [C3: Zakat Transaction Management](./003-zakat-transactions.md)
- [Maatwebsite Excel documentation](https://docs.laravel-excel.com/3.1/)
