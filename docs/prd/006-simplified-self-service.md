# PRD: Simplified Self-Service Zakat

| Version | Status | Phase | Last Updated |
|---------|--------|-------|--------------|
| 1.2 | Draft | V1.1 | 2026-03-06 |

---

## Summary

**Capability ID:** C6 (dari [product vision](./_index.md))

**One-liner:** Menurunkan hambatan masuk bagi muzakki baru dengan menyediakan login sosial, dukungan PWA, formulir pengajuan zakat yang disederhanakan tanpa memerlukan pendaftaran keluarga di awal, serta penentuan tahun Hijriah otomatis untuk mengurangi konfigurasi manual.

**Dependencies:**
- Requires: C1 (Auth & Roles), C3 (Zakat Transactions), C5 (App Config)
- Enables: Tidak ada kapabilitas baru — meningkatkan aksesibilitas C1, C2, dan C3

---

## Problem

**What:** Alur V1 mengharuskan muzakki baru melalui tiga langkah sebelum bisa membayar zakat: (1) registrasi akun dengan email/password, (2) pendaftaran keluarga lengkap dengan data alamat, KK, dsb., dan (3) baru kemudian mengisi formulir zakat. Proses ini terlalu berat untuk pengguna baru yang hanya ingin membayar zakat dengan cepat, terutama melalui ponsel. Di sisi operasional, tahun Hijriah yang menentukan periode transaksi harus diatur manual oleh admin di awal setiap tahun — menimbulkan risiko lupa dan salah tagging.

**Who:** Muzakki baru yang belum terdaftar dalam sistem — khususnya pengguna dengan literasi teknologi rendah yang terbiasa login menggunakan akun Google/Facebook dan tidak mau mengisi banyak formulir.

**Current State:** Muzakki harus registrasi email/password, verifikasi email, mendaftarkan keluarga (dengan data wajib: kepala keluarga, telepon, alamat), menambahkan anggota muzakki, baru kemudian bisa mengajukan zakat. Proses ini memakan waktu lebih dari 5 menit dan menyebabkan banyak pengguna berhenti di tengah jalan sebelum menyelesaikan pengajuan zakat.

---

## Solution

### Key Features

1. **Social Login (Google & Facebook)** — Autentikasi satu klik menggunakan akun Google atau Facebook yang sudah ada, menggantikan kebutuhan registrasi email/password manual. Pengguna yang login via social login tetap mendapat peran muzakki implisit seperti registrasi biasa.
2. **PWA (Progressive Web App)** — Aplikasi web yang dapat diinstal ke layar utama ponsel, memberikan pengalaman seperti aplikasi native tanpa perlu unduh dari app store. Mendukung ikon di layar utama, splash screen, dan tampilan fullscreen.
3. **Formulir Zakat Sederhana** — Jalur masuk baru yang memungkinkan muzakki mengajukan zakat hanya dengan mengisi nama, email, telepon, dan nominal zakat — tanpa wajib mendaftarkan keluarga terlebih dahulu. Sistem secara otomatis membuat record Family dan Muzakki dari data yang dimasukkan.
4. **Auto Hijri Year** — Sistem menghitung tahun Hijriah secara otomatis berdasarkan tanggal saat ini, menggantikan kebutuhan konfigurasi manual oleh admin. Admin tetap dapat meng-override jika diperlukan (misalnya koreksi kalender). Berlaku untuk seluruh sistem — semua transaksi baru, filter, dan laporan.

### User Workflows

