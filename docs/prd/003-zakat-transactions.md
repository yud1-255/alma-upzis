# PRD: Zakat Transaction Management

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.0 | Shipped | V1 | 2026-02-27 |

---

## Summary

**Capability ID:** C3 (dari [product vision](./_index.md))

**One-liner:** Mengelola siklus penuh transaksi zakat — dari pengajuan hingga konfirmasi dan pembatalan — melalui dua saluran: daring (mandiri oleh muzakki) dan gerai (booth UPZIS).

**Dependencies:**
- Requires: C1 (Auth & Roles), C2 (Family & Muzakki), C5 (App Config — untuk tahun Hijriah, nominal fitrah, periode tampilan pembayaran)
- Enables: C4 (Reporting & Data Export)

---

## Problem

**What:** Pengumpulan zakat melibatkan beberapa jenis donasi per orang, dua saluran pengajuan (transfer daring dan gerai langsung), serta alur konfirmasi untuk pembayaran daring. Pelacakan manual menyebabkan kesalahan, pembayaran yang terlewat, dan rekonsiliasi yang sulit.

**Who:** Muzakki (mengajukan donasi), petugas UPZIS (menerima di gerai dan mengkonfirmasi transfer daring), administrator (membatalkan transaksi yang salah).

**Current State:** Buku catatan di gerai, pesan WhatsApp untuk konfirmasi daring, dan tabulasi spreadsheet manual di akhir periode pengumpulan.

---

## Solution

### Key Features

1. **Dual-Channel Submission** — Mode daring (muzakki mandiri dengan nominal transfer unik untuk identifikasi rekening bank) dan mode gerai (UPZIS mengajukan atas nama, terkonfirmasi otomatis).
2. **8 Jenis Zakat Per Baris** — Setiap baris muzakki mendukung: fitrah (Rp, kg, lt), maal, profesi, infaq, wakaf, fidyah (Rp, kg), kafarat.
3. **Nomor Transaksi Otomatis** — Penomoran berurutan melalui tabel `sequence_numbers` dengan template format yang dapat dikonfigurasi (placeholder `%year%`, `%seq%`).
4. **Alur Konfirmasi Pembayaran** — Transaksi daring dimulai tanpa konfirmasi; UPZIS/admin mengkonfirmasi setelah memverifikasi transfer bank, mengisi `zakat_pic` dan `payment_date`.
5. **Pembatalan Transaksi** — Hapus lunak khusus admin (`is_active=false`) dengan entri log audit.
6. **Jejak Audit** — `ZakatLog` mencatat setiap perubahan status: submit (1), konfirmasi (2), batal (3), beserta pengguna dan waktunya.
7. **Nominal Transfer Unik** — Transaksi daring mendapat tambahan acak 0-500 Rp pada total, membuat setiap nominal transfer unik untuk pencocokan rekening bank.
8. **Pelacakan Tahun Hijriah** — Setiap transaksi diberi tag tahun Hijriah aktif dari AppConfig.

### User Workflows

1. **Muzakki mengajukan secara daring** — Pilih muzakki keluarga → masukkan nominal per jenis per orang → ajukan → terima nomor transaksi dan nominal transfer (total + nominal unik) → tunggu konfirmasi UPZIS.
2. **UPZIS mengajukan di gerai** — Cari/pilih keluarga → masukkan nominal → ajukan sebagai UPZIS → transaksi terkonfirmasi otomatis dengan petugas sebagai `zakat_pic`.
3. **UPZIS mengkonfirmasi pembayaran daring** — Lihat daftar pembayaran daring → verifikasi transfer bank diterima → klik konfirmasi → `payment_date` dan `zakat_pic` ditetapkan.
4. **Admin membatalkan transaksi** — Lihat transaksi → klik batalkan → transaksi ditandai `is_active=false` → entri log pembatalan dibuat.
5. **Lihat detail transaksi** — Menampilkan kwitansi dengan semua baris, info rekening bank, tampilan QRIS (tergated waktu), linimasa log aktivitas.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Akurasi transaksi | 100% — total sesuai baris | `total_rp` sama dengan jumlah semua nominal baris |
| Waktu konfirmasi daring | < 24 jam | Waktu antara pengajuan dan konfirmasi |
| Transaksi yang dibatalkan | < 2% dari total | Menunjukkan kualitas entri data |

---

## Scope

### In Scope (v1.0)

- [x] Mode pengajuan daring (`is_offline_submission=false`)
  - [x] `receive_from` = pengguna muzakki yang terautentikasi
  - [x] `zakat_pic` = null (menunggu konfirmasi)
  - [x] `unique_number` = acak 0-500 (0 jika hanya beras, tanpa nominal Rp)
  - [x] `total_transfer_rp` = `total_rp` + `unique_number`
- [x] Mode pengajuan gerai (`is_offline_submission=true`)
  - [x] `receive_from` = petugas UPZIS yang terautentikasi
  - [x] `receive_from_name` = nama donatur sebenarnya (teks bebas dari formulir)
  - [x] `zakat_pic` = petugas yang terautentikasi (terkonfirmasi otomatis)
  - [x] `unique_number` = 0
  - [x] `total_transfer_rp` = `total_rp`
