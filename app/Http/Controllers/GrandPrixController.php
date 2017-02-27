<?php

namespace App\Http\Controllers;

use App\Result;
use Carbon\Carbon;
use App\GrandPrix;
use App\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class GrandPrixController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth');
    }

    public function show ($id) {

        $gp = GrandPrix::findOrFail($id);
        $result = $gp->results()->where('user_id', \Auth::user()->id)->where('type', '!=', 'result')->first();
        $input["pole"] = $result->pole;
        for ($i = 1; $i <= 10; $i++)
            $input["position" . $i] = (string)$result->{'position' . $i};
        return view('grand_prixs/show')->withGp($gp)->withInput($input);
    }

    public function create () {

        return view('/grand_prixs/create');

    }

    public function store (Request $request) {

        $this->validate($request, [
            'name'      => 'required | string',
            'avatar'    => 'required',
            'info1'     => 'required | string',
            'info2'     => 'required | string',
            'info3'     => 'required | string',
            'info4'     => 'required | string',
            'date'      => 'required'
        ]);

        $season = Season::findOrFail(Input::get('season'));

        $gp = new GrandPrix();
        $gp->name = Input::get('name');
        $gp->avatar = Input::file('avatar');
        $gp->date = Input::get('date');
        $gp->info1 = Input::get('info1');
        $gp->info2 = Input::get('info2');
        $gp->info3 = Input::get('info3');
        $gp->info4 = Input::get('info4');
        $gp->season()->associate($season);
        $gp->save();
        return view('/grand_prixs/create')->withGp($gp->id)->withSeason(Input::get('season'));
    }
}