1. **Social login** — Klik "Masuk dengan Google/Facebook" di halaman login/registrasi → autentikasi via OAuth provider → masuk ke dashboard. Akun baru otomatis dibuat jika email belum terdaftar.
2. **Instal PWA** — Buka aplikasi di browser ponsel → browser menampilkan prompt "Tambahkan ke layar utama" → klik instal → aplikasi tersedia di layar utama seperti app native.
3. **Pengajuan zakat sederhana (pengguna baru, belum ada data keluarga)** — Login → pilih "Bayar Zakat" (formulir sederhana) → isi nama lengkap, email, telepon → tambah anggota keluarga jika diperlukan (nama saja) → isi nominal per jenis zakat per orang → ajukan → terima nomor transaksi dan nominal transfer unik → tunggu konfirmasi UPZIS.
4. **Pengajuan zakat sederhana (pengguna lama, sudah ada data keluarga)** — Login → pilih "Bayar Zakat" (formulir sederhana) → data nama, email, telepon terisi otomatis dari data keluarga yang sudah ada → ubah/tambah anggota jika perlu → isi nominal → ajukan.
5. **Alur V1 tetap tersedia** — Pengguna yang sudah memiliki data keluarga lengkap dapat tetap menggunakan alur pengajuan V1 yang sudah ada.

---

## Success Criteria

| Metric | Target | Measurement |
|--------|--------|-------------|
| Waktu dari login pertama hingga pengajuan zakat selesai (pengguna baru) | < 3 menit | Pengukuran manual pada sesi uji coba |
| Adopsi social login | > 50% dari registrasi baru | Registrasi via social login / total registrasi baru |
| Adopsi formulir sederhana | > 60% dari transaksi daring | Transaksi via formulir sederhana / total transaksi daring |
| Instalasi PWA | > 20% dari pengguna mobile unik | Jumlah instalasi / jumlah pengguna yang mengakses via mobile |

---

## Scope

### In Scope (This Version)

**Social Login:**
- [ ] Login/registrasi via Google OAuth 2.0
- [ ] Login/registrasi via Facebook Login
- [ ] Akun baru otomatis dibuat saat social login pertama kali (email belum terdaftar)
- [ ] Pengguna yang sudah terdaftar via email/password dapat login via social login jika email cocok (account linking)
- [ ] Peran muzakki implisit tetap berlaku untuk pengguna social login
- [ ] Tombol social login ditampilkan di halaman login dan halaman registrasi

**PWA:**
- [ ] Web app manifest dengan nama, ikon, dan tema warna aplikasi
- [ ] Service worker untuk caching aset statis (CSS, JS, gambar)
- [ ] Prompt instalasi "Tambahkan ke layar utama" pada browser yang mendukung
- [ ] Splash screen saat aplikasi dibuka dari layar utama
- [ ] Tampilan standalone (tanpa address bar browser)

**Auto Hijri Year:**
- [ ] Sistem menghitung tahun Hijriah dari tanggal server saat ini secara otomatis (konversi Gregorian → Hijri)
- [ ] Nilai auto-detect digunakan sebagai default untuk seluruh sistem (transaksi baru, filter daftar, laporan)
- [ ] Admin dapat meng-override via AppConfig (`hijri_year`) — jika nilai manual di-set, nilai manual yang dipakai
- [ ] Jika override dihapus/dikosongkan, sistem kembali ke auto-detect
- [ ] Berlaku system-wide: alur V1, formulir sederhana C6, dan reporting (C4)
- [ ] `hijri_year_beginning` tetap dikonfigurasi manual (untuk rentang dropdown filter tahun)

**Formulir Zakat Sederhana:**
- [ ] Jalur masuk baru di dashboard: tombol/link "Bayar Zakat" yang mengarah ke formulir sederhana
- [ ] Formulir sederhana berdampingan (coexist) dengan alur V1 — keduanya dapat diakses
- [ ] Field wajib formulir: nama lengkap, email, nomor telepon
- [ ] Pengguna dapat menambah anggota keluarga — setiap anggota = satu zakat_line dengan nama + nominal per jenis zakat
- [ ] Semua jenis zakat didukung: fitrah_rp, fitrah_kg, fitrah_lt, maal_rp, profesi_rp, infaq_rp, wakaf_rp, fidyah_rp, fidyah_kg, kafarat_rp
- [ ] Jika pengguna sudah terhubung ke Family, data nama/email/telepon/anggota terisi otomatis (pre-fill)
- [ ] Auto-create Family: sistem membuat record Family baru dari data formulir jika pengguna belum memiliki data keluarga
- [ ] Auto-create Muzakki: sistem membuat record Muzakki untuk setiap anggota keluarga yang dimasukkan di formulir
- [ ] Jika pengguna sudah memiliki Family, anggota baru yang ditambahkan di formulir sederhana otomatis ditambahkan sebagai Muzakki baru ke Family tersebut
- [ ] Transaksi yang dihasilkan menggunakan model data yang sama persis dengan V1 (tabel `zakats` dan `zakat_lines`)
- [ ] Alur konfirmasi pembayaran daring tetap berlaku (UPZIS mengkonfirmasi setelah transfer bank)
- [ ] Mekanisme unique number tetap berlaku untuk pengajuan daring
- [ ] Nomor transaksi berurutan otomatis tetap berlaku
- [ ] Tag tahun Hijriah menggunakan auto-detect (atau override admin jika di-set)
- [ ] Validasi: minimal satu nominal bukan nol pada seluruh baris
- [ ] Halaman detail transaksi / kwitansi sama dengan V1

