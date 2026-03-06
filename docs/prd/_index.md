# Alma UPZIS

## Vision

Platform digital pengelolaan Zakat, Infaq, dan Shadaqah (ZIS) untuk UPZIS Al Munawwarah di Masjid Al Muhajirin, Bukit Pamulang Indah (BPI). Alma UPZIS menggantikan alur kerja donasi berbasis kertas dengan aplikasi web yang mendukung pengajuan mandiri secara daring oleh muzakki dan pengumpulan langsung di gerai oleh petugas UPZIS — menghasilkan catatan yang akurat, jejak audit yang transparan, dan pelaporan yang efisien untuk setiap periode pengumpulan zakat Ramadhan.

## Target User

**Primary:**
- **Muzakki (Donatur)** — Warga BPI dan anggota masyarakat yang membayar zakat, infaq, dan donasi lainnya. Tingkat literasi teknologi rendah; membutuhkan alur yang sederhana dan ramah perangkat mobile.
- **Petugas UPZIS (Panitia)** — Anggota panitia masjid yang menerima pembayaran di gerai, mengkonfirmasi transfer daring, dan membuat laporan.

**Secondary:**
- **Administrator** — Mengelola peran pengguna, pengaturan aplikasi, dan memiliki pengawasan operasional penuh termasuk pembatalan transaksi.

## Problem Space

Pengumpulan zakat secara manual di masjid mengandalkan formulir kertas, buku catatan tulisan tangan, dan spreadsheet seadanya. Hal ini menyebabkan kesalahan pencatatan, rekaman yang hilang, kesulitan rekonsiliasi transfer bank, dan pelaporan akhir periode yang melelahkan. Muzakki tidak memiliki visibilitas atas status pembayaran mereka, dan petugas menghabiskan terlalu banyak waktu untuk entri data alih-alih melayani jamaah.

---

## Capability Map

| ID | Capability | PRD | Phase | Status |
|----|------------|-----|-------|--------|
| C1 | User Authentication & Role Management | [001](./001-auth-roles.md) | V1 | Shipped |
| C2 | Family & Muzakki Registration | [002](./002-family-muzakki.md) | V1 | Shipped |
| C3 | Zakat Transaction Management | [003](./003-zakat-transactions.md) | V1 | Shipped |
| C4 | Reporting & Data Export | [004](./004-reporting-export.md) | V1 | Shipped |
| C5 | Application Configuration | [005](./005-app-config.md) | V1 | Shipped |
| C6 | Simplified Self-Service Zakat | [006](./006-simplified-self-service.md) | V1.1 | Draft |

### Capability Dependencies

```
C1 (Auth & Roles)
 ├──▶ C2 (Family & Muzakki) ──▶ C3 (Zakat Transactions) ──▶ C4 (Reporting & Export)
 ├──▶ C5 (App Config) ──────────┘
 └──▶ C6 (Simplified Self-Service) ──▶ C2, C3 (auto-creates data, produces transactions)
```

---

## Product Principles

1. **Kesederhanaan bagi donatur** — Setiap layar memiliki satu tindakan yang jelas. Muzakki seharusnya dapat menyelesaikan pengajuan zakat dalam waktu kurang dari 5 menit tanpa pelatihan.
2. **Akuntabilitas melalui jejak audit** — Setiap perubahan status transaksi (submit, konfirmasi, batal) dicatat beserta pengguna dan waktunya. Tidak ada perubahan yang tersembunyi.
3. **Berbasis kalender Islam** — Tahun Hijriah adalah dimensi waktu utama untuk semua transaksi dan laporan, sesuai dengan cara periode pengumpulan zakat beroperasi secara alami.
4. **Pengumpulan dua saluran** — Alur mandiri daring dan alur gerai fisik adalah warga kelas satu, bukan fitur tambahan.
5. **Ramah perangkat mobile** — Desain responsif memastikan muzakki dapat mengajukan dari ponsel tanpa memerlukan komputer.

---

## Non-Functional Defaults

| Category | Default | Rationale |
|----------|---------|-----------|
| Auth | Wajib untuk semua operasi tulis | Data ZIS adalah informasi keuangan yang sensitif |
| Email verification | Wajib sebelum akses dashboard | Mencegah akun palsu mengotori data |
| Session timeout | Bawaan Laravel (120 menit) | Keseimbangan antara keamanan dan kenyamanan |
| Pagination | 10 item per halaman | Menjaga halaman tetap cepat pada koneksi mobile |
| Data retention | Tidak terbatas | Catatan zakat adalah arsip permanen masjid |

