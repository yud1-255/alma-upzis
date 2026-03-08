# Story: Konfirmasi Pembayaran dari Formulir Sederhana

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** admin UPZIS
**I want** mengkonfirmasi pembayaran dari pengajuan via formulir sederhana dengan alur yang sama dengan V1
**So that** saya tidak perlu mempelajari alur baru dan semua transaksi dikonfirmasi secara konsisten

---

## Acceptance Criteria

```gherkin
Scenario: Admin mengkonfirmasi pembayaran dari formulir sederhana
  GIVEN muzakki telah mengajukan zakat via formulir sederhana
    AND transaksi berstatus "menunggu pembayaran"
    AND muzakki telah melakukan transfer ke rekening UPZIS
  WHEN admin membuka daftar transaksi
    AND admin memilih transaksi tersebut
    AND admin menekan tombol "Konfirmasi Pembayaran"
  THEN status transaksi berubah menjadi "terkonfirmasi"
    AND transaksi muncul di laporan sebagai pembayaran yang sah

Scenario: Transaksi dari formulir sederhana tampil di daftar transaksi yang sama
  GIVEN ada transaksi dari alur V1 dan dari formulir sederhana
  WHEN admin membuka daftar transaksi
  THEN semua transaksi tampil dalam satu daftar yang sama
    AND tidak ada pemisahan atau tab khusus untuk transaksi formulir sederhana

Scenario: Detail transaksi formulir sederhana menampilkan data lengkap
  GIVEN admin membuka detail transaksi yang dibuat via formulir sederhana
  WHEN halaman detail dimuat
  THEN halaman menampilkan: nomor transaksi, nama muzakki, data keluarga, daftar zakat_lines dengan nama anggota dan nominal
    AND format dan layout sama dengan detail transaksi V1

Scenario: Kwitansi untuk transaksi formulir sederhana
  GIVEN admin telah mengkonfirmasi pembayaran dari formulir sederhana
  WHEN admin atau muzakki membuka/cetak kwitansi
  THEN kwitansi menampilkan data yang sama dengan kwitansi V1
    AND format kwitansi identik

Scenario: Admin menolak/membatalkan transaksi dari formulir sederhana
  GIVEN muzakki telah mengajukan zakat via formulir sederhana
    AND transaksi berstatus "menunggu pembayaran"
  WHEN admin membatalkan transaksi (jika fitur ini ada di V1)
  THEN perilaku pembatalan sama dengan alur V1

Scenario: Unique number diverifikasi saat konfirmasi
  GIVEN muzakki telah mengajukan zakat via formulir sederhana
    AND transaksi memiliki nominal transfer unik (dengan unique number)
  WHEN admin memverifikasi pembayaran masuk
  THEN nominal transfer unik membantu mencocokkan pembayaran dengan transaksi
    AND mekanisme pencocokan sama dengan V1
```

---

## UI/UX Notes

- Tidak ada perubahan UI untuk admin — alur konfirmasi identik dengan V1
- Transaksi dari formulir sederhana dan V1 tidak perlu dibedakan secara visual di daftar admin

---

## Technical Notes

- Transaksi dari formulir sederhana menggunakan tabel dan model data yang sama (`zakats`, `zakat_lines`) — tidak ada logika khusus untuk konfirmasi
- Alur konfirmasi, kwitansi, dan laporan seharusnya tidak memerlukan perubahan kode karena model data identik
- Pastikan `family_id` dan `zakat_lines` yang di-auto-create valid sehingga halaman detail dan kwitansi dapat menampilkan data dengan benar

---

## Out of Scope

- Alur konfirmasi baru khusus untuk formulir sederhana (sengaja menggunakan alur V1)
- Notifikasi otomatis ke muzakki saat pembayaran dikonfirmasi (jika belum ada di V1)
- Pembayaran online/payment gateway

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [pengajuan-zakat-sederhana](./pengajuan-zakat-sederhana.md), [auto-create-keluarga](./auto-create-keluarga.md)