### Out of Scope

| Item | Rationale | Future? |
|------|-----------|---------|
| Login via Apple ID | Prioritas lebih rendah; mayoritas pengguna target menggunakan Android | V2 |
| Offline form submission (PWA offline) | Service worker hanya untuk caching aset; pengajuan zakat tetap memerlukan koneksi internet | V2 |
| Push notifications via PWA | Belum ada kebutuhan notifikasi otomatis yang tervalidasi | V2 |
| Auto-merge Family jika data mirip dengan keluarga yang sudah ada | Kompleksitas matching tinggi; biarkan data duplikat dikelola manual oleh admin | V2 |
| Pengeditan data keluarga dari formulir sederhana | Formulir sederhana hanya untuk pengajuan; pengeditan data keluarga tetap melalui halaman keluarga V1 | V1.2 |
| Pengajuan gerai via formulir sederhana | Formulir sederhana hanya untuk mode daring (self-service); gerai tetap menggunakan alur V1 | TBD |

### Future (This Capability)

- **Pre-fill dari riwayat** — Formulir mengambil data anggota dan nominal dari transaksi terakhir pengguna. Planned for V1.2.
- **Progressive profiling** — Secara bertahap meminta data tambahan (alamat, KK) setelah pengajuan pertama berhasil, untuk melengkapi data keluarga tanpa menghalangi alur utama. Planned for V1.2.

---

## User Stories

### Social Login

| Story | Priority | Link |
|-------|----------|------|
| Pengguna baru mendaftar dan masuk menggunakan akun Google | P0 | -- |
| Pengguna baru mendaftar dan masuk menggunakan akun Facebook | P0 | -- |
| Pengguna lama (sudah punya akun email) masuk via social login | P1 | -- |

### PWA

| Story | Priority | Link |
|-------|----------|------|
| Pengguna menginstal aplikasi ke layar utama ponsel | P0 | -- |
| Pengguna membuka aplikasi dari layar utama tanpa address bar browser | P0 | -- |

### Auto Hijri Year

| Story | Priority | Link |
|-------|----------|------|
| Transaksi baru otomatis mendapat tag tahun Hijriah dari tanggal saat ini tanpa konfigurasi admin | P0 | -- |
| Admin meng-override tahun Hijriah via AppConfig jika diperlukan | P1 | -- |

### Simplified Form — Basic Flow

| Story | Priority | Link |
|-------|----------|------|
| Muzakki baru mengajukan zakat tanpa pendaftaran keluarga di awal | P0 | -- |
| Muzakki baru menambahkan anggota keluarga di formulir sederhana | P0 | -- |
| Muzakki yang sudah memiliki data keluarga mengajukan via formulir sederhana dengan data terisi otomatis | P0 | -- |
| Muzakki menerima nomor transaksi dan nominal transfer setelah pengajuan | P0 | -- |

### Simplified Form — Data Building

| Story | Priority | Link |
|-------|----------|------|
| Sistem membuat record Family otomatis dari data formulir sederhana | P0 | -- |
| Sistem membuat record Muzakki otomatis untuk setiap anggota di formulir | P0 | -- |
| Anggota baru yang ditambahkan di formulir otomatis tersimpan ke Family yang sudah ada | P1 | -- |

