# ADR-005: Soft Delete dan Audit Logging untuk Transaksi

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-02-27 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Transaksi zakat adalah catatan keuangan masjid yang bersifat sensitif dan harus akuntabel. Dalam operasional:
- Petugas UPZIS terkadang perlu membatalkan transaksi yang salah input
- Muzakki perlu melihat status transaksinya (diajukan, dikonfirmasi, dibatalkan)
- Pengurus masjid memerlukan jejak audit untuk akuntabilitas — siapa melakukan apa dan kapan
- Regulasi dan prinsip akuntansi mengharuskan catatan keuangan tidak boleh dihapus secara permanen

Perlu diputuskan bagaimana menangani pembatalan transaksi dan bagaimana mencatat setiap perubahan status.

---

## Decision

**We will:** Menggunakan soft delete untuk transaksi yang dibatalkan dan tabel `zakat_logs` terpisah sebagai audit trail yang mencatat setiap perubahan status transaksi (submit, konfirmasi, pembatalan).

**We will not:** Menghapus transaksi secara permanen dari database. Juga tidak menyimpan audit log sebagai kolom status di tabel `zakats` — perubahan status dicatat secara kronologis di tabel terpisah.

---

## Rationale

**Chose this because:**
- Catatan keuangan tidak boleh hilang — soft delete memastikan data selalu dapat dipulihkan dan diaudit
- Tabel `zakat_logs` yang terpisah memberikan timeline kronologis per transaksi — kapan diajukan, kapan dikonfirmasi, kapan dibatalkan, dan oleh siapa
- Integer action codes (1=submit, 2=confirm, 3=void) sederhana dan efisien untuk kueri
- Muzakki yang membatalkan dan membuat ulang transaksi tetap memiliki riwayat lengkap
- Laporan keuangan dapat selalu direkonstruksi dari data historis

**Deciding factor:**
- Akuntabilitas dan transparansi adalah prinsip produk inti — setiap perubahan harus dapat ditelusuri ke individu dan waktu spesifik.

**Accepted trade-offs:**
- Status transaksi bersifat derived (diturunkan dari kombinasi kolom `zakat_pic`, `payment_date`, dan log terakhir) — bukan kolom status eksplisit. Ini membuat kueri status sedikit lebih kompleks
- Data tidak pernah benar-benar dihapus — database akan terus bertumbuh (namun volume data satu masjid sangat kecil, sehingga tidak menjadi masalah dalam jangka panjang)
- Muzakki yang dinonaktifkan (`is_active=false`) tetap ada di database — konsisten dengan filosofi "tidak pernah hapus"

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Hard delete (hapus permanen) | Melanggar prinsip akuntabilitas; data keuangan hilang secara permanen; tidak ada jejak audit untuk transaksi yang dibatalkan |
| Status column pada tabel `zakats` | Hanya menyimpan status terakhir, bukan timeline perubahan; kehilangan informasi siapa yang melakukan aksi dan kapan |
| Laravel SoftDeletes trait | Hanya menandai `deleted_at` — tidak mencatat siapa yang menghapus atau alasannya; kurang granular dibanding audit log terpisah |
| Event sourcing | Sangat overkill untuk skala ini; membutuhkan infrastruktur dan pola yang kompleks; tim tidak familiar |

---

## Consequences

**Positive:**
- Setiap aksi pada transaksi tercatat dan dapat ditelusuri
- Halaman detail transaksi menampilkan timeline aktivitas yang jelas
- Laporan keuangan selalu akurat dan dapat diverifikasi
- Tidak ada risiko kehilangan data karena kesalahan operasional

**Negative:**
- Kueri "transaksi aktif" memerlukan JOIN atau subquery ke `zakat_logs` untuk mengecualikan yang void
- Tidak ada kolom status tunggal yang bisa difilter — logika status tersebar di beberapa kolom
- Database bertumbuh tanpa batas (namun tidak signifikan untuk volume data masjid)

**Neutral:**
- Pola yang sama (soft delete + audit log) diterapkan pada muzakki (`is_active` flag)
- Log action codes perlu didokumentasikan agar developer baru memahami artinya (1=submit, 2=confirm, 3=void)
- Tidak ada mekanisme purging data lama — semua data disimpan selamanya (sesuai prinsip arsip permanen masjid)

---

## References

- [RFC-001: V1 System Design — Section 4 Data Model](../rfc/001-system-design.md)
- [C3: Zakat Transaction Management PRD](../prd/003-zakat-transactions.md)
- [Product Vision — Product Principles #2](../prd/_index.md)
