# RFC-002: Simplified Self-Service Zakat — System Design

| Status | Author | Capability | Last Updated |
|--------|--------|------------|--------------|
| Draft | UPZIS Team | [C6](../prd/006-simplified-self-service.md) | 2026-03-06 |

**Status Values:** Draft → Review → Accepted → Implementing → Done | Superseded

---

## 1. Context

**Trigger:** Alur V1 mengharuskan muzakki baru melalui tiga langkah berat sebelum bisa membayar zakat: (1) registrasi email/password, (2) pendaftaran keluarga lengkap dengan alamat, dan (3) pengisian formulir zakat. Proses ini memakan waktu lebih dari 5 menit dan menyebabkan banyak pengguna berhenti sebelum menyelesaikan pengajuan. V1.1 bertujuan menurunkan hambatan masuk ini agar muzakki baru dapat mengajukan zakat dalam waktu kurang dari 3 menit.

**Requirements:** (dari [PRD C6](../prd/006-simplified-self-service.md))
- Social login via Google dan Facebook OAuth — autentikasi satu klik tanpa registrasi email/password
- PWA — aplikasi web yang dapat diinstal ke layar utama ponsel dengan pengalaman native-like
- Formulir zakat sederhana — pengajuan zakat hanya dengan nama, email, telepon; auto-create Family dan Muzakki
- Auto Hijri Year — penentuan tahun Hijriah otomatis dari tanggal server, menggantikan konfigurasi manual admin

**Constraints:**
- Harus berjalan di stack yang ada: Laravel 8, Inertia.js + Vue 3, MySQL, shared hosting (dari [ADR-001](../adr/001-laravel-php-framework.md), [ADR-002](../adr/002-mysql-database.md), [ADR-003](../adr/003-inertia-vue-frontend.md))
- Model data transaksi identik dengan V1 — tabel `zakats`, `zakat_lines`, `families`, `muzakkis` yang sama, tidak ada tabel transaksi baru
- Formulir sederhana dan alur V1 harus berdampingan (coexist) — keduanya tetap dapat diakses
- Auto Hijri Year berlaku system-wide — alur V1, formulir sederhana C6, dan reporting (C4)
- HTTPS sudah tersedia (syarat PWA terpenuhi)

---

## 2. Decision

### Summary

Empat fitur C6 diimplementasikan sebagai ekstensi dari arsitektur monolith V1 yang sudah ada, tanpa mengubah model data inti. Social login menggunakan Laravel Socialite dengan account linking berbasis email. PWA diimplementasikan dengan web app manifest dan service worker untuk caching aset statis. Formulir sederhana dibangun sebagai jalur masuk baru dengan **application-layer use case** (`SubmitSimpleZakat`) yang mengorkestrasikan auto-create Family/Muzakki lalu mendelegasikan pembuatan transaksi ke `ZakatDomain` yang sudah ada — tidak ada domain baru. Auto Hijri Year menggunakan library `geniusts/hijri-dates` dengan helper terpusat yang menerapkan logika fallback (admin override lalu auto-detect).

### Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                        Browser (Vue 3)                              │
│                                                                     │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────┐  ┌──────────┐ │
│  │  Pages/       │  │  Pages/       │  │ Components │  │ Service  │ │
│  │  Auth/        │  │  SimpleZakat/ │  │            │  │ Worker   │ │
│  │  SocialLogin  │  │  Create.vue  │  │ ZakatForm  │  │ (sw.js)  │ │
│  │              │  │  Show.vue    │  │ MemberRow  │  │          │ │
│  └──────┬───────┘  └──────┬───────┘  └────────────┘  └──────────┘ │
│         │                 │                                         │
│         │  Inertia.js (no REST API needed)                         │
└─────────┼─────────────────┼─────────────────────────────────────────┘
          │                 │
┌─────────┼─────────────────┼─────────────────────────────────────────┐
│         ▼                 ▼          Laravel 8 (PHP)                │
│                                                                     │
│  ┌──────────────────┐  ┌─────────────────────────────────────────┐ │
│  │ SocialLogin      │  │ SimpleZakatController                   │ │
│  │ Controller       │  │   create() → form view                 │ │
│  │   redirect()     │  │   store()  → validate + use case call  │ │
│  │   callback()     │  └──────────────────┬──────────────────────┘ │
│  └────────┬─────────┘                     │                        │
│           │                               │                        │
│  ┌────────▼─────────┐  ┌─────────────────▼──────────────────────┐ │
│  │ Laravel Socialite │  │ SubmitSimpleZakat (Use Case)           │ │
│  │                   │  │   execute(user, data): Zakat           │ │
│  │ Google Provider   │  │     ├── findOrCreateFamily()           │ │
│  │ Facebook Provider │  │     ├── syncMuzakkis()                 │ │
│  └───────────────────┘  │     └── ZakatDomain::submit...()  ◄───┤ │
│                         └────────────────────────────────────────┘ │
│  ┌───────────────────┐                                             │
│  │ HijriYearHelper   │  ┌─────────────────────────────────────┐   │
│  │   current(): str  │  │ Domains (Business Logic)            │   │
│  │   (override ??    │  │   ZakatDomain  ← TIDAK BERUBAH      │   │
│  │    auto-detect)   │  │   ResidenceDomain ← TIDAK BERUBAH   │   │
│  └───────────────────┘  └─────────────────┬───────────────────┘   │
│                                            │                       │
│  ┌───────────────────┐  ┌─────────────────▼───────────────────┐   │
│  │ manifest.json     │  │ Models (Eloquent)                   │   │
│  │ sw.js             │  │   User (+ social_id, social_type)   │   │
│  └───────────────────┘  │   Family (address nullable)         │   │
│                         │   Muzakki, Zakat, ZakatLine         │   │
│                         │   AppConfig                         │   │
│                         └──────────────────┬──────────────────┘   │
└─────────────────────────────────────────────┼─────────────────────┘
                                              │
                                     ┌────────▼────────┐
                                     │     MySQL       │
                                     │  Tabel existing │
                                     │  + 2 kolom baru │
                                     │  di users       │
                                     └─────────────────┘