### Simplified Form — Confirmation

| Story | Priority | Link |
|-------|----------|------|
| UPZIS mengkonfirmasi pembayaran dari formulir sederhana (alur sama dengan V1) | P0 | -- |

---

## Non-Functional Requirements

| Category | Requirement | Rationale |
|----------|-------------|-----------|
| Social login response time | < 3 detik dari klik hingga masuk ke dashboard | Ekspektasi pengguna terhadap social login yang instan |
| PWA Lighthouse score | > 80 untuk kategori PWA | Memastikan pengalaman instalasi yang baik |
| Auto-create data consistency | Family dan Muzakki yang dibuat otomatis harus valid dan dapat digunakan di alur V1 | Data yang dibuat dari formulir sederhana harus kompatibel penuh dengan model data yang ada |
| Formulir field minimal | Maksimal 3 field wajib (nama, email, telepon) sebelum mengisi nominal zakat | Menjaga prinsip low-friction |

---

## Domain Concepts

| Term | Definition |
|------|------------|
| Social Login | Autentikasi menggunakan akun pihak ketiga (Google/Facebook) melalui protokol OAuth 2.0 |
| PWA (Progressive Web App) | Teknologi web yang memungkinkan aplikasi web diinstal ke perangkat dan memberikan pengalaman seperti aplikasi native |
| Formulir Sederhana | Jalur masuk pengajuan zakat yang baru, dengan data wajib minimal (nama, email, telepon) tanpa mengharuskan pendaftaran keluarga di awal |
| Auto-create | Mekanisme sistem yang secara otomatis membuat record Family dan Muzakki dari data yang dimasukkan di formulir sederhana |
| Account Linking | Proses menghubungkan akun social login dengan akun email/password yang sudah ada berdasarkan kecocokan alamat email |
| Auto Hijri Year | Penentuan tahun Hijriah secara otomatis dari tanggal Gregorian saat ini, menggantikan konfigurasi manual. Admin override tetap dimungkinkan |

---

## Technical Considerations

- Social login memerlukan integrasi OAuth 2.0 — pertimbangkan Laravel Socialite yang sudah mature di ekosistem Laravel
- Account linking: jika email dari social provider sudah ada di tabel `users`, hubungkan ke akun yang ada tanpa membuat akun baru. Percayai klaim `email_verified` dari provider; jika provider tidak mengembalikan email terverifikasi, fallback ke alur verifikasi email V1.
- PWA memerlukan HTTPS (sudah terpenuhi jika deployment via HTTPS)
- Service worker harus dikonfigurasi agar tidak mengganggu session-based auth (cookie handling)
- Auto-create Family: gunakan data dari formulir sederhana untuk mengisi `head_of_family` (nama), `phone`, dan `email`. Field alamat (`address`, `is_bpi`, `kk_number`) di-set nullable — tidak perlu flag `is_complete` khusus. Family yang auto-created dapat digunakan di alur V1 untuk pengajuan zakat; jika pengguna ingin mengedit data keluarga via halaman V1, mereka perlu melengkapi field alamat yang wajib di halaman tersebut.
- Auto-create Muzakki: buat satu record Muzakki per anggota yang dimasukkan di formulir. Hubungkan ke Family yang baru dibuat atau yang sudah ada.
- Transaksi yang dihasilkan harus mengisi `family_id` dan `zakat_lines` yang valid — model data identik dengan V1, hanya jalur masuknya yang berbeda.
- Formulir sederhana dan alur V1 menghasilkan record di tabel yang sama (`zakats`, `zakat_lines`, `families`, `muzakkis`) — tidak ada tabel baru untuk data transaksi.
- Auto Hijri Year: konversi Gregorian → Hijri menggunakan library `geniusts/hijri-dates` (330K+ downloads, Carbon-based, cocok dengan stack Laravel). Logika: `AppConfig::getConfigValue('hijri_year') ?? HijriDate::currentYear()`. Untuk use case ini hanya perlu tahun, jadi perbedaan algoritmik ±1-2 hari di batas tahun tidak signifikan — admin override sebagai safety net.
- Auto Hijri Year berlaku system-wide — perlu memastikan semua titik yang sebelumnya membaca `AppConfig::getConfigValue('hijri_year')` sekarang melalui satu helper yang menerapkan logika fallback (override → auto-detect).

