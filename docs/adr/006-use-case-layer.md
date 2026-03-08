# ADR-006: Use Case Layer untuk Orkestrasi Lintas-Domain

| Status | Date | Deciders |
|--------|------|----------|
| Accepted | 2026-03-08 | UPZIS Team |

**Status Values:** Proposed → ~~Accepted~~ → Deprecated | Superseded by ADR-XXX

---

## Context

Arsitektur V1 menggunakan pola `Controller → Domain → Model` (lihat [ADR-004](./004-domain-layer-pattern.md)) dengan dua domain class: `ZakatDomain` (logika transaksi zakat) dan `ResidenceDomain` (logika alamat BPI). Pola ini bekerja baik selama setiap controller hanya perlu memanggil satu domain.

C6 (Simplified Self-Service) memperkenalkan skenario baru: formulir zakat sederhana perlu mengorkestrasikan beberapa langkah lintas-concern sebelum membuat transaksi:
1. Auto-create `Family` jika pengguna belum memiliki data keluarga
2. Auto-create `Muzakki` untuk setiap anggota yang dimasukkan di formulir
3. Membuat transaksi zakat menggunakan logika bisnis yang sudah ada di `ZakatDomain`

Langkah 1 dan 2 bukan logika bisnis zakat — ini adalah persiapan data. Langkah 3 adalah delegasi ke domain yang sudah ada. Pertanyaannya: di mana menempatkan logika orkestrasi ini?

---

## Decision

**We will:** Memperkenalkan layer `app/UseCases/` untuk class yang mengorkestrasikan alur lintas-concern. Use case menerima input dari controller, menyiapkan data yang diperlukan, lalu mendelegasikan logika bisnis ke domain yang sesuai.

**We will not:**
- Menambahkan logika orkestrasi ke domain class yang ada (mencegah god object)
- Membuat domain baru untuk setiap jalur masuk yang berbeda (mencegah pemecahan bounded context)
- Menggunakan use case untuk alur yang hanya memanggil satu domain tanpa orkestrasi tambahan (mencegah pass-through layer)

---

## Rationale

**Chose this because:**
- Bounded context tetap utuh — `ZakatDomain` tetap satu-satunya sumber logika bisnis zakat, tidak peduli dari jalur mana transaksi masuk
- `ZakatDomain` (sudah 14KB) tidak membengkak dengan logika yang bukan tanggung jawabnya
- Pemisahan concern yang jelas: use case = "apa yang terjadi saat user melakukan X", domain = "aturan bisnis area Y"
- Use case meng-inject domain via constructor — coupling yang valid dan eksplisit

**Deciding factor:**
- Formulir sederhana bukan domain bisnis baru. Domain "zakat" tetap satu — yang berbeda adalah cara data masuk ke sistem. Membuat domain terpisah (`SimpleZakatDomain`) menciptakan ilusi bahwa ada dua bounded context padahal hanya ada satu.

**Accepted trade-offs:**
- Menambah satu layer arsitektur baru yang belum ada di V1 — namun hanya digunakan saat ada orkestrasi nyata, bukan untuk setiap alur
- Use case class bisa tergoda menjadi "fat use case" jika tidak dijaga — konvensi: use case hanya orchestrate, tidak berisi business rules

---

## Alternatives Considered

| Alternative | Rejected Because |
|-------------|------------------|
| Tambah method `submitSimpleZakat()` ke `ZakatDomain` | `ZakatDomain` sudah 14KB; auto-create Family/Muzakki bukan concern domain zakat; risiko god object |
| Buat `SimpleZakatDomain` baru | Memecah bounded context — "zakat sederhana" bukan domain terpisah, hanya jalur masuk berbeda; menyesatkan secara DDD |
| Letakkan orkestrasi di controller | Melanggar prinsip thin controller dari ADR-004; controller seharusnya hanya menangani HTTP concerns |

---

## Consequences

**Positive:**
- Pola call chain menjadi jelas: `Controller → UseCase → Domain(s) → Model`
- Domain classes tetap fokus pada business rules tanpa dibebani orkestrasi
- Jika ada alur lintas-domain baru di masa depan, pattern sudah tersedia
- Mudah di-test: use case bisa di-test dengan meng-inject domain

**Negative:**
- Satu layer tambahan yang perlu dipahami developer baru
- Risiko over-use: developer bisa tergoda membuat use case untuk alur sederhana yang seharusnya cukup `Controller → Domain`

**Neutral:**
- Konvensi penggunaan: use case hanya dibuat jika ada orkestrasi lintas-concern. Jika controller hanya memanggil satu domain method, langsung panggil domain — tidak perlu use case
- Path `app/UseCases/` non-standar di ekosistem Laravel, sama seperti `app/Domains/` — perlu dokumentasi

---

## References

- [ADR-004: Domain Layer Pattern](./004-domain-layer-pattern.md) — pola yang diperluas
- [RFC-002: Simplified Self-Service Zakat — Section 3.3 & 6.4](../rfc/002-simplified-self-service.md)
- [PRD C6: Simplified Self-Service Zakat](../prd/006-simplified-self-service.md)
