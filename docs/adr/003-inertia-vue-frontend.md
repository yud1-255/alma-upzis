# ADR-003: Inertia.js + Vue 3 sebagai Frontend Strategy

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-02-27 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Alma UPZIS membutuhkan antarmuka web yang responsif dan interaktif. Beberapa halaman memiliki interaksi kompleks:
- Form entri zakat dengan baris muzakki dinamis (tambah/hapus), perhitungan total real-time, dan validasi client-side
- Autocomplete pencarian keluarga untuk mode gerai
- Pemilih alamat BPI bertingkat (blok → nomor rumah)
- Copy-to-clipboard untuk nominal transfer

Pada saat yang sama, sebagian besar halaman bersifat CRUD standar (daftar, detail, form sederhana) yang tidak memerlukan interaksi kompleks.

Constraints:
- Tidak ada kebutuhan API eksternal (tidak ada mobile app, tidak ada integrasi pihak ketiga)
- Tim tidak ingin mengelola dua codebase terpisah (backend API + frontend SPA)
- Harus tetap menggunakan routing dan validasi server-side Laravel
- Perlu pengalaman navigasi cepat tanpa full page reload

---

## Decision

**We will:** Menggunakan Inertia.js sebagai bridge antara Laravel backend dan Vue 3 frontend, sehingga mendapatkan pengalaman SPA tanpa membangun REST API terpisah.

**We will not:** Membangun REST API terpisah untuk dikonsumsi frontend. Juga tidak menggunakan Blade templates murni untuk halaman yang membutuhkan interaksi kompleks.

---

## Rationale

**Chose this because:**
- Inertia.js menghilangkan kebutuhan REST API — controller Laravel mengirim data langsung ke komponen Vue sebagai props
- Routing tetap di server-side (Laravel) — satu sumber kebenaran, tidak ada duplikasi routing
- Validasi server-side Laravel langsung ter-surface di Vue tanpa perlu penanganan error API khusus
- Laravel Breeze menyediakan scaffolding Inertia.js + Vue 3 out-of-the-box
- Vue 3 Composition API memberikan struktur yang baik untuk halaman interaktif kompleks (form entri zakat)

**Deciding factor:**
- Satu sumber routing dan validasi di server — mengurangi cognitive overhead untuk tim kecil, sambil tetap mendapatkan interaktivitas SPA.

**Accepted trade-offs:**
- Coupling ketat antara Laravel dan Vue — tidak bisa mengganti salah satu tanpa mengganti keduanya
- SEO tidak optimal untuk halaman publik (mitigasi: hanya halaman Welcome yang publik, sisanya di balik autentikasi)
- Tidak ada REST API yang bisa dikonsumsi mobile app di masa depan — harus dibangun terpisah jika dibutuhkan
- Setiap navigasi tetap melakukan round-trip ke server (bukan client-side routing murni) — namun terasa cepat karena hanya mengirim JSON, bukan full HTML

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Blade templates murni (tanpa SPA) | Form entri zakat sangat interaktif (baris dinamis, perhitungan live, autocomplete) — Blade dengan jQuery menghasilkan JavaScript ad-hoc yang sulit dipelihara |
| Vue 3 SPA terpisah + Laravel REST API | Duplikasi routing dan validasi; perlu mengelola CORS, token auth, dan state management terpisah; overhead terlalu besar untuk tim kecil tanpa kebutuhan API eksternal |
| React + Inertia.js | Inertia mendukung React, namun Laravel Breeze scaffolding lebih matang untuk Vue; tim lebih familiar dengan Vue |
| Livewire (Laravel) | Alternatif yang valid, namun kurang cocok untuk form yang sangat interaktif dengan manipulasi array kompleks (baris muzakki dinamis); Vue memberikan kontrol lebih granular |

---

## Consequences

**Positive:**
- Development lebih cepat — tidak perlu membangun dan memelihara API layer
- Konsistensi — routing, validasi, dan autentikasi semuanya di satu tempat (Laravel)
- Pengalaman pengguna SPA-like (navigasi tanpa full page reload)
- Komponen Vue dapat di-reuse di seluruh halaman

**Negative:**
- Tidak ada API yang bisa dikonsumsi mobile app atau integrasi pihak ketiga
- Perlu build step (Laravel Mix/Webpack) untuk kompilasi Vue — menambah kompleksitas development setup
- Bundle JavaScript cukup besar (Vue + Inertia + semua halaman)

**Neutral:**
- Tailwind CSS dipilih sebagai CSS framework (bawaan Laravel Breeze Inertia stack)
- Alpine.js juga tersedia untuk interaksi ringan tanpa Vue component
- Hot Module Replacement (HMR) tersedia melalui Laravel Mix untuk development

---

## References

- [RFC-001: V1 System Design](../rfc/001-system-design.md)
- [ADR-001: Laravel + PHP Framework](./001-laravel-php-framework.md)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Laravel Breeze — Inertia Stack](https://laravel.com/docs/8.x/starter-kits#breeze-and-inertia)
