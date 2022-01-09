<?php

namespace App\Http\Controllers;

use App\Domains\ZakatDomain;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use Auth;

class ZakatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $domain = new ZakatDomain();
        $zakats = $domain->transactionSummaryList()->paginate(10);

        return Inertia::render('Zakat/Index', [
            'zakats' => $zakats,
            'can' => [
                'confirmPayment' => $user->can('confirmPayment', new Zakat())
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $family = $user->family;
        $muzakkis = $user->family->muzakkis;

        $domain = new ZakatDomain();
        $transaction_no = $domain->generateZakatNumber(false);

        return Inertia::render('Zakat/Create', [
            'family' => $family, 'muzakkis' => $muzakkis,
            'transaction_no' => $transaction_no
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
            'total_rp' => ['numeric'],
            'family_head' => ['required']
        ]);
        $zakat = new Zakat();
        $formData = $request->only($zakat->getFillable());
        $zakat->fill($formData);
        $zakatLines = $request['zakat_lines'];

        $domain = new ZakatDomain();
        $domain->submitAsMuzakki(Auth::user(), $zakat, $zakatLines);

        return Redirect::route('zakat.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Zakat  $zakat
     * @return \Illuminate\Http\Response
     */
    public function show(Zakat $zakat)
    {
        if (Auth::user()->cannot('view', $zakat)) {
            abort(403);
        }
        $zakatTx = Zakat::with('zakatLines', 'receiveFrom', 'zakatPIC', 'zakatLines.muzakki')->where('id', $zakat->id)->first();

        return Inertia::render('Zakat/Show', ['zakat' => $zakatTx]);
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
        $domain = new ZakatDomain();
        $domain->deleteTransaction($zakat);
        return Redirect::route('zakat.index');
    }

    public function confirmPayment(Request $request)
    {
        // TODO implement confirm payment logic (use domain)
        $id = $request->route('id');
        dd($id);
        return Redirect::route('zakat.index');
    }
}
