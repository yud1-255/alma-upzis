<?php

namespace App\Http\Controllers;

use App\Domains\ZakatDomain;
use App\Models\Muzakki;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Auth;

class MuzakkiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $muzakki = new Muzakki();
        $formData = $request->only($muzakki->getFillable());
        $muzakki->fill($formData);

        $muzakki->save();

        // return Redirect::route('zakat.create');
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Muzakki  $muzakki
     * @return \Illuminate\Http\Response
     */
    public function show(Muzakki $muzakki)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Muzakki  $muzakki
     * @return \Illuminate\Http\Response
     */
    public function edit(Muzakki $muzakki)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Muzakki  $muzakki
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Muzakki $muzakki)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Muzakki  $muzakki
     * @return \Illuminate\Http\Response
     */
    public function destroy(Muzakki $muzakki)
    {
        $user = Auth::user();
        $domain = new ZakatDomain();

        $domain->deleteMuzakki($user, $muzakki);

        return Redirect::back();
    }
}