```

### Technology Choices

| Concern | Choice | Rationale |
|---------|--------|-----------|
| Social Login | Laravel Socialite | Paket resmi Laravel untuk OAuth; mendukung Google & Facebook out-of-the-box; integrasi natural dengan session-based auth Laravel Breeze |
| Hijri Conversion | `geniusts/hijri-dates` | 330K+ downloads, Carbon-based, cocok stack Laravel; konversi algoritmik cukup untuk kebutuhan tahun (bukan tanggal presisi) |
| PWA Manifest | Manual (`manifest.json` + `sw.js`) | Tidak perlu paket tambahan; PWA setup sederhana (manifest + service worker untuk asset caching) |
| Orchestration | Use Case pattern (`app/UseCases/`) | Menjaga bounded context tetap utuh; domain logic zakat tetap di `ZakatDomain`; use case hanya mengorkestrasikan persiapan data |
| Frontend | Vue 3 + Inertia.js (existing) | Konsisten dengan V1; halaman baru ditambahkan sebagai Vue pages biasa |
| Database | MySQL (existing) | Tidak ada tabel baru untuk transaksi; hanya migration untuk kolom social login di `users` dan nullable fields di `families` |

---

## 3. Design Details

### 3.1 Social Login (Laravel Socialite Integration)

**Responsibility:** Menyediakan autentikasi satu klik via Google dan Facebook OAuth 2.0, termasuk pembuatan akun baru dan account linking untuk akun yang sudah ada.

**Interface:**
```php
// SocialLoginController
redirectToProvider(string $provider): RedirectResponse
    // Redirect ke OAuth provider (Google/Facebook)

handleProviderCallback(string $provider): RedirectResponse
    // Proses callback OAuth, buat/link akun, login user

// Internal helper methods (private)
findOrCreateUser(SocialiteUser $socialUser, string $provider): User
    // 1. Cari user berdasarkan email
    // 2. Jika ada → link social account (update social_id, social_type)
    // 3. Jika tidak → buat user baru dengan social credentials
```

**Alur:**
```
User klik "Masuk dengan Google"
  │
  ▼
GET /auth/{provider}/redirect
  │
  ▼
Redirect ke Google OAuth consent screen
  │
  ▼
Google callback → GET /auth/{provider}/callback
  │
  ▼
Socialite mendapat: name, email, avatar, email_verified
  │
  ├── Email sudah ada di tabel users?
  │     ├── Ya → Update social_id, social_type → Login
  │     └── Tidak → Buat user baru → Set email_verified_at → Login
  │
  ▼
