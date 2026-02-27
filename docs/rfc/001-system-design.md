# RFC-001: V1 System Design — Alma UPZIS

| Status | Author | Capability | Last Updated |
|--------|--------|------------|--------------|
| Done | UPZIS Team | [C1](../prd/001-auth-roles.md), [C2](../prd/002-family-muzakki.md), [C3](../prd/003-zakat-transactions.md), [C4](../prd/004-reporting-export.md), [C5](../prd/005-app-config.md) | 2026-02-27 |

**Status Values:** Draft → Review → Accepted → Implementing → ~~Done~~ | Superseded

> **Catatan:** RFC ini didokumentasikan secara retroaktif dari implementasi V1 yang sudah berjalan di production. Semua keputusan arsitektur di sini mencerminkan sistem sebagaimana yang sudah terbangun dan telah dioperasikan.

---

## 1. Context

**Trigger:** UPZIS Al Munawwarah membutuhkan platform digital untuk menggantikan alur kerja pengumpulan zakat berbasis kertas di Masjid Al Muhajirin, Bukit Pamulang Indah. Sistem harus mendukung dua saluran pengumpulan (daring dan gerai), pendaftaran keluarga/muzakki, pengelolaan transaksi lengkap, dan pelaporan — semuanya siap untuk periode Ramadhan.

**Requirements:** (dari [Product Vision](../prd/_index.md))
- Pengajuan zakat dua saluran: mandiri daring oleh muzakki dan entri gerai oleh petugas UPZIS
- Pendaftaran keluarga dan muzakki dengan dukungan sistem alamat BPI
- Pengelolaan siklus penuh transaksi: pengajuan → konfirmasi → pembatalan
- Pelaporan dan ekspor Excel untuk rekap harian, transaksi, muzakki, dan pembayaran daring
- Konfigurasi runtime untuk parameter yang berubah setiap tahun (tahun Hijriah, nominal fitrah, dll.)
- Kontrol akses berbasis peran: administrator, petugas UPZIS, dan muzakki

**Constraints:**
- **Tim kecil** — Dikembangkan oleh tim kecil, membutuhkan framework yang matang dengan boilerplate minimal
- **Literasi teknologi pengguna rendah** — Antarmuka harus sederhana dan responsif mobile-first
- **Infrastruktur shared hosting** — Harus berjalan di hosting PHP/MySQL standar tanpa container atau layanan cloud khusus
- **Timeline ketat** — Harus siap sebelum Ramadhan; tidak ada waktu untuk belajar stack baru
- **Bahasa Indonesia** — Antarmuka dan alur pengguna dalam Bahasa Indonesia

---

## 2. Decision

### Summary

Alma UPZIS dibangun sebagai aplikasi web monolith menggunakan Laravel 8 (PHP) dengan Inertia.js + Vue 3 untuk antarmuka SPA-like tanpa membangun API terpisah. MySQL digunakan sebagai database relasional. Arsitektur mengikuti pola Domain Layer di atas model Eloquent untuk memisahkan logika bisnis dari controller.

### Architecture

```
┌─────────────────────────────────────────────────────────┐
│                      Browser (Vue 3)                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────┐ │
│  │  Pages/   │ │Components│ │ Layouts/ │ │  Tailwind   │ │
│  │  Zakat/   │ │          │ │          │ │    CSS      │ │
│  │  Family/  │ │          │ │          │ │             │ │
│  │  Auth/    │ │          │ │          │ │             │ │
│  └────┬─────┘ └──────────┘ └──────────┘ └────────────┘ │
│       │  Inertia.js (no REST API needed)                │
└───────┼─────────────────────────────────────────────────┘
        │
┌───────┼─────────────────────────────────────────────────┐
│       ▼         Laravel 8 (PHP)                          │
│  ┌──────────┐     ┌──────────────┐     ┌─────────────┐ │
│  │Controllers│────▶│  Domains     │────▶│   Models    │ │
│  │          │     │  ZakatDomain │     │  (Eloquent) │ │
│  │  Zakat   │     │  Residence   │     │  Zakat      │ │
│  │  Family  │     │  Domain      │     │  Family     │ │
│  │  Muzakki │     │              │     │  Muzakki    │ │
│  │  Auth    │     └──────────────┘     │  User       │ │
│  │  Role    │                          │  AppConfig  │ │
│  │  Config  │     ┌──────────────┐     └──────┬──────┘ │
│  └──────────┘     │  Policies    │            │        │
│                   │  (Authorization)          │        │
│  ┌──────────┐     └──────────────┘            │        │
│  │ Exports  │                                 │        │
│  │(Maatwebsite)   ┌──────────────┐            │        │
│  └──────────┘     │  Middleware   │            │        │
│                   │  (Role check) │            │        │
│                   └──────────────┘            │        │
└───────────────────────────────────────────────┼────────┘
                                                │
                                       ┌────────▼────────┐
                                       │     MySQL       │
                                       │  11 tabel inti  │
                                       │  + 4 Laravel    │
                                       └─────────────────┘
```

