# Story: Pengajuan via Formulir Sederhana dengan Data Terisi Otomatis

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** muzakki yang sudah memiliki data keluarga di sistem
**I want** formulir sederhana terisi otomatis dengan data keluarga saya yang sudah ada
**So that** saya tidak perlu mengetik ulang data yang sudah pernah saya masukkan

---

## Acceptance Criteria

```gherkin
Scenario: Data kepala keluarga terisi otomatis
  GIVEN muzakki sudah login
    AND muzakki sudah terhubung ke Family dengan data:
      | head_of_family | Ali Rahman    |
      | email          | ali@mail.com  |
      | phone          | 08123456789   |
  WHEN muzakki membuka formulir sederhana
  THEN field nama lengkap terisi "Ali Rahman"
    AND field email terisi "ali@mail.com"
    AND field nomor telepon terisi "08123456789"

Scenario: Anggota keluarga yang sudah ada ditampilkan
  GIVEN muzakki sudah terhubung ke Family
    AND Family memiliki anggota Muzakki: "Ali Rahman", "Siti", "Ahmad"
  WHEN muzakki membuka formulir sederhana
  THEN baris anggota untuk "Ali Rahman", "Siti", dan "Ahmad" sudah ditampilkan
    AND kolom nominal zakat tersedia untuk setiap anggota

Scenario: Muzakki mengubah data pre-fill
  GIVEN formulir sederhana sudah terisi otomatis
  WHEN muzakki mengubah nomor telepon dari "08123456789" menjadi "08198765432"
    AND muzakki mengajukan formulir
  THEN pengajuan menggunakan nomor telepon yang diubah
    AND data Family di database TIDAK diperbarui (formulir sederhana hanya untuk pengajuan, bukan edit)

Scenario: Muzakki menambah anggota baru di atas data yang sudah ada
  GIVEN formulir sudah terisi otomatis dengan 3 anggota yang sudah ada
  WHEN muzakki menekan "Tambah Anggota"
    AND muzakki mengisi nama "Fatimah"
    AND muzakki mengisi nominal fitrah_rp = 50000 untuk Fatimah
  THEN baris Fatimah ditambahkan di bawah anggota yang sudah ada
    AND Fatimah akan otomatis dibuat sebagai Muzakki baru di Family yang ada (lihat auto-create-keluarga)

Scenario: Family tanpa data lengkap (auto-created sebelumnya)
  GIVEN muzakki sudah terhubung ke Family yang dibuat via formulir sederhana sebelumnya
    AND Family hanya memiliki nama, email, telepon (tanpa alamat)
  WHEN muzakki membuka formulir sederhana
  THEN field nama, email, telepon tetap terisi otomatis
    AND formulir berfungsi normal tanpa error meskipun data Family tidak lengkap

Scenario: Muzakki tidak terhubung ke Family
  GIVEN muzakki sudah login
    AND muzakki belum terhubung ke Family manapun
  WHEN muzakki membuka formulir sederhana
  THEN semua field kosong (tidak ada data pre-fill)
    AND muzakki mengisi formulir seperti pengguna baru
```

---

## UI/UX Notes

- Data pre-fill harus jelas merupakan data yang bisa diedit (bukan read-only)
- Anggota yang sudah ada dari database ditampilkan dengan indikasi visual bahwa mereka sudah tersimpan
- Anggota baru yang ditambahkan dibedakan secara visual dari anggota yang sudah ada

---

## Technical Notes

- Pre-fill berdasarkan relasi User -> Family -> Muzakki
- Perubahan data di formulir TIDAK meng-update record Family/Muzakki yang sudah ada — formulir ini hanya untuk pengajuan
- Pengeditan data keluarga tetap melalui halaman manajemen keluarga V1

---

## Out of Scope

- Pre-fill nominal dari riwayat transaksi terakhir (planned V1.2)
- Progressive profiling (meminta data tambahan seperti alamat, planned V1.2)
- Update data Family/Muzakki dari formulir sederhana

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** [RFC-002: Simplified Self-Service Zakat](../../rfc/002-simplified-self-service.md)
- **Related Stories:** [pengajuan-zakat-sederhana](./pengajuan-zakat-sederhana.md), [tambah-anggota-formulir](./tambah-anggota-formulir.md), [auto-create-keluarga](./auto-create-keluarga.md)
