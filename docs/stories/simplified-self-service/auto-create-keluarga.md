# Story: Pembuatan Otomatis Record Family dan Muzakki dari Formulir Sederhana

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** sistem (atas nama muzakki dan admin UPZIS)
**I want** record Family dan Muzakki dibuat secara otomatis dari data yang dimasukkan di formulir sederhana
**So that** data keluarga tersimpan untuk penggunaan berikutnya tanpa muzakki harus melalui proses pendaftaran keluarga terpisah

---

## Acceptance Criteria

```gherkin
Scenario: Auto-create Family untuk pengguna baru
  GIVEN muzakki mengajukan zakat via formulir sederhana
    AND muzakki belum memiliki data Family di sistem
    AND muzakki mengisi: nama = "Ali Rahman", email = "ali@mail.com", telepon = "08123456789"
  WHEN pengajuan berhasil dikirim
  THEN sistem membuat record Family baru dengan:
    | head_of_family | Ali Rahman    |
    | email          | ali@mail.com  |
    | phone          | 08123456789   |
    | address        | NULL          |
    | is_bpi         | NULL          |
    | kk_number      | NULL          |
  AND Family terhubung ke akun muzakki

Scenario: Auto-create Muzakki untuk kepala keluarga
  GIVEN muzakki mengajukan zakat via formulir sederhana
    AND sistem membuat Family baru
  WHEN pengajuan berhasil dikirim
  THEN sistem membuat record Muzakki untuk kepala keluarga "Ali Rahman"
    AND Muzakki terhubung ke Family yang baru dibuat

Scenario: Auto-create Muzakki untuk setiap anggota tambahan
  GIVEN muzakki mengajukan zakat via formulir sederhana
    AND muzakki menambahkan anggota: "Siti" dan "Ahmad"
  WHEN pengajuan berhasil dikirim
  THEN sistem membuat record Muzakki untuk "Siti"
    AND sistem membuat record Muzakki untuk "Ahmad"
    AND kedua Muzakki terhubung ke Family yang sama

Scenario: Anggota baru ditambahkan ke Family yang sudah ada
  GIVEN muzakki sudah memiliki Family dengan anggota: "Ali Rahman", "Siti"
    AND muzakki membuka formulir sederhana (data pre-fill)
    AND muzakki menambahkan anggota baru "Fatimah"
  WHEN pengajuan berhasil dikirim
  THEN sistem membuat record Muzakki baru "Fatimah"
    AND "Fatimah" terhubung ke Family yang sudah ada
    AND record "Ali Rahman" dan "Siti" yang sudah ada TIDAK diubah

Scenario: Family auto-created kompatibel dengan alur V1
  GIVEN sistem telah membuat Family secara otomatis dari formulir sederhana
  WHEN muzakki mengajukan zakat via alur V1
  THEN Family yang auto-created dapat digunakan di alur V1
    AND daftar Muzakki tersedia untuk dipilih di alur V1

Scenario: Family auto-created — edit via halaman V1 memerlukan data lengkap
  GIVEN sistem telah membuat Family secara otomatis (alamat NULL)
  WHEN muzakki membuka halaman edit keluarga di alur V1
  THEN halaman menampilkan data yang sudah ada (nama, email, telepon)
    AND field alamat yang wajib di halaman V1 harus dilengkapi untuk menyimpan perubahan

Scenario: Tidak membuat Family duplikat
  GIVEN muzakki sudah memiliki Family di sistem
  WHEN muzakki mengajukan zakat via formulir sederhana untuk kedua kalinya
  THEN sistem TIDAK membuat Family baru
    AND transaksi terhubung ke Family yang sudah ada

Scenario: Transaksi terhubung ke data yang benar
  GIVEN muzakki mengajukan zakat via formulir sederhana
    AND sistem auto-create Family dan Muzakki
  WHEN transaksi disimpan
  THEN transaksi memiliki `family_id` yang valid
    AND setiap `zakat_line` memiliki referensi ke Muzakki yang valid
    AND model data identik dengan transaksi yang dibuat via alur V1
```

---

## UI/UX Notes

- Auto-create bersifat transparan bagi muzakki — tidak ada konfirmasi atau notifikasi khusus
- Muzakki tidak perlu tahu bahwa record Family/Muzakki sedang dibuat di background

---

## Technical Notes

- Family auto-created mengisi: `head_of_family`, `phone`, `email`. Field `address`, `is_bpi`, `kk_number` di-set NULL
- Tidak perlu flag `is_complete` — field nullable sudah cukup
- Auto-create terjadi sebagai bagian dari proses submit formulir (satu transaksi database)
- Data disimpan di tabel yang sama (`families`, `muzakkis`) — tidak ada tabel baru
- Transaksi mengisi `family_id` dan `zakat_lines` yang valid (identik dengan V1)

---

## Out of Scope

- Auto-merge Family jika data mirip dengan keluarga yang sudah ada (planned V2)
- Update data Family/Muzakki yang sudah ada dari formulir sederhana
- Validasi duplikat Muzakki berdasarkan nama

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [pengajuan-zakat-sederhana](./pengajuan-zakat-sederhana.md), [tambah-anggota-formulir](./tambah-anggota-formulir.md), [pengajuan-prefill](./pengajuan-prefill.md)