### Technology Choices

| Concern | Choice | Rationale |
|---------|--------|-----------|
| Language | PHP 7.3+ / 8.0+ | Ekosistem hosting sangat luas; tim sudah familiar |
| Framework | Laravel 8 | Full-featured, autentikasi bawaan (Breeze), ORM matang, ekosistem paket kuat |
| Frontend | Vue 3 + Inertia.js | SPA-like experience tanpa perlu membangun API REST terpisah; Inertia menjembatani routing Laravel ke Vue |
| CSS | Tailwind CSS 3 | Utility-first, cepat untuk prototyping, responsif bawaan |
| Database | MySQL | Standar hosting PHP; relasional sesuai model data keluarga-muzakki-transaksi |
| Auth | Laravel Breeze (session-based) | Scaffolding lengkap: registrasi, login, verifikasi email, reset password |
| Export | Maatwebsite Excel 3.1 | Paket Laravel paling populer untuk ekspor Excel; integrasi query builder |
| Build tool | Laravel Mix 6 (Webpack) | Bawaan Laravel; kompilasi Tailwind + Vue tanpa konfigurasi tambahan |

---

## 3. Design Details

### 3.1 Domain Layer

**Responsibility:** Memisahkan logika bisnis dari controller agar controller tetap tipis dan logika dapat diuji secara mandiri.

**Interface:**
```php
// ZakatDomain
submitAsMuzakki(User $user, array $data): Zakat
submitAsUpzis(User $user, array $data): Zakat
confirmZakatPayment(Zakat $zakat, User $pic): void
voidTransaction(Zakat $zakat, User $user): void
transactionSummary(string $hijriYear, ?string $search): Builder
zakatTransactionRecap(string $hijriYear, ?string $search): Builder
zakatMuzakkiRecap(string $hijriYear, ?string $search): Builder
zakatOnlinePayments(string $hijriYear, ?string $search): Builder
generateZakatNumber(bool $isOffline): string

// ResidenceDomain
getBlockNumbers(): array
getBlockNumberOptions(): array
searchFamily(string $query): Collection  // max 10 hasil
getFamily(string $kkNumber, User $user): ?Family
updateFamilyRegistration(Family $family, array $data): Family
```

**Behavior:**
- `submitAsMuzakki()` menghasilkan `unique_number` acak (0-500) untuk identifikasi transfer bank
- `submitAsUpzis()` otomatis mengkonfirmasi transaksi (menetapkan `zakat_pic` = petugas)
- `generateZakatNumber()` menggunakan tabel `sequence_numbers` dengan increment atomik level DB
- `getFamily()` menginkrementasi `kk_check_count` pada user dan menolak jika melebihi `check_kk_limit`

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| Semua nominal nol pada transaksi | Validation error | Ditolak sebelum simpan; minimal satu baris harus memiliki nilai > 0 |
| KK check limit terlampaui | Policy denial | `FamilyPolicy::checkKkNumber()` mengembalikan false |
| Sequence number collision | DB-level constraint | Auto-increment atomik mencegah duplikasi |
| User tanpa keluarga mencoba submit | Validation redirect | Diarahkan ke halaman pendaftaran keluarga |

### 3.2 Authorization Layer

**Responsibility:** Mengontrol akses berdasarkan peran pengguna di seluruh aplikasi.