---

## Product Non-Goals

- **Bukan multi-masjid / multi-tenant** — Dibangun untuk satu organisasi UPZIS di satu masjid. Tidak ada isolasi tenant, tidak ada pelaporan lintas masjid.
- **Bukan payment gateway** — Tidak memproses pembayaran secara langsung. Transfer bank dan tunai ditangani di luar sistem; aplikasi hanya mencatat dan mengkonfirmasi.
- **Bukan sistem akuntansi** — Melacak donasi dan menghasilkan laporan, tetapi tidak menangani distribusi dana (mustahik), neraca keuangan, atau buku besar.
- **Bukan aplikasi mobile** — Berbasis web saja, desain responsif. Tidak ada aplikasi native iOS/Android.

---

## Success Metrics (Product-Level)

| Metric | Target | Measurement |
|--------|--------|-------------|
| Transaksi zakat yang terdigitalisasi | 100% dari seluruh transaksi zakat masjid | Semua transaksi gerai dan daring tercatat dalam sistem |
| Waktu pembuatan laporan | < 1 menit per ekspor | Waktu dari klik ekspor hingga menerima file Excel |
| Adopsi pengajuan daring | > 30% dari total transaksi | Rasio transaksi daring vs gerai per tahun Hijriah |

---

## Roadmap Overview

### V1 — Pengumpulan ZIS Digital (Shipped)
**Goal:** Menggantikan sepenuhnya pengumpulan zakat berbasis kertas dengan aplikasi web yang mendukung alur daring dan gerai, pendaftaran keluarga/muzakki, pengelolaan transaksi, dan pelaporan.

Capabilities: C1, C2, C3, C4, C5

### V1.1 — Simplified Self-Service Zakat
**Goal:** Menurunkan hambatan masuk bagi muzakki baru dengan social login, PWA, formulir pengajuan zakat yang disederhanakan, dan penentuan tahun Hijriah otomatis — memungkinkan pengajuan zakat dalam waktu kurang dari 3 menit tanpa pendaftaran keluarga di awal. Data keluarga dan muzakki dibangun secara otomatis dari pengajuan.

Capabilities: C6

### V2 — Future
**Goal:** TBD

---

## Glossary

| Term | Definition |
|------|------------|
| Zakat | Kewajiban mengeluarkan sebagian harta dalam Islam, salah satu dari lima rukun Islam |
| Fitrah | Zakat wajib yang dibayarkan sebelum Idul Fitri, dapat berupa uang (Rp), beras per berat (kg), atau beras per volume (lt) |
| Maal | Zakat atas harta/aset yang melebihi batas nisab |
| Profesi | Zakat atas penghasilan/gaji dari profesi |
| Infaq | Pengeluaran sukarela di jalan Allah |
| Wakaf | Wakaf — donasi aset untuk keperluan amal permanen |
| Fidyah | Tebusan atas puasa yang ditinggalkan, dibayarkan dengan uang (Rp) atau beras (kg) |
| Kafarat | Pembayaran kafarat atas pelanggaran sumpah atau aturan puasa |
| Muzakki | Seseorang yang membayar zakat |
| Mustahik | Seseorang yang berhak menerima zakat (tidak dilacak dalam sistem ini) |
| UPZIS | Unit Pengumpul Zakat, Infaq, dan Shadaqah — unit pengumpul ZIS masjid |
| Hijri | Kalender lunar Islam; dimensi waktu utama untuk periode zakat |
| KK | Kartu Keluarga — nomor kartu keluarga Indonesia, digunakan untuk mencari data keluarga yang sudah ada |
| BPI | Bukit Pamulang Indah — kompleks perumahan tempat masjid berada |
| Gerai | Gerai pengumpulan fisik yang dijaga petugas UPZIS selama Ramadhan |
| Panitia | Anggota panitia / petugas UPZIS |

---

## References

- Prosedur pengumpulan UPZIS Al Munawwarah (internal)
- Masjid Al Muhajirin, Bukit Pamulang Indah, Jakarta Selatan
