# ADR-004: Domain Layer untuk Pemisahan Logika Bisnis

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-02-27 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Alma UPZIS memiliki logika bisnis yang cukup kompleks, terutama di area transaksi zakat:
- Dua mode pengajuan (daring dan gerai) dengan aturan berbeda
- Penghitungan nominal transfer unik (unique_number)
- Penomoran transaksi otomatis (sequence number)
- Konfirmasi dan pembatalan transaksi dengan audit logging
- Kueri agregasi untuk enam jenis laporan
- Logika alamat BPI dengan cascade update ke muzakki

Jika semua logika ini ditempatkan langsung di controller, controller akan menjadi sangat besar (fat controllers) dan sulit diuji. Perlu ada lapisan pemisah antara controller (HTTP concerns) dan model (data persistence).

---

## Decision

**We will:** Menggunakan Domain classes (`app/Domains/`) sebagai lapisan logika bisnis antara controller dan model Eloquent. Setiap domain bertanggung jawab atas satu area bisnis.

**We will not:** Menempatkan logika bisnis kompleks langsung di controller atau di model Eloquent. Juga tidak menggunakan Repository pattern atau full-blown DDD (Domain-Driven Design) yang terlalu kompleks untuk skala ini.

---

## Rationale

**Chose this because:**
- Controller tetap tipis — hanya menangani request/response, validasi input, dan routing ke domain
- Logika bisnis terpusat dan dapat diuji secara independen dari HTTP layer
- Lebih sederhana daripada Repository pattern — domain langsung menggunakan Eloquent model, tanpa abstraksi tambahan
- Dua domain (`ZakatDomain`, `ResidenceDomain`) cukup mencakup seluruh logika bisnis aplikasi
- Penamaan "Domain" intuitif — setiap class merepresentasikan area bisnis yang jelas

**Deciding factor:**
- `ZakatDomain` berisi ~14KB logika (submission, konfirmasi, pembatalan, reporting) — terlalu banyak untuk satu controller, terlalu beragam untuk satu model. Domain layer adalah kompromi yang tepat.

**Accepted trade-offs:**
- Tidak ada interface/contract formal — domain langsung di-instantiate, bukan di-inject melalui interface
- Domain methods terkadang melakukan query dan persistence sekaligus — tidak sepenuhnya murni business logic
- Tambahan satu layer abstraksi — namun hanya dua class, tidak over-engineered

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Fat controllers (semua logika di controller) | Controller akan berisi ratusan baris logika bisnis; sulit diuji; duplicate logic saat beberapa controller membutuhkan operasi yang sama |
| Repository pattern | Over-engineering untuk skala ini; Eloquent sudah menyediakan query builder yang cukup kuat; menambah boilerplate tanpa manfaat signifikan |
| Service + Repository pattern (full DDD) | Terlalu banyak abstraksi untuk dua area bisnis; membutuhkan effort maintenance yang tidak sebanding |
| Model-based logic (fat models) | Model `Zakat` akan menjadi sangat besar; logika lintas-model (user + family + zakat) sulit ditempatkan di satu model |

---

## Consequences

**Positive:**
- Controller rata-rata hanya 20-40 baris per method — mudah dibaca dan dipahami
- `ZakatDomain` menjadi single source of truth untuk semua logika transaksi
- `ResidenceDomain` mengenkapsulasi semua logika alamat BPI dan pencarian keluarga
- Mudah menambah domain baru jika ada area bisnis baru di masa depan

**Negative:**
- Tidak ada enforced contract — developer harus disiplin menempatkan logika bisnis di domain, bukan di controller
- Domain classes bisa menjadi "god objects" jika tidak dijaga — `ZakatDomain` sudah cukup besar (14KB)
- Tidak ada dependency injection formal — sulit di-mock untuk unit testing tanpa refactoring

**Neutral:**
- Pola ini bersifat konvensi tim, bukan framework convention — perlu dokumentasi agar developer baru memahami struktur
- Domain class hidup di `app/Domains/` — path non-standar di ekosistem Laravel

---

## References

- [RFC-001: V1 System Design — Section 3.1 Domain Layer](../rfc/001-system-design.md)
- [ADR-001: Laravel + PHP Framework](./001-laravel-php-framework.md)