**Interface:**
```php
// Middleware: EnsureUserHasRole
handle(Request $request, Closure $next, string ...$roles): Response

// User model methods
hasRole(string $role): bool
hasAnyRole(array $roles): bool

// Policies
ZakatPolicy::viewAny(User $user): bool         // admin|upzis
ZakatPolicy::view(User $user, Zakat $zakat): bool  // pemilik atau admin|upzis
ZakatPolicy::create(User $user): bool          // semua user terautentikasi
ZakatPolicy::delete(User $user): bool          // admin saja
ZakatPolicy::confirmPayment(User $user): bool  // admin|upzis
ZakatPolicy::submitForOthers(User $user): bool // admin|upzis
```

**Behavior:**
- Middleware `EnsureUserHasRole` melindungi rute grup (admin, upzis)
- Muzakki adalah peran implisit — tidak ada catatan di tabel `roles`, semua user terautentikasi tanpa peran eksplisit dianggap muzakki
- Peran di-eager-load dan dibagikan ke seluruh halaman via `HandleInertiaRequests` middleware
- Penetapan peran oleh admin: lepas semua peran lalu lampirkan satu (satu peran per user)

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| User tanpa peran mengakses rute terproteksi | 403 Forbidden | Middleware mengembalikan abort(403) |
| User mencoba hapus transaksi tanpa peran admin | Policy denial | ZakatPolicy::delete() mengembalikan false |

### 3.3 Inertia.js Bridge

**Responsibility:** Menjembatani routing dan data server-side Laravel ke komponen Vue 3 tanpa perlu REST API.

**Interface:**
```php
// Controller response pattern
return Inertia::render('Zakat/Index', [
    'zakats' => $paginatedData,
    'hijriYears' => $years,
    'filters' => $request->only(['search', 'hijri_year']),
]);
```

**Behavior:**
- Setiap halaman Vue menerima props langsung dari controller Laravel
- Navigasi menggunakan `Inertia::visit()` — full-page refresh tanpa kehilangan state SPA
- Shared data (user, roles, flash messages) dikirim otomatis oleh `HandleInertiaRequests`
- Form submission menggunakan `Inertia.post()` / `Inertia.put()` dengan validasi server-side

### 3.4 Export System

**Responsibility:** Menghasilkan file Excel berformat untuk lima jenis laporan.

**Interface:**
```php
// Semua export class mengimplementasikan:
FromQuery       // Data dari query builder (lazy loading)
WithMapping     // Transformasi baris ke kolom
WithHeadings    // Header kolom (beberapa menggunakan 2 baris)
WithStyles      // Format header bold, border
ShouldAutoSize  // Auto-resize kolom

// Export types
ZakatExport::class              // zakat.xlsx
TransactionRecapExport::class   // transaction_recap.xlsx
MuzakkiListExport::class        // muzakki_list.xlsx
MuzakkiRecapExport::class       // muzakki_recap.xlsx
OnlinePaymentsExport::class     // online_payments.xlsx
```

**Behavior:**
- `TransactionRecapExport` dan `MuzakkiRecapExport` menggunakan header 2 baris (grouping: Fitrah → Rp/Kg/Lt)
- Semua ekspor difilter berdasarkan tahun Hijriah
- Download langsung (synchronous) — tidak menggunakan queue

---

## 4. Data Model

### Entities

```
User
├── id: bigint (PK, auto-increment)
├── name: string
├── email: string (unique)
├── password: string (hashed)
├── email_verified_at: timestamp (nullable)
├── family_id: bigint (FK → families.id, nullable)
├── kk_check_count: int (default 0)
└── remember_token: string (nullable)

Roles: users ←→ roles (many-to-many via role_user)

Role
├── id: bigint (PK)
└── name: string (unique: 'administrator', 'upzis')

Family
├── id: bigint (PK, auto-increment)
├── head_of_family: string
├── phone: string
├── kk_number: string (nullable, tidak unique)
├── address: string
├── is_bpi: boolean
├── bpi_block_no: string (nullable)
└── bpi_house_no: string (nullable)

Muzakki
├── id: bigint (PK, auto-increment)
├── family_id: bigint (FK → families.id)
├── name: string
├── phone: string (nullable)
├── address: string
├── is_bpi: boolean
├── bpi_block_no: string (nullable)
├── bpi_house_no: string (nullable)
├── use_family_address: boolean
└── is_active: boolean (default true)

Zakat
├── id: bigint (PK, auto-increment)
├── transaction_no: string
├── receive_from: bigint (FK → users.id)
├── zakat_pic: bigint (FK → users.id, nullable)
├── transaction_date: date
├── hijri_year: string
├── family_head: string (denormalisasi)
├── receive_from_name: string (untuk mode gerai)
├── is_offline_submission: boolean
├── total_rp: decimal
├── unique_number: int (0-500)
├── total_transfer_rp: decimal
└── payment_date: date (nullable)

ZakatLine
├── id: bigint (PK, auto-increment)
├── zakat_id: bigint (FK → zakats.id)
├── muzakki_id: bigint
├── fitrah_rp: decimal
├── fitrah_kg: decimal
├── fitrah_lt: decimal
├── maal_rp: decimal
├── profesi_rp: decimal
├── infaq_rp: decimal
├── wakaf_rp: decimal
├── fidyah_rp: decimal
├── fidyah_kg: decimal
└── kafarat_rp: decimal

ZakatLog
├── id: bigint (PK, auto-increment)
├── zakat_id: bigint (FK → zakats.id)
├── user_id: bigint (FK → users.id)
└── action: int (1=submit, 2=confirm, 3=void)

AppConfig
├── id: bigint (PK, auto-increment)
├── key: string (tidak unique — disengaja untuk multi-value)
└── value: string

SequenceNumber
├── id: bigint (PK, auto-increment)
└── last_number: int (auto-increment atomik)
```