Redirect ke /dashboard
```

**Behaviour:**
- Provider yang didukung: `google`, `facebook` — divalidasi via route parameter constraint
- Jika social provider mengembalikan `email_verified=true`, set `email_verified_at = now()` pada user baru (bypass verifikasi email V1)
- Jika provider tidak mengembalikan email terverifikasi (edge case: Facebook phone-only), user tetap dibuat tetapi harus melalui alur verifikasi email V1
- Account linking: jika email dari social provider sudah ada di `users`, update `social_id` dan `social_type` pada akun tersebut — tidak membuat akun baru
- User yang dibuat via social login mendapat peran muzakki implisit (sama dengan registrasi biasa — tanpa record di tabel `roles`)
- Password diisi dengan string acak yang di-hash (user tidak bisa login via password, tapi field DB tetap terisi)

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| User menolak consent di OAuth provider | OAuth error | Redirect ke halaman login dengan flash message error |
| Provider mengembalikan email kosong | Missing email | Redirect ke halaman login dengan pesan "Akun [provider] tidak memiliki email" |
| Network timeout ke OAuth provider | Connection error | Redirect ke halaman login dengan pesan error umum |
| Provider token invalid/expired | Auth error | Redirect ke halaman login, minta user coba lagi |

### 3.2 PWA (Progressive Web App)

**Responsibility:** Membuat aplikasi web dapat diinstal ke layar utama ponsel dengan pengalaman standalone (tanpa address bar browser).

**Interface:**
```
public/manifest.json     — Web app manifest (nama, ikon, tema, display mode)
public/sw.js              — Service worker (caching aset statis)
resources/views/app.blade.php — <link rel="manifest"> + service worker registration
```

**Konfigurasi Manifest:**
```json
{
  "name": "Alma UPZIS",
  "short_name": "Alma",
  "start_url": "/dashboard",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#1a5d1a",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
```

**Service Worker Strategy:**
- Cache-first untuk aset statis: CSS, JS (compiled), gambar, ikon
- Network-first untuk halaman HTML dan request Inertia (agar tidak mengganggu session auth)
- Cache name menggunakan versioning (`alma-cache-v1`) untuk invalidasi saat deploy
- Service worker TIDAK meng-intercept cookie/session — autentikasi session-based Laravel tidak terganggu

**Behaviour:**
- Browser yang mendukung PWA (Chrome Android, Safari iOS) menampilkan prompt "Tambahkan ke layar utama" secara otomatis (setelah memenuhi kriteria engagement)
- Splash screen ditampilkan saat membuka dari layar utama (dari manifest: `background_color` + `icons`)
- Tampilan standalone — tanpa address bar browser
- Offline: menampilkan halaman fallback sederhana ("Tidak ada koneksi internet") — pengajuan zakat tetap memerlukan koneksi

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| Browser tidak mendukung service worker | No SW | Aplikasi tetap berjalan normal sebagai web biasa; fitur install tidak tersedia |
| Cache storage penuh | QuotaExceeded | Service worker menghapus cache lama dan mencoba ulang |

### 3.3 Formulir Zakat Sederhana (SubmitSimpleZakat Use Case)

**Responsibility:** Mengorkestrasikan pengajuan zakat via formulir sederhana — auto-create Family dan Muzakki jika diperlukan, lalu mendelegasikan pembuatan transaksi zakat ke `ZakatDomain` yang sudah ada. Ini adalah **application-layer use case**, bukan domain baru — logika bisnis zakat tetap terpusat di `ZakatDomain`.

**Rationale:** Formulir sederhana bukan domain bisnis baru; domain "zakat" tetap sama (aturan transaksi, penomoran, unique number, konfirmasi). Yang berbeda hanyalah jalur masuk (presentation) dan orkestrasi persiapan data (application). Memisahkannya sebagai domain baru akan memecah bounded context dan menduplikasi logika bisnis. Use case pattern menjaga `ZakatDomain` sebagai single source of truth untuk logika transaksi.

**Interface:**
```php
// app/UseCases/SubmitSimpleZakat.php
class SubmitSimpleZakat
{
    public function __construct(
        private ZakatDomain $zakatDomain
    ) {}

    /**
     * Orchestrator utama:
     * 1. findOrCreateFamily() — persiapan data (bukan domain logic)
     * 2. syncMuzakkis() — persiapan data (bukan domain logic)
     * 3. $this->zakatDomain->submitAsMuzakki() — delegasi ke domain
     */
    public function execute(User $user, array $data): Zakat

    // Internal methods (private)
    private function findOrCreateFamily(User $user, array $contactData): Family
        // Jika user->family_id ada → return family yang ada
        // Jika tidak → buat Family baru, link ke user

    private function syncMuzakkis(Family $family, array $members): Collection
        // Untuk setiap anggota di form:
        //   - Jika muzakki_id ada (existing) → gunakan yang ada
        //   - Jika baru → buat Muzakki baru, link ke family
}
```

**Batas tanggung jawab:**
| Concern | Siapa yang menangani |
|---------|---------------------|
| Auto-create Family | `SubmitSimpleZakat` (use case) |
| Auto-create Muzakki | `SubmitSimpleZakat` (use case) |
| Generate zakat number | `ZakatDomain` (existing) |
| Generate unique number | `ZakatDomain` (existing) |
| Create Zakat + ZakatLine records | `ZakatDomain` (existing) |
| Hijri year tagging | `ZakatDomain` via `HijriYearHelper` (existing) |
| Audit logging | `ZakatDomain` (existing) |

**Input Data Structure (dari Vue form):**
```php
$data = [
    'head_of_family' => 'Nama Lengkap',        // string, required
    'email'          => 'email@example.com',     // string, required, email
    'phone'          => '081234567890',           // string, required
    'members'        => [
        [
            'muzakki_id' => null,                // null = anggota baru
            'name'       => 'Nama Anggota 1',    // string, required
            'zakat'      => [
                'fitrah_rp'   => 50000,
                'fitrah_kg'   => 0,
                'fitrah_lt'   => 0,
                'maal_rp'     => 0,
                'profesi_rp'  => 0,
                'infaq_rp'    => 0,
                'wakaf_rp'    => 0,
                'fidyah_rp'   => 0,
                'fidyah_kg'   => 0,
                'kafarat_rp'  => 0,
            ]
        ],
        [
            'muzakki_id' => 5,                   // integer = muzakki existing
            'name'       => 'Nama Anggota 2',
            'zakat'      => [ /* ... */ ]
        ]
    ]
];
```

**Alur Auto-Create Family:**
```
User belum punya family_id?
  │
  ├── Ya (pengguna baru):
  │     1. Buat Family baru:
  │        - head_of_family = $data['head_of_family']
  │        - phone = $data['phone']
  │        - email = $data['email']           ← kolom baru di families
  │        - address = null                   ← nullable
  │        - kk_number = null                 ← nullable
  │        - is_bpi = false                   ← default
  │     2. Set user->family_id = family.id
  │     3. Return family
  │
  └── Tidak (pengguna lama):
        1. Load family yang ada
        2. Update phone/email jika berubah di form
        3. Return family
```

**Alur Auto-Create Muzakki:**
```
Untuk setiap member di $data['members']:
  │
  ├── muzakki_id = null (anggota baru):
  │     Buat Muzakki baru:
  │       - family_id = family.id
  │       - name = member['name']
  │       - phone = null
  │       - address = null                    ← nullable
  │       - is_bpi = false
  │       - use_family_address = true
  │       - is_active = true
  │
  └── muzakki_id = integer (existing):
        Verify muzakki belongs to family → gunakan yang ada
```

**Behaviour:**
- Formulir sederhana menghasilkan record di tabel yang SAMA dengan V1 (`zakats`, `zakat_lines`) — karena menggunakan `ZakatDomain` yang sama
- Use case `SubmitSimpleZakat` hanya bertanggung jawab atas persiapan data (Family, Muzakki), lalu memanggil `ZakatDomain::submitAsMuzakki()` untuk logika transaksi yang sesungguhnya
- `generateZakatNumber()`, `unique_number` (0-500), audit logging — semuanya ditangani oleh `ZakatDomain`, bukan diduplikasi
- Tahun Hijriah diambil dari `HijriYearHelper::current()` di dalam `ZakatDomain` (auto-detect dengan admin override)
- `is_offline_submission = false` (selalu daring)
- `receive_from = $user->id`
- `family_head = $family->head_of_family`
- Konfirmasi pembayaran mengikuti alur V1 yang ada — UPZIS meng-confirm setelah transfer bank
- Jika user sudah punya Family, data form (nama, email, telepon) di-pre-fill dari data Family existing
- Anggota baru yang ditambahkan di formulir sederhana otomatis ditambahkan sebagai Muzakki ke Family existing
- Validasi: minimal satu nominal bukan nol pada seluruh baris zakat_lines

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| Semua nominal nol pada seluruh baris | Validation error | Ditolak dengan pesan "Minimal satu jenis zakat harus diisi" |
| Email format invalid | Validation error | Laravel validation rule `email` |
| muzakki_id tidak milik family user | Authorization error | Tolak request di use case; muzakki harus milik family yang sama |
| User sudah punya transaksi pending (optional) | Tidak diblokir | Diperbolehkan — sama dengan V1 |
| DB transaction gagal di tengah jalan | Transaction rollback | `DB::transaction()` di use case membungkus auto-create + delegasi ke `ZakatDomain` — gagal di titik mana pun akan rollback semua |

### 3.4 Auto Hijri Year (HijriYearHelper)

**Responsibility:** Menyediakan satu titik akses terpusat untuk mendapatkan tahun Hijriah yang berlaku system-wide, dengan logika fallback: admin override lalu auto-detect.

**Interface:**
```php
// app/Helpers/HijriYearHelper.php
class HijriYearHelper
{
    /**
     * Mendapatkan tahun Hijriah yang berlaku saat ini.
     * Logika: admin override (AppConfig) ?? auto-detect (library).
     */
    public static function current(): string

    /**
     * Mendapatkan tahun Hijriah dari auto-detect (tanpa override).
     * Untuk keperluan informasi/debug.
     */
    public static function autoDetect(): string
}
```

**Logika:**
```php
public static function current(): string
{
    $override = AppConfig::getConfigValue('hijri_year');

    if ($override !== null && $override !== '') {
        return $override;
    }

    return self::autoDetect();
}

public static function autoDetect(): string
{
    return (string) HijriDate::now()->year;
    // HijriDate dari package geniusts/hijri-dates
}
```

**Behaviour:**
- Berlaku system-wide: semua titik yang sebelumnya membaca `AppConfig::getConfigValue('hijri_year')` langsung harus diganti menjadi `HijriYearHelper::current()`
- Titik-titik yang terpengaruh:
  - `ZakatDomain::submitAsMuzakki()` — tag `hijri_year` pada transaksi baru (digunakan oleh V1 maupun C6 via `SubmitSimpleZakat` use case)
  - `ZakatDomain::submitAsUpzis()` — tag `hijri_year` pada transaksi baru
  - Controller yang menyediakan filter tahun Hijriah default di halaman laporan
  - Halaman AppConfig admin — menampilkan info auto-detect di samping field override
- `hijri_year_beginning` tetap dikonfigurasi manual via AppConfig (untuk rentang dropdown filter tahun) — tidak terpengaruh auto-detect
- Admin override: toggle on/off di halaman AppConfig. Jika override aktif, admin memasukkan tahun Hijriah manual. Jika off (default), auto-detect berlaku
- Perbedaan algoritmik +/-1-2 hari di batas tahun Hijriah tidak signifikan — admin override sebagai safety net

**Error Cases:**
| Condition | Error | Handling |
|-----------|-------|----------|
| Library `geniusts/hijri-dates` gagal konversi | Runtime exception | Fallback ke tahun Hijriah terakhir yang tersimpan di AppConfig, log error |
| Override berisi value non-numerik | Validation error | Validasi di halaman AppConfig — hanya menerima angka 4 digit |

---

## 4. Data Model

### Perubahan pada Entities Existing

```
User (MODIFIED — tambah 2 kolom)
├── ... (semua kolom V1 tetap)
├── social_id: string (nullable)          ← BARU: ID dari OAuth provider
└── social_type: string (nullable)        ← BARU: 'google' | 'facebook' | null

Family (MODIFIED — nullable fields)
├── ... (semua kolom V1 tetap)
├── email: string (nullable)              ← BARU: email kepala keluarga
├── address: string (nullable)            ← DIUBAH: sebelumnya required, sekarang nullable
├── kk_number: string (nullable)          ← TETAP nullable (sudah nullable di V1)
├── is_bpi: boolean (default false)       ← TETAP: default false
├── bpi_block_no: string (nullable)       ← TETAP nullable
└── bpi_house_no: string (nullable)       ← TETAP nullable
```

### Entities yang TIDAK Berubah

```
Muzakki   — Tidak ada perubahan schema; auto-created records mengisi field existing
            (address nullable → diisi null; use_family_address → true)

Zakat     — Tidak ada perubahan schema; transaksi dari formulir sederhana mengisi
            semua field yang sama persis dengan V1

ZakatLine — Tidak ada perubahan schema

ZakatLog  — Tidak ada perubahan schema

AppConfig — Tidak ada perubahan schema; key 'hijri_year' sudah ada di V1
```

### Migrasi Database

```php
// Migration 1: Add social login columns to users
Schema::table('users', function (Blueprint $table) {
    $table->string('social_id')->nullable()->after('remember_token');
    $table->string('social_type')->nullable()->after('social_id');

    $table->index(['social_id', 'social_type']);
});

// Migration 2: Add email to families, make address nullable
Schema::table('families', function (Blueprint $table) {
    $table->string('email')->nullable()->after('phone');
    $table->string('address')->nullable()->change();  // sebelumnya required
});

// Migration 3: Make muzakki address nullable (jika belum)
Schema::table('muzakkis', function (Blueprint $table) {
    $table->string('address')->nullable()->change();
});
```

### State Transitions

Tidak ada perubahan state transitions — transaksi dari formulir sederhana mengikuti alur yang sama persis:

```
Transaksi Daring (formulir sederhana maupun V1):
  [Created] ──submit──▶ [Pending] ──confirm──▶ [Confirmed]
                                                    │
                                               ──void──▶ [Voided]
```

---

## 5. API Design

**Catatan:** Konsisten dengan V1, semua interaksi menggunakan Inertia.js — bukan REST API. Route baru ditambahkan di `routes/web.php`.

**Route Baru:**

| Method | Path | Purpose | Access |
|--------|------|---------|--------|
| GET | `/auth/{provider}/redirect` | Redirect ke OAuth provider | Guest |
| GET | `/auth/{provider}/callback` | Handle OAuth callback | Guest |
| GET | `/simple-zakat/create` | Form pengajuan zakat sederhana | Auth |
| POST | `/simple-zakat` | Submit pengajuan zakat sederhana | Auth |
| GET | `/simple-zakat/{id}` | Detail transaksi (reuse view V1) | Auth (pemilik/admin/upzis) |

**Route Constraint:**
```php
Route::get('/auth/{provider}/redirect', ...)->where('provider', 'google|facebook');
Route::get('/auth/{provider}/callback', ...)->where('provider', 'google|facebook');
```

**Route V1 yang Terpengaruh (Auto Hijri Year):**

Route-route berikut tidak berubah path-nya, tetapi controller-nya diupdate untuk menggunakan `HijriYearHelper::current()` sebagai default filter:
- `GET /zakat` — default filter tahun Hijriah
- `GET /zakat/transaction_recap` — default filter
- `GET /zakat/daily_recap` — default filter
- `GET /zakat/muzakki_recap` — default filter
- `GET /zakat/daily_muzakki_recap` — default filter
- `GET /zakat/muzakki_list` — default filter
- `GET /zakat/online_payments` — default filter

---

## 6. Alternatives Considered

### 6.1 Social Login: Laravel Socialite vs Manual OAuth Implementation

**Alternatif A: Manual OAuth 2.0 Implementation**
- **Approach:** Implementasi OAuth 2.0 flow secara manual — HTTP request ke Google/Facebook API, parsing token, handling state parameter.
- **Pros:** Tidak ada dependensi paket tambahan; kontrol penuh atas flow.
- **Cons:** Banyak boilerplate code; harus menangani security concerns (CSRF state, token validation) sendiri; harus maintenance jika provider mengubah API.
- **Ditolak karena:** Reinventing the wheel. Laravel Socialite sudah menangani semua edge cases OAuth (state parameter, token refresh, provider-specific quirks) dan digunakan oleh ratusan ribu proyek Laravel.

**Alternatif B: Laravel Socialite (DIPILIH)**
- **Approach:** Paket resmi Laravel untuk OAuth social login, dengan driver bawaan Google dan Facebook.
- **Pros:** Battle-tested, dokumentasi lengkap, integrasi natural dengan Laravel auth, satu baris kode untuk redirect dan callback.
- **Cons:** Dependensi paket tambahan; terbatas pada provider yang didukung (namun Google dan Facebook cukup).

### 6.2 Social Login: Kolom Social di Tabel Users vs Tabel Social Accounts Terpisah

**Alternatif A: Tabel `social_accounts` terpisah (one-to-many)**
- **Approach:** Tabel baru `social_accounts` dengan FK ke `users`, mendukung multiple social accounts per user.
- **Pros:** Scalable jika menambah provider baru (Apple, Twitter); satu user bisa link banyak social accounts.
- **Cons:** JOIN tambahan untuk setiap login; kompleksitas lebih tinggi; account linking logic lebih rumit.
- **Ditolak karena:** Over-engineering untuk kebutuhan saat ini. PRD hanya mensyaratkan Google dan Facebook. Jika Apple ID ditambahkan di V2, migrasi dari 2 kolom ke tabel terpisah tidak sulit (jumlah user satu masjid kecil).

**Alternatif B: Kolom `social_id` dan `social_type` di tabel `users` (DIPILIH)**
- **Approach:** Tambah 2 kolom di tabel `users` untuk menyimpan provider terakhir yang digunakan.
- **Pros:** Sederhana; tidak ada JOIN tambahan; cukup untuk 2 provider.
- **Cons:** Hanya menyimpan satu social account per user; jika user punya Google DAN Facebook, hanya yang terakhir digunakan yang tersimpan.
- **Trade-off yang diterima:** Untuk use case ini, kita hanya perlu tahu apakah user pernah login via social — bukan menyimpan semua social accounts. Account linking berbasis email, bukan social_id.

### 6.3 PWA: Package vs Manual Implementation

**Alternatif A: Laravel PWA Package (e.g., `silviolleite/laravel-pwa`)**
- **Approach:** Package Laravel yang generate manifest.json, service worker, dan offline page secara otomatis.
- **Pros:** Setup lebih cepat; ada Artisan commands untuk generate ikon.
- **Cons:** Dependensi tambahan; konfigurasi yang dihasilkan terlalu generic; service worker yang di-generate mungkin mengganggu session auth Laravel.
- **Ditolak karena:** Setup PWA sangat sederhana (2 file: manifest + service worker). Kontrol manual memastikan service worker tidak mengganggu cookie/session Laravel. Package menambah kompleksitas tanpa manfaat signifikan.

**Alternatif B: Manual manifest.json + sw.js (DIPILIH)**
- **Approach:** Tulis manual `manifest.json` dan `sw.js` di `public/`, register di `app.blade.php`.
- **Pros:** Kontrol penuh atas caching strategy; tidak ada dependensi tambahan; mudah di-debug.
- **Cons:** Harus menulis service worker code sendiri.

### 6.4 Formulir Sederhana: Arsitektur Orchestration

**Alternatif A: Extend `ZakatDomain` dengan method `submitSimpleZakat()`**
- **Approach:** Tambahkan method baru ke `ZakatDomain` yang menangani auto-create Family/Muzakki + submit transaksi.
- **Pros:** Satu domain untuk semua logika zakat; reuse method internal (`generateZakatNumber`, dll.) tanpa coupling antar class.
- **Cons:** `ZakatDomain` sudah 14KB (lihat [ADR-004](../adr/004-domain-layer-pattern.md)) — menambah logika auto-create Family dan Muzakki akan membuatnya semakin besar. Auto-create Family/Muzakki bukan core concern domain zakat.
- **Ditolak karena:** Risiko god object. Logika auto-create Family/Muzakki adalah orchestration concern, bukan business rule zakat.

**Alternatif B: `SimpleZakatDomain` — domain baru terpisah**
- **Approach:** Domain class baru (`app/Domains/SimpleZakatDomain.php`) yang mengorkestrasikan auto-create dan delegasi ke `ZakatDomain`.
- **Pros:** `ZakatDomain` tidak membengkak; logika auto-create terisolasi.
- **Cons:** Memecah bounded context — "zakat sederhana" bukan domain bisnis yang berbeda, hanya jalur masuk yang berbeda. Menyesatkan secara DDD: dua domain untuk satu konsep bisnis yang sama. Potensi duplikasi logika jika batas tanggung jawab tidak jelas.
- **Ditolak karena:** Formulir sederhana bukan domain baru. Domain "zakat" tetap satu — yang berbeda adalah cara data masuk ke sistem. Membuat domain terpisah menciptakan ilusi bahwa ada dua bounded context padahal hanya ada satu.

**Alternatif C: Application-layer use case `SubmitSimpleZakat` (DIPILIH)**
- **Approach:** Class use case (`app/UseCases/SubmitSimpleZakat.php`) yang mengorkestrasikan persiapan data (auto-create Family/Muzakki) lalu mendelegasikan ke `ZakatDomain::submitAsMuzakki()` untuk logika transaksi.
- **Pros:** Bounded context tetap utuh — satu `ZakatDomain` untuk semua logika bisnis zakat. Pemisahan concern yang jelas: use case = orchestration, domain = business rules. `ZakatDomain` tidak membengkak. Mudah dipahami: use case menggambarkan "apa yang terjadi saat user submit formulir sederhana" tanpa mencampur dengan aturan bisnis.
- **Cons:** Menambah satu layer baru (`app/UseCases/`) yang belum ada di codebase V1. Use case meng-inject `ZakatDomain` — ada coupling, tapi ini coupling yang valid (use case tahu domain mana yang dipanggil).
- **Trade-off yang diterima:** Layer `UseCases/` baru, tapi hanya satu class. Jika ke depan ada use case lain yang memerlukan orchestration lintas-domain, pattern ini sudah tersedia. Ini bukan over-engineering — ini minimal layering yang diperlukan untuk menjaga domain tetap bersih.

### 6.5 Formulir Sederhana: Family Email — Kolom Baru vs Mengambil dari User

**Alternatif A: Ambil email dari `users.email` (tanpa kolom baru di families)**
- **Approach:** Tidak menambah kolom email di `families`; email ditampilkan dengan mengambil dari `users.email` via relasi `user->family`.
- **Pros:** Tidak ada migrasi tambahan; single source of truth untuk email.
- **Cons:** Family tanpa user yang terhubung tidak punya email; pada formulir sederhana, email yang diinput di form bisa berbeda dari user email (misalnya: email suami untuk keluarga, tapi login dengan email istri); pada mode gerai (V1), petugas membuat Family tanpa user — tidak ada email.
- **Ditolak karena:** Email keluarga dan email user login bisa berbeda. Kepala keluarga bisa berbeda dari user yang login. Kolom email di `families` memberikan fleksibilitas tanpa assumption.

**Alternatif B: Tambah kolom `email` di tabel `families` (DIPILIH)**
- **Approach:** Tambah kolom `email` nullable di `families` untuk menyimpan email kontak keluarga.
- **Pros:** Decoupled dari user email; mendukung skenario di mana email keluarga berbeda dari email login.
- **Cons:** Potensi data tidak sinkron antara `users.email` dan `families.email`.
- **Trade-off yang diterima:** Untuk formulir sederhana, email di form diisi ke `families.email`. Data bisa berbeda dari `users.email` — ini disengaja dan bukan masalah.

### 6.6 Auto Hijri Year: Helper Statis vs Service Class

**Alternatif A: Service class dengan dependency injection**
- **Approach:** `HijriYearService` yang di-inject via constructor, dengan interface untuk testability.
- **Pros:** Mudah di-mock untuk testing; sesuai SOLID principles; bisa di-bind di container.
- **Cons:** Boilerplate untuk sesuatu yang pada dasarnya hanya dua baris kode; perlu inject di setiap controller dan domain yang membutuhkan.
- **Ditolak karena:** Over-engineering. Logika sangat sederhana (`override ?? auto-detect`); tidak ada side effect yang perlu di-mock. Jika perlu testing, static method bisa ditest langsung.

**Alternatif B: Static helper class (DIPILIH)**
- **Approach:** `HijriYearHelper` dengan method static, dipanggil langsung tanpa injection.
- **Pros:** Sederhana; satu titik akses; mudah dicari dalam codebase (`HijriYearHelper::current()`).
- **Cons:** Sulit di-mock untuk unit testing; tight coupling.
- **Trade-off yang diterima:** Untuk logika sesederhana ini, pragmatisme menang atas purisme DI. Testing bisa dilakukan via integration test yang set `AppConfig` value.

### 6.7 Auto Hijri Year: Library Konversi

**Alternatif A: `islamic-network/prayer-times` (API-based)**
- **Approach:** Menggunakan Aladhan API atau library berat yang mencakup waktu shalat + kalender Hijri.
- **Pros:** Akurasi tinggi (observational data).
- **Cons:** Dependensi pada API eksternal; library berat untuk kebutuhan yang hanya perlu tahun; latency network.
- **Ditolak karena:** Hanya butuh tahun Hijriah, bukan tanggal presisi. Dependency pada API eksternal menambah failure point di shared hosting.

**Alternatif B: `geniusts/hijri-dates` (DIPILIH)**
- **Approach:** Library PHP yang Carbon-based, konversi algoritmik Gregorian ke Hijri.
- **Pros:** 330K+ downloads; Carbon-based (cocok Laravel); tidak ada dependensi API; ringan.
- **Cons:** Konversi algoritmik, bukan observational — bisa beda 1-2 hari di batas tahun.
- **Trade-off yang diterima:** Hanya butuh tahun (bukan tanggal), jadi perbedaan 1-2 hari tidak signifikan. Admin override sebagai safety net.

---

## 7. Risks

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| OAuth provider mengubah API/scope | L | M | Laravel Socialite di-maintain aktif; update paket cukup. Fallback: registrasi email/password V1 tetap tersedia |
| Service worker mengganggu session auth | M | H | Service worker hanya cache aset statis (CSS/JS/gambar); semua request navigasi dan POST di-pass-through ke network. Testing manual di setiap browser target |
| Duplikasi Family karena auto-create tanpa matching | M | L | Diketahui dan diterima (sesuai PRD). Volume data satu masjid kecil; admin dapat merge manual. Auto-merge ditunda ke V2 |
| Library Hijri conversion tidak akurat di batas tahun | L | L | Admin override sebagai safety net; notifikasi di halaman admin jika auto-detect berbeda dari override |
| Social login credentials (client ID/secret) bocor | L | H | Simpan di `.env` (bukan hardcode); `.env` sudah di `.gitignore` |
| User social login tanpa email verified stuck di verifikasi | L | M | Cek `email_verified` dari provider; jika true, bypass verifikasi. Jika tidak, fallback ke alur V1 |

---

## 8. Implementation Phases

### Phase 1: Auto Hijri Year + Migrasi Database
**Exit Criteria:** `HijriYearHelper::current()` mengembalikan tahun Hijriah yang benar; semua titik di V1 yang sebelumnya membaca `hijri_year` dari AppConfig sekarang melalui helper; migrasi database untuk nullable fields dan kolom baru berhasil dijalankan.

**Scope:**
- Install package `geniusts/hijri-dates`
- Buat `HijriYearHelper` dengan logika override-then-autodetect
- Refactor semua referensi `AppConfig::getConfigValue('hijri_year')` ke `HijriYearHelper::current()`
- Migrasi: `families.address` nullable, `families.email` nullable, `muzakkis.address` nullable
- Migrasi: `users.social_id` + `users.social_type`
- Update halaman AppConfig admin: tampilkan auto-detect value di samping field override, toggle on/off

**Tasks:** `docs/tasks/` dengan prefix `c6-phase-1`

### Phase 2: Social Login
**Depends on:** Phase 1 (migrasi kolom `social_id`/`social_type` di users)
**Exit Criteria:** User dapat mendaftar dan login via Google dan Facebook; account linking berfungsi untuk email yang sudah terdaftar; user social login masuk ke dashboard dalam < 3 detik.

**Scope:**
- Install Laravel Socialite + konfigurasi Google & Facebook providers
- `SocialLoginController` dengan redirect dan callback
- Tombol social login di halaman login dan registrasi (Vue components)
- Account linking logic (email match)
- Environment variables untuk client ID/secret

**Tasks:** `docs/tasks/` dengan prefix `c6-phase-2`

### Phase 3: Formulir Zakat Sederhana
**Depends on:** Phase 1 (migrasi nullable + email di families), Phase 2 (opsional — formulir bisa digunakan tanpa social login)
**Exit Criteria:** Muzakki baru dapat mengajukan zakat via formulir sederhana dalam < 3 menit; Family dan Muzakki auto-created valid dan dapat digunakan di alur V1; transaksi yang dihasilkan identik dengan V1.

**Scope:**
- `SubmitSimpleZakat` use case (`app/UseCases/`) — orchestration auto-create Family/Muzakki + delegasi ke `ZakatDomain::submitAsMuzakki()`
- `SimpleZakatController` dengan halaman form dan submit
- Vue pages: `SimpleZakat/Create.vue` (form) — reuse halaman detail transaksi V1 untuk show
- Pre-fill data dari Family existing jika user sudah punya data keluarga
- Tombol/link "Bayar Zakat" di dashboard
- Validasi: minimal satu nominal bukan nol

**Tasks:** `docs/tasks/` dengan prefix `c6-phase-3`

### Phase 4: PWA
**Depends on:** Tidak ada dependency ketat — bisa paralel dengan Phase 2/3
**Exit Criteria:** Lighthouse PWA score > 80; aplikasi dapat diinstal ke layar utama; splash screen muncul; tampilan standalone tanpa address bar.

**Scope:**
- `manifest.json` dengan ikon, tema, dan display mode
- `sw.js` dengan cache strategy (cache-first aset statis, network-first navigasi)
- Service worker registration di `app.blade.php`
- Ikon aplikasi (192x192 dan 512x512)
- Testing di Chrome Android dan Safari iOS

**Tasks:** `docs/tasks/` dengan prefix `c6-phase-4`

---

## 9. Operational Concerns

**Logging:**
- Social login: log setiap login sukses dan gagal di level INFO (`User {id} logged in via {provider}`) dan WARNING (`Social login failed: {reason}`)
- Auto-create Family/Muzakki: log setiap auto-create di level INFO (`Family {id} auto-created for user {id}`, `Muzakki {id} auto-created in family {id}`)
- Auto Hijri Year: log saat auto-detect digunakan vs admin override di level DEBUG
- Service worker: client-side logging ke console untuk cache hit/miss (development only)

**Metrics:**
- Jumlah registrasi via social login vs email/password (query: `users WHERE social_type IS NOT NULL`)
- Jumlah transaksi via formulir sederhana (bisa ditambahkan kolom `submission_source` di `zakats` jika diperlukan, atau dihitung dari Family yang auto-created)
- PWA instalasi (tidak dapat diukur server-side — opsional: event tracking via `beforeinstallprompt`)

**Alerts:**
- Tidak ada alerting otomatis tambahan — konsisten dengan V1
- Manual monitoring: cek jumlah transaksi pending di halaman Online Payments

**Rollback:**
- Social login: disable route di `routes/web.php` dan hapus tombol social login di Vue — user yang sudah terdaftar via social tetap bisa login via password reset
- Formulir sederhana: disable route dan tombol dashboard — transaksi yang sudah dibuat tetap valid di V1
- Auto Hijri Year: set override manual di AppConfig — helper tetap berfungsi, tapi override selalu digunakan
- PWA: hapus `manifest.json` dan `sw.js` — browser menghapus service worker secara otomatis setelah tidak ada service worker baru; aplikasi kembali ke web biasa
- Database rollback: kolom tambahan (`social_id`, `social_type`, `families.email`) bersifat nullable — rollback migrasi aman

---

## 10. Security

- [ ] **OAuth credentials:** Client ID dan secret disimpan di `.env`, TIDAK di-commit ke repository. `.env` sudah ada di `.gitignore`
- [ ] **OAuth state parameter:** Laravel Socialite secara otomatis menangani state parameter untuk mencegah CSRF pada OAuth flow
- [ ] **Email trust dari provider:** Hanya percayai `email_verified=true` dari provider. Jika provider tidak mengkonfirmasi email verified, user harus melalui verifikasi email V1
- [ ] **Account linking security:** Linking hanya berdasarkan email match yang exact — tidak ada fuzzy matching. Ini aman karena email dari Google/Facebook sudah diverifikasi oleh provider
- [ ] **Social login password:** User yang dibuat via social login mendapat password acak yang di-hash. Mereka tidak bisa login via password kecuali melakukan password reset. Ini mencegah account takeover jika social account dikompromikan
- [ ] **Service worker scope:** Service worker hanya cache aset statis. Tidak meng-intercept POST request atau request dengan cookie — menghindari cache poisoning pada data sensitif
- [ ] **Input validation formulir sederhana:** Validasi server-side di controller untuk semua field (nama, email, telepon, nominal). Tidak bergantung pada validasi client-side Vue
- [ ] **Family ownership:** `SubmitSimpleZakat` use case memverifikasi bahwa muzakki_id yang dikirim dalam form benar-benar milik family user — mencegah cross-family data manipulation
- [ ] **DB transaction integrity:** Seluruh proses di `SubmitSimpleZakat` (auto-create Family, auto-create Muzakki, delegasi ke `ZakatDomain`) dibungkus dalam `DB::transaction()` — gagal di titik mana pun akan rollback semua perubahan
- [ ] **Rate limiting social login:** Laravel default throttle pada route login (60 req/menit) berlaku juga untuk social login callback — mencegah abuse

---

## 11. Open Questions

Tidak ada — semua pertanyaan telah dijawab di [PRD C6](../prd/006-simplified-self-service.md#resolved-questions). RFC ini siap untuk review.

---

## References

- [PRD C6: Simplified Self-Service Zakat](../prd/006-simplified-self-service.md)
- [Product Vision](../prd/_index.md)
- [RFC-001: V1 System Design](./001-system-design.md)
- [ADR-001: Laravel + PHP Framework](../adr/001-laravel-php-framework.md)
- [ADR-002: MySQL Database](../adr/002-mysql-database.md)
- [ADR-003: Inertia.js + Vue 3 Frontend](../adr/003-inertia-vue-frontend.md)
- [ADR-004: Domain Layer Pattern](../adr/004-domain-layer-pattern.md)
- [ADR-005: Soft Delete & Audit Logging](../adr/005-soft-delete-audit-logging.md)
- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [geniusts/hijri-dates Package](https://github.com/GeniusTS/hijri-dates)
- [MDN: Progressive Web Apps](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