- [x] 8 jenis zakat per baris muzakki: fitrah_rp, fitrah_kg, fitrah_lt, maal_rp, profesi_rp, infaq_rp, wakaf_rp, fidyah_rp, fidyah_kg, kafarat_rp
- [x] Nominal fitrah Rp yang dapat dikonfigurasi dari AppConfig
- [x] Nomor transaksi berurutan otomatis
- [x] Tag tahun Hijriah dari AppConfig pada setiap transaksi
- [x] Validasi: setidaknya salah satu dari total_rp, total_kg, atau total_lt harus bukan nol
- [x] Endpoint konfirmasi pembayaran (menetapkan `zakat_pic`, `payment_date`)
- [x] Pembatalan transaksi oleh administrator (`is_active=false`)
- [x] Log audit: ZakatLog dengan aksi submit(1), konfirmasi(2), batal(3)
- [x] Daftar transaksi — admin/upzis melihat semua (dapat dicari, filter tahun); muzakki hanya melihat milik sendiri
- [x] Tampilan detail transaksi dengan kwitansi, rekening bank, QRIS (tampilan tergated waktu), log aktivitas
- [x] Salin-ke-clipboard untuk nominal transfer di detail transaksi
- [x] Autocomplete pencarian keluarga untuk mode gerai
- [x] Paginasi (10 per halaman) pada daftar transaksi

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Integrasi payment gateway langsung | Masjid menggunakan rekening bank yang sudah ada; tidak perlu integrasi API | TBD |
| Pembayaran berulang/terjadwal | Zakat umumnya tahunan; tidak ada pola berulang | TBD |
| Dukungan multi-mata uang | Semua transaksi dalam IDR | Never |
| Konfirmasi sebagian / pembayaran terbagi | Transaksi bersifat atomik — konfirmasi atau tidak | TBD |
| Pengeditan transaksi setelah pengajuan | Batalkan dan buat ulang adalah alur yang dimaksud | Never |
| Pelacakan mustahik (penerima) | Di luar cakupan produk — lihat non-goals | Never |
| Notifikasi SMS/WhatsApp | Tindak lanjut manual melalui laporan pembayaran daring | TBD |

### Future (This Capability)

- Tidak ada yang direncanakan — kapabilitas sudah stabil.

---

## User Stories

### Online Submission

| Story | Priority | Link |
|-------|----------|------|
| Muzakki mengajukan zakat untuk anggota keluarga secara daring | P0 | — |
| Muzakki melihat transaksinya dan nominal transfer | P0 | — |

### Gerai Submission

| Story | Priority | Link |
|-------|----------|------|
| Petugas UPZIS mengajukan zakat atas nama donatur yang datang | P0 | — |
| Petugas UPZIS mencari dan memilih keluarga untuk entri gerai | P0 | — |
| Petugas UPZIS mendaftarkan keluarga baru langsung saat entri gerai | P1 | — |

### Confirmation & Voiding

| Story | Priority | Link |
|-------|----------|------|
| Petugas UPZIS mengkonfirmasi pembayaran daring | P0 | — |
| Admin membatalkan transaksi yang salah | P1 | — |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Integritas data | Transaksi dihapus secara lunak, tidak pernah dihapus permanen | Pelestarian jejak audit |
| Rentang nominal unik | 0-500 Rp | Cukup kecil agar tidak mendistorsi donasi, cukup besar untuk membedakan transfer |
| Konkurensi | Pembuatan nomor transaksi berurutan menggunakan increment level DB | Mencegah nomor duplikat saat pengajuan bersamaan |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Transaction | Pengajuan zakat yang berisi satu atau lebih baris (satu per muzakki) |
| Line Item (ZakatLine) | Kontribusi satu muzakki dalam sebuah transaksi, dengan nominal per jenis zakat |
| Unique Number | Rp acak 0-500 yang ditambahkan pada transaksi daring untuk identifikasi transfer bank |
| Gerai Mode | Petugas UPZIS mengajukan atas nama donatur; terkonfirmasi otomatis |
| Online Mode | Muzakki mengajukan sendiri; memerlukan konfirmasi UPZIS setelah transfer bank |
| Void | Penghapusan lunak transaksi (`is_active=false`) oleh administrator |
| ZakatLog | Entri jejak audit yang mencatat aksi submit, konfirmasi, atau batal beserta pengguna dan waktunya |

---

## Technical Considerations

- Model `SequenceNumber` menangani auto-increment dengan template format; `last_number` di-increment secara atomik
- `total_rp` adalah kolom kalkulasi (jumlah semua nominal Rp dari baris); disimpan secara denormalisasi pada transaksi
- `family_head` dan `receive_from_name` adalah string denormalisasi — perubahan data keluarga tidak memperbarui transaksi secara retroaktif
- Tampilan QRIS dan transfer bank dikendalikan oleh pengaturan rentang tanggal AppConfig (lihat C5)
- ZakatPolicy mengatur semua akses: `create` terbuka untuk pengguna terautentikasi; `delete` khusus admin; `confirmPayment` memerlukan upzis/admin; `submitForOthers` memerlukan upzis/admin

---

## Open Questions

Tidak ada — kapabilitas sudah dikirim dan stabil.

---

## RFCs

- [RFC-001: V1 System Design](../rfc/001-system-design.md) — Arsitektur keseluruhan sistem termasuk ZakatDomain, state transitions, dan dual-channel submission

---

## Changelog

### Version 1.0 — 2026-02-27
- Dokumentasi retroaktif dari kapabilitas yang sudah dikirim

---

## References

- [Product vision](./_index.md)
- [C1: Auth & Roles](./001-auth-roles.md)
- [C2: Family & Muzakki](./002-family-muzakki.md)
- [C5: App Configuration](./005-app-config.md)