### State Transitions

```
Transaksi Daring (Online):
  [Created] ──submit──▶ [Pending] ──confirm──▶ [Confirmed]
                                                    │
                                               ──void──▶ [Voided]

Transaksi Gerai (Offline):
  [Created] ──submit──▶ [Confirmed] (otomatis)
                              │
                         ──void──▶ [Voided]
```

Catatan: Status tidak disimpan sebagai kolom eksplisit. Status diturunkan dari:
- `zakat_pic` null → pending (menunggu konfirmasi)
- `zakat_pic` terisi + `payment_date` terisi → confirmed
- ZakatLog dengan `action=3` → voided

---

## 5. API Design

**Catatan:** Alma UPZIS tidak menggunakan REST API tradisional. Semua interaksi menggunakan Inertia.js yang menghubungkan rute Laravel ke komponen Vue secara langsung.

**Web Routes Utama:**

| Method | Path | Purpose | Access |
|--------|------|---------|--------|
| GET | `/` | Halaman publik | Public |
| GET | `/dashboard` | Dashboard setelah login | Auth |
| GET/POST | `/zakat` | Daftar & buat transaksi | Auth |
| GET | `/zakat/{id}` | Detail transaksi | Auth (pemilik/admin/upzis) |
| DELETE | `/zakat/{id}` | Batalkan transaksi | Admin |
| POST | `/zakat/{id}/confirm` | Konfirmasi pembayaran | Admin/UPZIS |
| GET | `/zakat/transaction_recap` | Rekap transaksi | Admin/UPZIS |
| GET | `/zakat/daily_recap` | Rekap harian | Admin/UPZIS |
| GET | `/zakat/muzakki_recap` | Rekap muzakki | Admin/UPZIS |
| GET | `/zakat/daily_muzakki_recap` | Rekap harian muzakki | Admin/UPZIS |
| GET | `/zakat/muzakki_list` | Daftar muzakki | Admin/UPZIS |
| GET | `/zakat/online_payments` | Pembayaran daring | Admin/UPZIS |
| GET | `/zakat/export/{type}/{hijriYear?}` | Ekspor Excel | Admin/UPZIS |
| GET/POST | `/family` | CRUD keluarga | Auth |
| GET | `/family/check_kk` | Pencarian KK | Auth (rate-limited) |
| GET | `/family/search` | Autocomplete keluarga | Admin/UPZIS |
| POST | `/family/register` | Daftarkan keluarga saat entri zakat | Admin/UPZIS |
| POST | `/family/assign/{id}` | Hubungkan keluarga ke user | Auth |
| GET/POST | `/muzakki` | CRUD muzakki | Auth |
| GET | `/roles` | Halaman pengelolaan peran | Admin |
| POST | `/roles/assign` | Tetapkan peran | Admin |
| GET | `/app_config` | Daftar konfigurasi | Admin |
| POST | `/app_config/{id}` | Perbarui konfigurasi | Admin |

---

## 6. Alternatives Considered

### Monolith Laravel + Blade (Tanpa SPA)

