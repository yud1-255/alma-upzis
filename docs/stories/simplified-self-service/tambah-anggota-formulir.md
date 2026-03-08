# Story: Menambahkan Anggota Keluarga di Formulir Sederhana

| Capability | Priority | Status |
|------------|----------|--------|
| C6 — Simplified Self-Service Zakat | P0 | Not Started |

---

## User Story

**As a** muzakki yang mengisi formulir zakat sederhana
**I want** menambahkan anggota keluarga langsung di formulir pengajuan
**So that** saya bisa membayar zakat untuk seluruh anggota keluarga sekaligus tanpa harus mendaftarkan mereka terlebih dahulu

---

## Acceptance Criteria

```gherkin
Scenario: Menambah satu anggota keluarga
  GIVEN muzakki berada di formulir sederhana
    AND muzakki sudah mengisi data diri (nama, email, telepon)
  WHEN muzakki menekan tombol "Tambah Anggota"
  THEN baris anggota baru ditampilkan dengan field: nama anggota
    AND kolom nominal per jenis zakat tersedia untuk anggota tersebut

Scenario: Menambah beberapa anggota keluarga
  GIVEN muzakki berada di formulir sederhana
    AND muzakki sudah menambahkan 2 anggota keluarga
  WHEN muzakki menekan tombol "Tambah Anggota" lagi
  THEN baris anggota ketiga ditampilkan
    AND tidak ada batas jumlah anggota yang bisa ditambahkan

Scenario: Menghapus anggota yang sudah ditambahkan
  GIVEN muzakki berada di formulir sederhana
    AND muzakki sudah menambahkan 3 anggota keluarga
  WHEN muzakki menekan tombol hapus pada anggota kedua
  THEN baris anggota kedua dihapus dari formulir
    AND anggota pertama dan ketiga tetap ada dengan data utuh

Scenario: Setiap anggota menghasilkan satu zakat_line per jenis zakat
  GIVEN muzakki menambahkan 2 anggota keluarga: "Siti" dan "Ahmad"
    AND muzakki mengisi fitrah_rp = 50000 untuk "Siti"
    AND muzakki mengisi fitrah_rp = 50000 dan maal_rp = 100000 untuk "Ahmad"
  WHEN muzakki mengajukan formulir
  THEN transaksi memiliki 3 zakat_lines:
    | nama  | jenis      | nominal |
    | Siti  | fitrah_rp  | 50000   |
    | Ahmad | fitrah_rp  | 50000   |
    | Ahmad | maal_rp    | 100000  |

Scenario: Validasi — nama anggota kosong
  GIVEN muzakki menambahkan anggota keluarga
    AND field nama anggota dibiarkan kosong
    AND anggota tersebut memiliki nominal zakat yang diisi
  WHEN muzakki menekan tombol "Ajukan"
  THEN pesan validasi "Nama anggota wajib diisi" ditampilkan
    AND pengajuan tidak dikirim

Scenario: Anggota tanpa nominal — baris diabaikan
  GIVEN muzakki menambahkan anggota "Siti"
    AND semua nominal zakat untuk "Siti" kosong atau nol
  WHEN muzakki menekan tombol "Ajukan"
  THEN baris "Siti" diabaikan (tidak menghasilkan zakat_line)
    AND pengajuan tetap valid jika ada minimal satu nominal bukan nol di baris lain
```

---

## UI/UX Notes

- Tombol "Tambah Anggota" berada di bawah daftar anggota
- Setiap baris anggota memiliki tombol hapus (ikon X atau trash)
- Muzakki sendiri (kepala keluarga) selalu ada sebagai baris pertama dan tidak dapat dihapus

---

## Technical Notes

- Setiap anggota yang ditambahkan akan otomatis dibuat sebagai record Muzakki (lihat story [auto-create-keluarga](./auto-create-keluarga.md))
- Tidak ada batas jumlah anggota (sama dengan perilaku V1)
- Data minimal per anggota: nama saja (field lain tidak wajib untuk anggota tambahan)

---

## Out of Scope

- Edit data anggota yang sudah tersimpan (untuk edit, gunakan halaman keluarga V1)
- Pencarian anggota dari database muzakki yang sudah ada
- Import anggota dari file

---

## Links

- **PRD:** [C6 — Simplified Self-Service Zakat](../../prd/006-simplified-self-service.md)
- **RFC:** TBD
- **Related Stories:** [pengajuan-zakat-sederhana](./pengajuan-zakat-sederhana.md), [auto-create-keluarga](./auto-create-keluarga.md)
