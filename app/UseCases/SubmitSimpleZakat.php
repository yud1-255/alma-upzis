<?php

namespace App\UseCases;

use App\Domains\ZakatDomain;
use App\Helpers\HijriYearHelper;
use App\Models\Family;
use App\Models\Muzakki;
use App\Models\User;
use App\Models\Zakat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubmitSimpleZakat
{
    private ZakatDomain $zakatDomain;

    public function __construct(ZakatDomain $zakatDomain)
    {
        $this->zakatDomain = $zakatDomain;
    }

    /**
     * Orchestrate simplified zakat submission: auto-create family/muzakki,
     * then delegate to ZakatDomain for the actual transaction.
     *
     * @param User $user The authenticated user submitting zakat
     * @param array $data Validated input containing contact info and members with zakat amounts
     * @return Zakat The created zakat transaction
     */
    public function execute(User $user, array $data): Zakat
    {
        return DB::transaction(function () use ($user, $data) {
            $family = $this->findOrCreateFamily($user, [
                'head_of_family' => $data['head_of_family'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            $muzakkis = $this->syncMuzakkis($family, $data['members']);

            $zakatLines = $this->buildZakatLines($muzakkis, $data['members']);

            $zakat = new Zakat();
            $zakat->transaction_date = now()->toDateString();
            $zakat->hijri_year = HijriYearHelper::current();
            $zakat->family_head = $family->head_of_family;
            $zakat->total_rp = $this->calculateTotalRp($zakatLines);

            return $this->zakatDomain->submitAsMuzakki($zakat, $zakatLines);
        });
    }

    /**
     * Find existing family for user, or create a new one with minimal defaults.
     *
     * If user already has a family_id, returns that family (updating contact info).
     * Otherwise creates a new Family and links it to the user.
     */
    public function findOrCreateFamily(User $user, array $contactData): Family
    {
        if ($user->family_id) {
            $family = Family::findOrFail($user->family_id);
            $family->update([
                'head_of_family' => $contactData['head_of_family'],
                'phone' => $contactData['phone'],
                'email' => $contactData['email'],
            ]);

            return $family;
        }

        $family = Family::create([
            'head_of_family' => $contactData['head_of_family'],
            'phone' => $contactData['phone'],
            'email' => $contactData['email'],
            'address' => null,
            'kk_number' => null,
            'is_bpi' => false,
        ]);

        $user->family()->associate($family);
        $user->save();

        return $family;
    }

    /**
     * Sync muzakkis for the family based on submitted members.
     *
     * For each member:
     * - If muzakki_id is set and belongs to the family, reuse it
     * - If muzakki_id is null, create a new Muzakki with minimal defaults
     *
     * @param Family $family
     * @param array $members Array of member data with optional muzakki_id and name
     * @return Collection<int, Muzakki> Ordered collection matching input members order
     */
    public function syncMuzakkis(Family $family, array $members): Collection
    {
        $muzakkis = collect();

        foreach ($members as $member) {
            $muzakkiId = $member['muzakki_id'] ?? null;

            if ($muzakkiId !== null) {
                $muzakki = Muzakki::where('id', $muzakkiId)
                    ->where('family_id', $family->id)
                    ->firstOrFail();

                $muzakki->update(['name' => $member['name']]);
            } else {
                $muzakki = Muzakki::create([
                    'family_id' => $family->id,
                    'name' => $member['name'],
                    'phone' => null,
                    'address' => null,
                    'is_bpi' => false,
                    'use_family_address' => true,
                ]);
                $muzakki->is_active = true;
                $muzakki->save();
            }

            $muzakkis->push($muzakki);
        }

        return $muzakkis;
    }

    /**
     * Build zakat line arrays from synced muzakkis and member zakat data.
     *
     * @param Collection<int, Muzakki> $muzakkis Ordered muzakkis matching members order
     * @param array $members Original member data with zakat amounts
     * @return array Array of zakat line data ready for createMany
     */
    private function buildZakatLines(Collection $muzakkis, array $members): array
    {
        $zakatLines = [];

        foreach ($muzakkis as $index => $muzakki) {
            $zakatAmounts = $members[$index]['zakat'];

            $zakatLines[] = [
                'muzakki_id' => $muzakki->id,
                'fitrah_rp' => $zakatAmounts['fitrah_rp'] ?? 0,
                'fitrah_kg' => $zakatAmounts['fitrah_kg'] ?? 0,
                'fitrah_lt' => $zakatAmounts['fitrah_lt'] ?? 0,
                'maal_rp' => $zakatAmounts['maal_rp'] ?? 0,
                'profesi_rp' => $zakatAmounts['profesi_rp'] ?? 0,
                'infaq_rp' => $zakatAmounts['infaq_rp'] ?? 0,
                'wakaf_rp' => $zakatAmounts['wakaf_rp'] ?? 0,
                'fidyah_rp' => $zakatAmounts['fidyah_rp'] ?? 0,
                'fidyah_kg' => $zakatAmounts['fidyah_kg'] ?? 0,
                'kafarat_rp' => $zakatAmounts['kafarat_rp'] ?? 0,
            ];
        }

        return $zakatLines;
    }

    /**
     * Sum all rupiah-denominated fields across zakat lines.
     */
    private function calculateTotalRp(array $zakatLines): float
    {
        $rpFields = ['fitrah_rp', 'maal_rp', 'profesi_rp', 'infaq_rp', 'wakaf_rp', 'fidyah_rp', 'kafarat_rp'];

        $total = 0;
        foreach ($zakatLines as $line) {
            foreach ($rpFields as $field) {
                $total += $line[$field] ?? 0;
            }
        }

        return $total;
    }
}
