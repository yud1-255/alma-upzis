<?php

namespace App\Http\Controllers;

use App\Domains\ZakatDomain;
use App\Domains\ResidenceDomain;
use App\Exports\ZakatExport;
use App\Exports\TransactionRecapExport;
use App\Exports\MuzakkiListExport;
use App\Exports\MuzakkiRecapExport;
use App\Exports\OnlinePaymentsExport;
use App\Models\AppConfig;
use App\Models\Zakat;
use App\Models\Family;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

use Maatwebsite\Excel\Facades\Excel;

use Auth;

class ZakatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $domain = new ZakatDomain($user);
        $zakats = array();

        $searchTerm = $request->searchTerm ?? "";
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');

        if ($user->can('viewAny', new Zakat())) {
            $zakats = $domain->transactionSummary($searchTerm, $hijriYear, false);
        } else {
            $zakats = $domain->ownTransactionSummary($user);
        }

        return Inertia::render('Zakat/Index', [
            'zakats' => $zakats->paginate(10)->withQueryString(),
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
            'can' => [
                'delete' => $user->can('delete', new Zakat()),
                'confirmPayment' => $user->can('confirmPayment', new Zakat()),
                'viewAny' => $user->can('viewAny', new Zakat())
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $familyId = $request->familyId;
        $isFamilyRequested = $familyId != null && $familyId != $user->family_id;

        $family = $familyId == null ? $user->family : Family::find($familyId);

        if (
            $user->cannot('submitForOthers', new Zakat()) &&
            $isFamilyRequested
        ) {
            abort(403);
        } elseif ($family == null) {
            return Redirect::route("family.create");
        }

        $familyPlaceholder = $familyId == null ? '' : $family->head_of_family;
        $muzakkis = $family->muzakkis->where('is_active', true)->values();

        $domain = new ZakatDomain($user);
        $transactionNo = $domain->generateZakatNumber(false);

        $residenceDomain = new ResidenceDomain();
        $blockNumbers = $residenceDomain->getBlockNumberOptions();
        $houseNumbers = $residenceDomain->getHouseNumbers();

        return Inertia::render('Zakat/Create', [
            'family' => $family,
            'family_placeholder' => $familyPlaceholder,
            'muzakkis' => $muzakkis,
            'transaction_no' => $transactionNo,
            'hijri_year' => AppConfig::getConfigValue('hijri_year'),
            'fitrah_amount' => AppConfig::getConfigValues('fitrah_amount'),
            'is_family_requested' => $isFamilyRequested,
            'blockNumbers' => $blockNumbers,
            'houseNumbers' => $houseNumbers,
            'can' => ['submitForOthers' => $user->can('submitForOthers', new Zakat())]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'family_head' => ['required']
        ]);

        $user = Auth::user();
        $isSubmitAsUpzis = $request->is_submit_as_upzis;

        $zakat = new Zakat();
        $formData = $request->only($zakat->getFillable());
        $zakat->fill($formData);
        $zakatLines = $request['zakat_lines'];

        $domain = new ZakatDomain(Auth::user());

        if ($user->can('submitForOthers', $zakat) && $isSubmitAsUpzis) {
            $domain->submitAsUpzis($zakat, $zakatLines);
        } else {
            $domain->submitAsMuzakki($zakat, $zakatLines);
        }

        return Redirect::route('zakat.show', ['zakat' => $zakat]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Zakat  $zakat
     * @return \Illuminate\Http\Response
     */
    public function show(Zakat $zakat)
    {
        $user = Auth::user();

        if ($user->cannot('view', $zakat)) {
            abort(403);
        }

        $domain = new ZakatDomain(Auth::user());
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
                'confirmPayment' => $user->can('confirmPayment', $zakat)
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Zakat  $zakat
     * @return \Illuminate\Http\Response
     */
    public function edit(Zakat $zakat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Zakat  $zakat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Zakat $zakat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Zakat  $zakat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zakat $zakat)
    {
        if (Auth::user()->cannot('delete', $zakat)) {
            abort(403);
        }

        $domain = new ZakatDomain(Auth::user());
        $domain->voidTransaction($zakat);
        return Redirect::route('zakat.index');
    }

    public function confirmPayment(Request $request)
    {
        $id = $request->route('id');
        $zakat = Zakat::find($id);


        if (Auth::user()->cannot('confirmPayment', $zakat)) {
            abort(403);
        }

        $paymentDate =  Date::createFromFormat('Y-m-d', $request->paymentDate);

        $domain = new ZakatDomain(Auth::user());
        $domain->confirmZakatPayment(Auth::user(), $zakat, $paymentDate);


        return Redirect::to($request->pageUrl);
    }

    public function transactionRecap(Request $request)
    {
        $searchTerm = $request->searchTerm ?? "";
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');

        $domain = new ZakatDomain(Auth::user());
        $zakats = $domain->zakatTransactionRecap($searchTerm, $hijriYear);

        return Inertia::render('Zakat/TransactionRecap', [
            'zakats' => $zakats->orderBy('transaction_no')->paginate(10)->withQueryString(),
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
        ]);
    }

    public function dailyTransactionRecap(Request $request)
    {
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');

        $domain = new ZakatDomain(Auth::user());
        $zakats = $domain->zakatTransactionRecap("", $hijriYear)
            ->orderBy('payment_date', 'asc')
            ->orderBy('transaction_no', 'asc')
            ->get();

        return Inertia::render('Zakat/DailyTransactionRecap', [
            'zakats' => $zakats,
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
        ]);
    }

    public function muzakkiRecap(Request $request)
    {
        $searchTerm = $request->searchTerm ?? "";
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');

        $domain = new ZakatDomain(Auth::user());
        $zakats = $domain->zakatMuzakkiRecap($searchTerm, $hijriYear);

        return Inertia::render('Zakat/MuzakkiRecap', [
            'zakats' => $zakats->paginate(10)->withQueryString(),
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
        ]);
    }

    public function dailyMuzakkiRecap(Request $request)
    {
        $domain = new ZakatDomain(Auth::user());
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');


        $zakats = $domain->zakatMuzakkiRecap("", $hijriYear)
            ->reorder()
            ->orderBy('payment_date', 'asc')
            ->orderBy('transaction_no', 'asc')
            ->get();

        return Inertia::render('Zakat/DailyMuzakkiRecap', [
            'zakats' => $zakats,
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
        ]);
    }

    public function muzakkiList(Request $request)
    {
        $families = Family::with('muzakkis', 'user');

        return Inertia::render('Zakat/MuzakkiList', [
            'families' => $families->paginate(10)
        ]);
    }

    public function onlinePayments(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->searchTerm ?? "";
        $hijriYear = $request->hijriYear ?? AppConfig::getConfigValue('hijri_year');

        $domain = new ZakatDomain(Auth::user());
        $zakats = $domain->zakatOnlinePayments($searchTerm, $hijriYear);

        return Inertia::render('Zakat/OnlinePayments', [
            'zakats' => $zakats->paginate(10)->withQueryString(),
            'can' => ['confirmPayment' => $user->can('confirmPayment', new Zakat())],
            'hijriYears' => $domain->getHijriYears(),
            'hijriYear' => $hijriYear,
        ]);
    }

    public function export(Request $request)
    {
        $type = $request->type;
        $hijriYear = $request->hijriYear;

        switch ($type) {
            case 'summary':
                return Excel::download(new ZakatExport(Auth::user(), $hijriYear), 'zakat.xlsx');
            case 'transaction_recap':
                return Excel::download(new TransactionRecapExport(Auth::user(), $hijriYear), 'transaction_recap.xlsx');
            case 'muzakki_list':
                return Excel::download(new MuzakkiListExport(Auth::user()), 'muzakki_list.xlsx');
            case 'muzakki_recap':
                return Excel::download(new MuzakkiRecapExport(Auth::user(), $hijriYear), 'muzakki_recap.xlsx');
            case 'online_payments':
                return Excel::download(new OnlinePaymentsExport(Auth::user(), $hijriYear), 'online_payments.xlsx');
            default:
                abort(404);
        }
    }
}