---

## Resolved Questions

- [x] **Bagaimana menangani data Family yang tidak lengkap?** — **Keputusan:** Set nullable. Field alamat (`address`, `is_bpi`, `kk_number`) boleh kosong pada Family yang auto-created. Tidak perlu flag `is_complete` khusus. Family tetap dapat digunakan untuk pengajuan zakat di alur V1; pengeditan data keluarga via halaman V1 akan meminta pengguna melengkapi field wajib.
- [x] **Apakah email dari social provider yang belum diverifikasi oleh provider harus tetap di-bypass verifikasi email?** — **Keputusan:** Percayai proses verifikasi social provider. Jika provider mengembalikan `email_verified=true`, bypass verifikasi email kita. Jika tidak (edge case: Facebook phone-only account), fallback ke alur verifikasi email V1.
- [x] **Bagaimana perilaku jika pengguna social login mengajukan via formulir sederhana, lalu ingin menggunakan alur V1?** — **Keputusan:** Data Family auto-created dapat langsung dipakai di alur V1 untuk pengajuan zakat (family dan muzakki record sudah ada). Jika pengguna ingin mengedit data keluarga via halaman manajemen V1, mereka perlu melengkapi field alamat yang wajib di halaman tersebut. Ini adalah perilaku alami yang mendorong progressive data completion.
- [x] **Apakah perlu ada batas jumlah anggota keluarga yang bisa ditambahkan di formulir sederhana?** — **Keputusan:** Tidak perlu batas khusus. Perilaku sama dengan V1 — tidak ada batas eksplisit jumlah anggota keluarga.
- [x] **Library konversi Hijri mana yang digunakan?** — **Keputusan:** Gunakan `geniusts/hijri-dates` (330K+ downloads, Carbon-based, cocok dengan stack Laravel). Konversi algoritmik cukup memadai — untuk use case ini hanya perlu tahun Hijriah, bukan tanggal presisi. Perbedaan algoritmik ±1-2 hari di batas tahun tidak signifikan karena admin override tersedia sebagai safety net.
- [x] **Bagaimana mekanisme admin override untuk auto Hijri year?** — **Keputusan:** Toggle on/off di halaman AppConfig admin. Jika toggle on (override aktif), admin memasukkan tahun Hijriah manual. Jika toggle off (default), sistem menggunakan auto-detect. Operasi ini jarang dilakukan (sekali per tahun jika ada koreksi), sehingga toggle sederhana sudah memadai.

## Open Questions

Tidak ada — semua pertanyaan telah dijawab. Siap untuk tahap RFC.

---

## RFCs

| RFC | Title | Status |
|-----|-------|--------|
| TBD | Simplified Self-Service Zakat — System Design | Not started |

---

## Changelog

### Version 1.2 — 2026-03-06
- Resolved: semua 6 open questions dijawab dan didokumentasikan
- Keputusan: Family nullable (tanpa flag is_complete), trust social provider email verification, library `geniusts/hijri-dates`, toggle on/off untuk Hijri override

### Version 1.1 — 2026-03-06
- Ditambahkan: fitur Auto Hijri Year (penentuan tahun Hijriah otomatis, system-wide, dengan admin override)
- Ditambahkan: open questions terkait library konversi Hijri dan mekanisme override

### Version 1.0 — 2026-03-04
- Definisi kapabilitas awal mencakup social login, PWA, dan formulir zakat sederhana

---

## References

- [Product vision](./_index.md)
- [C1: User Authentication & Role Management](./001-auth-roles.md) — Social login memperluas kapabilitas autentikasi
- [C2: Family & Muzakki Registration](./002-family-muzakki.md) — Auto-create menghasilkan data yang kompatibel dengan model C2
- [C3: Zakat Transaction Management](./003-zakat-transactions.md) — Formulir sederhana menghasilkan transaksi dengan model data identik C3
