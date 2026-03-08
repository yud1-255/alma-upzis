<?php

namespace App\Http\Controllers;

use App\Domains\ZakatDomain;
use App\Helpers\HijriYearHelper;
use App\Models\AppConfig;
use App\Models\Zakat;
use App\UseCases\SubmitSimpleZakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SimpleZakatController extends Controller
{
    /**
     * Show the simplified zakat creation form.
     *
     * Pre-fills contact data if the user already has a family record.
     */
    public function create()
    {
        $user = Auth::user();
        $family = $user->family;

        $prefill = [
            'head_of_family' => $family->head_of_family ?? $user->name,
            'email' => $family->email ?? $user->email,
            'phone' => $family->phone ?? '',
        ];

        $muzakkis = $family
            ? $family->muzakkis->where('is_active', true)->values()
            : collect();

        return Inertia::render('SimpleZakat/Create', [
            'prefill' => $prefill,
            'muzakkis' => $muzakkis,
            'hijri_year' => HijriYearHelper::current(),
            'fitrah_amount' => AppConfig::getConfigValues('fitrah_amount'),
        ]);
    }

    /**
     * Validate and submit a simplified zakat transaction.
     *
     * Creates family and muzakki records as needed, then delegates
     * to ZakatDomain for the actual transaction creation.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'head_of_family' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'members' => ['required', 'array', 'min:1'],
            'members.*.muzakki_id' => ['nullable', 'integer'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.zakat' => ['required', 'array'],
            'members.*.zakat.fitrah_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.fitrah_kg' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.fitrah_lt' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.maal_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.profesi_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.infaq_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.wakaf_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.fidyah_rp' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.fidyah_kg' => ['required', 'numeric', 'min:0'],
            'members.*.zakat.kafarat_rp' => ['required', 'numeric', 'min:0'],
        ]);

        $this->validateMuzakkiOwnership($user, $validated['members']);

        $zakatDomain = new ZakatDomain($user);
        $useCase = new SubmitSimpleZakat($zakatDomain);
        $zakat = $useCase->execute($user, $validated);

        return Redirect::route('zakat.show', ['zakat' => $zakat]);
    }

    /**
     * Show a specific simple zakat transaction detail.
     *
     * Reuses the existing Zakat/Show view since the transaction
     * structure is identical to standard zakat submissions.
     */
    public function show(int $id)
    {
        $user = Auth::user();
        $zakat = Zakat::findOrFail($id);

        if ($user->cannot('view', $zakat)) {
            abort(403);
        }

        $domain = new ZakatDomain($user);
        $zakatTx = Zakat::with('zakatLines', 'receiveFrom', 'zakatPic', 'zakatLines.muzakki')
            ->where('id', $zakat->id)->first();

        return Inertia::render('Zakat/Show', [
            'zakat' => $zakatTx,
            'logs' => $domain->getActivityLogs($zakatTx),
            'displayBankAccount' => $domain->isInBankTransferPeriod(Date::now()),
            'displayQRIS' => $domain->isInQRISPaymentPeriod(Date::now()),
            'bankAccount' => AppConfig::getConfigValue('bank_account'),
            'can' => [
                'print' => $user->can('print', $zakat),
                'confirmPayment' => $user->can('confirmPayment', $zakat),
            ],
        ]);
    }

    /**
     * Verify that any provided muzakki_id values belong to the user's family.
     *
     * Prevents users from attaching zakat lines to muzakkis they do not own.
     */
    private function validateMuzakkiOwnership(mixed $user, array $members): void
    {
        $familyId = $user->family_id;

        foreach ($members as $index => $member) {
            $muzakkiId = $member['muzakki_id'] ?? null;

            if ($muzakkiId === null) {
                continue;
            }

            if ($familyId === null) {
                abort(422, "Muzakki ID diberikan tetapi pengguna belum memiliki keluarga.");
            }

            $belongsToFamily = \App\Models\Muzakki::where('id', $muzakkiId)
                ->where('family_id', $familyId)
                ->exists();

            if (!$belongsToFamily) {
                abort(422, "Muzakki pada baris ke-" . ($index + 1) . " bukan anggota keluarga Anda.");
            }
        }
    }
}
