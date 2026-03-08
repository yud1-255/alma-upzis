# Story: Pengajuan Zakat via Formulir Sederhana (Pengguna Baru)

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** muzakki baru yang belum memiliki data keluarga di sistem
**I want** mengajukan zakat hanya dengan mengisi nama, email, telepon, dan nominal zakat
**So that** saya bisa membayar zakat dalam waktu kurang dari 3 menit tanpa harus mendaftarkan keluarga terlebih dahulu

---

## Acceptance Criteria

```gherkin
Scenario: Muzakki baru mengajukan zakat via formulir sederhana — happy path
  GIVEN muzakki sudah login (via social login atau email/password)
    AND muzakki belum memiliki data keluarga di sistem
    AND muzakki berada di dashboard
  WHEN muzakki menekan tombol "Bayar Zakat"
  THEN formulir sederhana ditampilkan
    AND field yang ditampilkan: nama lengkap, email, nomor telepon
    AND bagian daftar anggota keluarga dengan kolom nama dan nominal per jenis zakat

Scenario: Muzakki mengisi data dan mengirim pengajuan
  GIVEN muzakki berada di formulir sederhana
    AND muzakki mengisi nama lengkap, email, dan nomor telepon
    AND muzakki mengisi minimal satu nominal zakat yang bukan nol untuk dirinya sendiri
  WHEN muzakki menekan tombol "Ajukan"
  THEN transaksi berhasil dibuat
    AND muzakki menerima nomor transaksi berurutan
    AND muzakki menerima nominal transfer unik (dengan unique number)
    AND halaman konfirmasi menampilkan nomor transaksi dan nominal transfer
    AND transaksi mendapat tag tahun Hijriah aktif (auto-detect atau override)

Scenario: Semua jenis zakat tersedia di formulir
  GIVEN muzakki berada di formulir sederhana
  WHEN formulir selesai dimuat
  THEN semua jenis zakat tersedia: fitrah_rp, fitrah_kg, fitrah_lt, maal_rp, profesi_rp, infaq_rp, wakaf_rp, fidyah_rp, fidyah_kg, kafarat_rp

Scenario: Validasi — nama lengkap kosong
  GIVEN muzakki berada di formulir sederhana
    AND muzakki mengosongkan field nama lengkap
  WHEN muzakki menekan tombol "Ajukan"
  THEN pesan validasi "Nama lengkap wajib diisi" ditampilkan
    AND pengajuan tidak dikirim

Scenario: Validasi — email tidak valid
  GIVEN muzakki berada di formulir sederhana
    AND muzakki mengisi email dengan format tidak valid (misalnya "ali@")
  WHEN muzakki menekan tombol "Ajukan"
  THEN pesan validasi "Format email tidak valid" ditampilkan
    AND pengajuan tidak dikirim

Scenario: Validasi — nomor telepon kosong
  GIVEN muzakki berada di formulir sederhana
    AND muzakki mengosongkan field nomor telepon
  WHEN muzakki menekan tombol "Ajukan"
  THEN pesan validasi "Nomor telepon wajib diisi" ditampilkan
    AND pengajuan tidak dikirim

Scenario: Validasi — semua nominal nol
  GIVEN muzakki berada di formulir sederhana
    AND muzakki tidak mengisi nominal zakat apapun (semua nol atau kosong)
  WHEN muzakki menekan tombol "Ajukan"
  THEN pesan validasi "Minimal satu nominal zakat harus diisi" ditampilkan
    AND pengajuan tidak dikirim

Scenario: Formulir sederhana berdampingan dengan alur V1
  GIVEN muzakki berada di dashboard
  WHEN halaman selesai dimuat
  THEN tombol "Bayar Zakat" (formulir sederhana) ditampilkan
    AND alur V1 tetap dapat diakses melalui menu yang ada
```

---

## UI/UX Notes

- Tombol "Bayar Zakat" merupakan jalur masuk utama di dashboard — posisi prominent
- Formulir dalam satu halaman (single page), bukan multi-step wizard
- Target: muzakki baru dapat menyelesaikan pengajuan dalam < 3 menit dari login pertama

---

## Technical Notes

- Transaksi yang dihasilkan menggunakan model data identik V1 (tabel `zakats` dan `zakat_lines`)
- Mekanisme unique number dan nomor transaksi berurutan sama dengan V1
- Tag tahun Hijriah menggunakan helper auto-detect/override
- Auto-create Family dan Muzakki ditangani oleh story terpisah ([auto-create-keluarga](./auto-create-keluarga.md))

---

## Out of Scope

- Pengeditan data keluarga dari formulir sederhana (tetap via halaman V1)
- Pengajuan gerai via formulir sederhana (hanya mode daring)
- Pre-fill dari riwayat transaksi terakhir (planned V1.2)

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [tambah-anggota-formulir](./tambah-anggota-formulir.md), [pengajuan-prefill](./pengajuan-prefill.md), [auto-create-keluarga](./auto-create-keluarga.md), [konfirmasi-pembayaran-sederhana](./konfirmasi-pembayaran-sederhana.md)
