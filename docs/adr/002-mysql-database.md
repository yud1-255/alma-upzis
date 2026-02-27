# ADR-002: MySQL sebagai Database

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-02-27 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Alma UPZIS membutuhkan database untuk menyimpan data pengguna, keluarga, muzakki, transaksi zakat (dengan baris detail per muzakki), log audit, dan konfigurasi aplikasi.

Karakteristik data:
- Model relasional yang jelas: keluarga → muzakki, transaksi → baris detail, user ↔ roles
- Transaksi keuangan membutuhkan integritas ACID (nominal harus konsisten)
- Volume data relatif kecil: satu masjid, ratusan keluarga, ribuan transaksi per tahun
- Laporan dan ekspor mengandalkan query agregasi (SUM, GROUP BY, JOIN)

Constraints:
- Shared hosting standar Indonesia umumnya menyediakan MySQL
- Biaya infrastruktur harus sangat rendah (organisasi nirlaba masjid)
- Tim familiar dengan MySQL

---

## Decision

**We will:** Menggunakan MySQL sebagai satu-satunya database untuk seluruh aplikasi.

**We will not:** Menggunakan database NoSQL, PostgreSQL, atau SQLite untuk produksi. Juga tidak menggunakan database terpisah untuk reporting (single DB untuk OLTP dan reporting).

---

## Rationale

**Chose this because:**
- MySQL adalah database standar di shared hosting PHP Indonesia — tersedia gratis di hampir semua paket hosting
- Laravel Eloquent memiliki dukungan MySQL yang sangat matang dan teruji
- Model data sepenuhnya relasional — foreign keys, joins, dan agregasi dibutuhkan di mana-mana
- ACID compliance menjamin konsistensi data transaksi keuangan
- Performa lebih dari cukup untuk volume data satu masjid

**Deciding factor:**
- Ketersediaan MySQL di shared hosting target — tidak ada pilihan lain yang sama mudahnya di-deploy.

**Accepted trade-offs:**
- Tidak ada unique constraint pada `kk_number` — duplikasi keluarga mungkin terjadi (mitigasi: pencarian KK mendorong pengguna menghubungkan ke keluarga yang sudah ada)
- Tidak ada unique constraint pada `app_config.key` — disengaja untuk multi-value support (`fitrah_amount`)
- Tabel `app_config` menggunakan pola key-value yang kurang ideal di RDBMS (namun jumlah kunci sangat sedikit sehingga tidak menjadi masalah performa)

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| PostgreSQL | Tidak tersedia di mayoritas shared hosting PHP Indonesia; fitur tambahan (JSONB, full-text search) tidak dibutuhkan untuk use case ini |
| SQLite | Tidak cocok untuk concurrent access dari multiple web requests; tidak ada alat administrasi standar di hosting |
| MongoDB | Model data sepenuhnya relasional; membutuhkan JOIN dan agregasi yang sulit di document DB; biaya hosting terpisah |
| MariaDB | Compatible dan bisa menjadi alternatif, namun shared hosting umumnya label MySQL — bukan penolakan aktif |

---

## Consequences

**Positive:**
- Nol biaya infrastruktur database tambahan — sudah termasuk di paket hosting
- Tooling administrasi (phpMyAdmin) tersedia di semua hosting
- Backup dan restore sederhana (mysqldump)
- Tim dapat mengelola database tanpa DBA

**Negative:**
- Horizontal scaling sulit jika volume data bertumbuh sangat besar (tidak relevan untuk skala saat ini)
- Beberapa fitur PostgreSQL yang berguna (JSONB, native full-text search, materialized views) tidak tersedia
- Schema changes memerlukan migration — tidak sefleksibel document store

**Neutral:**
- Perlu memastikan backup rutin dikonfigurasi di hosting
- Migrasi ke PostgreSQL di masa depan relatif mudah karena Eloquent abstraksi database layer

---

## References

- [RFC-001: V1 System Design](../rfc/001-system-design.md)
- [ADR-001: Laravel + PHP Framework](./001-laravel-php-framework.md)
