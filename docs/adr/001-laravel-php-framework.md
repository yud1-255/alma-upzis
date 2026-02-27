# ADR-001: Laravel 8 + PHP sebagai Backend Framework

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-02-27 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Alma UPZIS membutuhkan framework backend untuk membangun aplikasi web pengelolaan zakat yang mencakup autentikasi pengguna, CRUD keluarga/muzakki, pengelolaan transaksi, dan pelaporan.

Constraints:
- Tim kecil dengan pengalaman utama di ekosistem PHP
- Deployment target adalah shared hosting PHP/MySQL standar (bukan container/cloud)
- Timeline ketat — harus siap sebelum Ramadhan
- Membutuhkan scaffolding autentikasi lengkap (registrasi, login, verifikasi email, reset password)
- Membutuhkan ORM untuk model data relasional (keluarga → muzakki → transaksi)
- Membutuhkan dukungan paket ekspor Excel

---

## Decision

**We will:** Menggunakan Laravel 8 (PHP 7.3+/8.0+) sebagai backend framework untuk seluruh aplikasi.

**We will not:** Menggunakan framework non-PHP (Node.js, Python/Django, Ruby/Rails) atau PHP framework lain (Symfony, CodeIgniter).

---

## Rationale

**Chose this because:**
- Laravel Breeze menyediakan scaffolding autentikasi lengkap out-of-the-box: registrasi, login, verifikasi email, reset password, konfirmasi password — mengurangi waktu development secara signifikan
- Eloquent ORM menyederhanakan model data relasional yang kompleks (User ↔ Role, Family → Muzakki → ZakatLine)
- Ekosistem paket Laravel yang matang: Maatwebsite Excel untuk ekspor, Sanctum untuk API token (disiapkan untuk masa depan)
- Shared hosting PHP/MySQL tersedia dengan biaya sangat rendah — sesuai budget masjid
- Tim sudah produktif dengan PHP/Laravel — tidak ada kurva belajar

**Deciding factor:**
- Kombinasi Laravel Breeze + Inertia.js stack memberikan autentikasi + SPA dalam satu scaffolding, menghemat waktu berminggu-minggu.

**Accepted trade-offs:**
- Terikat pada ekosistem PHP untuk seluruh backend — migrasi ke framework lain akan sangat mahal
- Performance PHP lebih rendah dibanding Go/Rust untuk high-concurrency, namun tidak relevan untuk skala satu masjid
- Laravel 8 bukan versi terbaru (LTS sampai tertentu) — perlu perencanaan upgrade ke versi lebih baru

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Node.js (Express/Nest) | Tim tidak familiar; hosting Node.js lebih mahal; tidak ada scaffolding autentikasi setara Breeze |
| Python (Django) | Hosting Python kurang tersedia di shared hosting Indonesia; ORM Django lebih verbose untuk relasi kompleks |
| Symfony | Lebih berat dan verbose dibanding Laravel; scaffolding autentikasi memerlukan lebih banyak konfigurasi manual |
| CodeIgniter | Kurang fitur modern (tidak ada ORM sekuat Eloquent, tidak ada migration system yang matang, tidak ada Inertia.js integration) |

---

## Consequences

**Positive:**
- Development cepat berkat scaffolding dan convention-over-configuration
- Deployment sederhana di shared hosting tanpa infrastruktur khusus
- Ekosistem paket yang kaya untuk kebutuhan masa depan
- Dokumentasi dan komunitas Laravel sangat besar

**Negative:**
- Terikat pada release cycle Laravel — perlu maintenance upgrade versi
- PHP memory management kurang efisien untuk operasi heavy (mitigasi: ekspor Excel menggunakan chunked query)
- Monolith coupling — sulit memecah menjadi microservices jika skala bertumbuh signifikan

**Neutral:**
- Perlu memilih strategi frontend terpisah (lihat [ADR-003](./003-inertia-vue-frontend.md))
- Database choice terkait erat dengan ekosistem Laravel (lihat [ADR-002](./002-mysql-database.md))

---

## References

- [RFC-001: V1 System Design](../rfc/001-system-design.md)
- [Laravel 8 Documentation](https://laravel.com/docs/8.x)
- [Laravel Breeze](https://laravel.com/docs/8.x/starter-kits#laravel-breeze)