- **Approach:** Server-side rendering penuh dengan Blade templates, tanpa JavaScript framework.
- **Pros:** Lebih sederhana, tanpa build step, SEO bawaan, lebih sedikit dependensi.
- **Cons:** Pengalaman pengguna kurang responsif, form submission menyebabkan full page reload, interaksi dinamis (autocomplete, perhitungan live) membutuhkan banyak JavaScript ad-hoc.
- **Rejected because:** Form entri zakat sangat interaktif — menambah/menghapus baris muzakki secara dinamis, perhitungan total real-time, dan autocomplete keluarga. Blade dengan jQuery akan menghasilkan kode JavaScript yang tidak terstruktur dan sulit dipelihara.

### Laravel + Separate SPA (Vue/React) with REST API

- **Approach:** Backend Laravel sebagai API REST murni, frontend Vue/React sebagai SPA terpisah.
- **Pros:** Decoupling penuh, bisa digunakan untuk mobile app di masa depan, standar industri untuk SPA.
- **Cons:** Duplikasi routing (backend + frontend), membutuhkan pengelolaan CORS, auth token, dan state management terpisah. Dua codebase yang harus disinkronkan.
- **Rejected because:** Overhead terlalu besar untuk tim kecil. Tidak ada kebutuhan API eksternal (tidak ada mobile app, tidak ada integrasi pihak ketiga). Inertia.js memberikan pengalaman SPA tanpa biaya pemeliharaan API terpisah.

### Node.js (Express/Nest) + React

- **Approach:** Full-stack JavaScript dengan Node.js backend dan React frontend.
- **Pros:** Satu bahasa untuk seluruh stack, ekosistem npm yang besar.
- **Cons:** Tim tidak familiar dengan Node.js. Hosting Node.js lebih mahal dan kurang tersedia dibanding PHP shared hosting. Tidak ada scaffolding autentikasi setara Laravel Breeze.
- **Rejected because:** Constraint hosting (shared hosting PHP/MySQL) dan pengalaman tim. Waktu belajar stack baru tidak sesuai timeline.

### Google Spreadsheet / Airtable

- **Approach:** Menggunakan spreadsheet kolaboratif atau Airtable sebagai "database" dengan form sederhana.
- **Pros:** Tanpa pengembangan, adopsi cepat, familiar bagi petugas.
- **Cons:** Tidak ada kontrol akses granular, tidak ada jejak audit otomatis, rentan kesalahan input, sulit menegakkan aturan bisnis, tidak scalable, bergantung pada koneksi internet.
- **Rejected because:** Tidak memenuhi kebutuhan dual-channel submission, identifikasi nominal transfer unik, jejak audit per aksi, dan kontrol akses berbasis peran. Masalah yang sama dengan kertas — hanya digital tapi tidak terstruktur.

---

## 7. Risks

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Volume transaksi tinggi saat peak Ramadhan | M | M | Paginasi 10 item, query terindeks, ekspor dari query builder (bukan load semua ke memori) |
| Definisi blok BPI berubah (perumahan baru) | L | L | Data hardcoded di ResidenceDomain — perlu perubahan kode jika ada blok baru |
| Duplikasi keluarga karena tidak ada unique constraint pada KK | M | L | Pencarian KK mendorong pengguna menghubungkan ke keluarga yang sudah ada; mitigasi sosial (komunitas kecil) |
| Shared hosting memory limit saat ekspor Excel besar | L | M | Maatwebsite menggunakan `FromQuery` (chunked) bukan `FromCollection`; volume data satu masjid masih kecil |
| Single point of failure (monolith, single server) | M | H | Backup database rutin; aplikasi stateless (session file-based tapi bisa dipindah ke DB) |

---

## 8. Implementation Phases

> **Catatan:** Semua fase sudah selesai diimplementasikan. Dokumentasi ini bersifat retroaktif.

### Phase 1: Foundation (Auth & Config)
**Exit Criteria:** User dapat mendaftar, login, dan admin dapat mengelola peran serta konfigurasi.

**Scope:**
- Scaffolding Laravel Breeze (registrasi, login, verifikasi email, reset password)
- Model User dengan relasi roles (many-to-many)
- Middleware EnsureUserHasRole
- Halaman pengelolaan peran admin
- Tabel app_config dan halaman pengelolaan

**Capabilities:** C1, C5

### Phase 2: Registration (Family & Muzakki)
**Depends on:** Phase 1
**Exit Criteria:** Keluarga dan muzakki dapat didaftarkan dengan dukungan alamat BPI.

**Scope:**
- Model Family dan Muzakki dengan migrasi
- ResidenceDomain dengan logika blok BPI
- Form pendaftaran keluarga (BPI dan non-BPI)
- CRUD muzakki per keluarga
- Pencarian KK dan family-to-user linking

**Capabilities:** C2

### Phase 3: Transactions (Zakat Core)
**Depends on:** Phase 2
**Exit Criteria:** Transaksi zakat dapat diajukan melalui kedua saluran, dikonfirmasi, dan dibatalkan.

**Scope:**
- Model Zakat, ZakatLine, ZakatLog, SequenceNumber
- ZakatDomain dengan logika dual-channel submission
- Form entri zakat dengan baris dinamis per muzakki
- Alur konfirmasi pembayaran daring
- Pembatalan transaksi dengan audit log

**Capabilities:** C3

### Phase 4: Reporting & Export
**Depends on:** Phase 3
**Exit Criteria:** Semua 6 tampilan laporan dan 5 jenis ekspor Excel berfungsi.

**Scope:**
- 6 halaman laporan Vue (Transaction Recap, Daily Recap, Muzakki Recap, dll.)
- 5 kelas ekspor Maatwebsite Excel
- Pencarian, filter tahun Hijriah, dan paginasi
- Kwitansi siap cetak (salinan tunggal dan rangkap)

**Capabilities:** C4

---

## 9. Operational Concerns

**Logging:**
- Laravel default logging (file-based, `storage/logs/laravel.log`)
- ZakatLog sebagai jejak audit level aplikasi untuk semua perubahan status transaksi
- Log level: info untuk operasi normal, error untuk exception

**Metrics:**
- Jumlah transaksi per tahun Hijriah (tersedia melalui laporan)
- Rasio transaksi daring vs gerai (tersedia melalui ekspor)
- Jumlah pembayaran daring yang menunggu konfirmasi (halaman Online Payments)

**Alerts:**
- Tidak ada sistem alerting otomatis — pemantauan manual oleh petugas melalui halaman Online Payments

**Rollback:**
- Deployment manual (bukan CI/CD) — rollback dengan mengembalikan kode ke versi sebelumnya
- Database backup sebelum migrasi
- Transaksi di-soft-delete (tidak pernah dihapus permanen) — data selalu dapat dipulihkan

---

## 10. Security

- [x] **Autentikasi:** Session-based via Laravel Breeze, CSRF protection bawaan pada semua form
- [x] **Verifikasi email:** Wajib sebelum akses dashboard — mencegah akun palsu
- [x] **Otorisasi:** Policy classes + middleware EnsureUserHasRole pada setiap rute terproteksi
- [x] **Password hashing:** Bcrypt default Laravel — password tidak pernah disimpan plain text
- [x] **Rate limiting:** Pencarian KK dibatasi per user via `kk_check_count`; password reset di-throttle (1 req/60 detik)
- [x] **Jejak audit:** Semua perubahan status transaksi dicatat di ZakatLog dengan user ID dan timestamp
- [x] **Soft delete:** Transaksi yang dibatalkan ditandai void, tidak dihapus — menjaga integritas data
- [x] **Input validation:** Server-side validation di setiap controller/request class — tidak bergantung pada validasi client-side
- [x] **XSS protection:** Vue 3 menggunakan text interpolation (bukan v-html) secara default; Inertia menangani encoding
- [x] **SQL injection:** Eloquent ORM menggunakan prepared statements secara default

---

## 11. Open Questions

Tidak ada — sistem sudah berjalan di produksi dan stabil.

---

## References

- [Product Vision](../prd/_index.md)
- [C1: Auth & Roles PRD](../prd/001-auth-roles.md)
- [C2: Family & Muzakki PRD](../prd/002-family-muzakki.md)
- [C3: Zakat Transactions PRD](../prd/003-zakat-transactions.md)
- [C4: Reporting & Export PRD](../prd/004-reporting-export.md)
- [C5: App Configuration PRD](../prd/005-app-config.md)
- [Laravel 8 Documentation](https://laravel.com/docs/8.x)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Maatwebsite Excel Documentation](https://docs.laravel-excel.com/3.1/)
