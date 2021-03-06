<?php

namespace App\Http\Controllers;

use App\GrandPrix;
use App\Points;
use App\Result;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ResultsController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth');
    }

    public function bet ($gp_id, $user_id) {
        $gp = GrandPrix::findOrFail($gp_id);
        $date = new Carbon($gp->date);
        $lastDay = false;

        if ($gp->betTime()->isToday())
            $lastDay = true;

        $date = $date->format('Y/m/d');
        $gps = $gp->season->gp->sortBy('date');
        $result = $gp->results()->where('user_id', $user_id)->first();

        if (!$result)
            $result = new Result();
        elseif (Input::get('type') == 'result') {
            $resultat = $gp->results()->where('type', 'result')->first();
            if ($resultat)
                return view('grand_prixs/show')->withGp($gp)->withResultErrors('Vous avez deja annonce des resultats')->withInput(Input::all())->withDate($date)->withLast($lastDay);
            else
                $result = new Result();
        }

        $user = User::findOrFail($user_id);
        $result->score = 0;
        $result->type = Input::get('type');
        $result->pole = Input::get('pole');
        $result->position1 = Input::get('position1');
        $result->position2 = Input::get('position2');
        $result->position3 = Input::get('position3');
        $result->position4 = Input::get('position4');
        $result->position5 = Input::get('position5');
        $result->position6 = Input::get('position6');
        $result->position7 = Input::get('position7');
        $result->position8 = Input::get('position8');
        $result->position9 = Input::get('position9');
        $result->position10 = Input::get('position10');
        $result->user()->associate($user);
        $result->gp()->associate($gp);
        $result->save();

        foreach ($gps as $index => $gpd) {
            if ($gpd->id == $gp_id)
                $pos = $index;
        }
        if (Input::get('type') != 'result') {
            for ($i = $pos; $i < count($gps); $i++) {
                $res = $gps[$i]->results()->where('type', 'bet')->where('user_id', $user_id)->first();
                if (!$res)
                    $res = new Result();
                $res->score = 0;
                $res->type = $result->type;
                $res->pole = $result->pole;
                $res->position1 = $result->position1;
                $res->position2 = $result->position2;
                $res->position3 = $result->position3;
                $res->position4 = $result->position4;
                $res->position5 = $result->position5;
                $res->position6 = $result->position6;
                $res->position7 = $result->position7;
                $res->position8 = $result->position8;
                $res->position9 = $result->position9;
                $res->position10 = $result->position10;
                $res->user()->associate($user);
                $res->gp()->associate($gps[$i]);
                $res->save();
            }
        }

        if (Input::get('type') === "result")
            $this->_calculate_points($gp);
        return view('grand_prixs/show')->withGp($gp)->withValidation('Pari pris en compte !')->withInput(Input::all())->withDate($date)->withLast($lastDay);
    }

    private function _calculate_position ($result, $bet, $position)
    {
        if ($result->{'position' . $position} == $bet->{'position' . $position})
            return 25;
        else {
            for ($i = 1; $i < 11 && $bet->{'position'.$i} != $result->{'position'.$position}; $i++);

            if ($i > 10)
                return 0;

            $abs = abs($position - $i);

            return 20 - (2 * $abs);
        }
    }

    private function _calculate_points($gp) {

        $bets = $gp->results;
        $result = $gp->results->where('type', 'result')->first();

        foreach ($bets as $bet) {
            $point = new Points();
            $total = 0;
            for ($i = 1; $i < 11; $i++)
                $point->{'position' . $i} = $this->_calculate_position($result, $bet, $i);
            if ($result->pole == $bet->pole)
                $point->pole = 20;
            else
                $point->pole = 0;
            if ($bet->position1 == $result->position1) {
                if ($bet->position2 == $result->position2) {
                    if ($bet->position3 == $result->position3) {
                        $point->podium = 100;
                    }
                    else
                        $point->duo = 60;
                }
                elseif ($bet->position2 == $result->position3) {
                    if ($bet->position3 == $result->position2)
                        $point->diumpo = 50;
                }
                else
                    $point->vainq = 20;
            }
            elseif ($bet->position2 == $result->position2) {
                if ($bet->position1 == $result->position3)
                    if ($bet->position3 == $result->position1)
                        $point->diumpo = 60;
            }
            elseif ($bet->position3 == $result->position3) {
                if ($bet->position2 == $result->position1)
                    if ($bet->position1 == $result->position2)
                        $point->diumpo = 50;
            }
            elseif ($bet->position1 == $result->position2 && $bet->position2 == $result->position3 && $bet->position3 == $result->position1)
                $point->diumpo = 50;
            elseif ($bet->position2 == $result->position1 && $bet->position3 == $result->position2 && $bet->position1 == $result->position3)
                $point->diumpo = 50;
            elseif ($bet->position2 == $result->position1)
                if ($bet->position1 == $result->position2)
                    $point->udo = 30;
            $point->total = $point->pole + $point->podium + $point->diumpo + $point->duo + $point->udo + $point->vainq
                            + $point->position1
                            + $point->position2
                            + $point->position3
                            + $point->position4
                            + $point->position5
                            + $point->position6
                            + $point->position7
                            + $point->position8
                            + $point->position9
                            + $point->position10;
            $bet->point()->save($point);
        }
     }

    public function show ($gp_id)
    {
        $gp = GrandPrix::findOrFail($gp_id);
        $bets = collect();
        foreach ($gp->results as $res) {
            if (isset($res->point))
                $bets = $gp->results
                    ->sortByDesc(function ($elem) {return ($elem->point->total);})
                    ->sortByDesc(function ($elem) {return ($elem->type);});
            else
                $bets = $gp->results;
        }



        $result = $result = $gp->results->where('type', 'result')->first();

        $pilotes = $gp->pilotes;
        $id_pilotes = array(array());
        foreach ($pilotes as $pilote) {
            $id_pilotes[$pilote->id][0] = $pilote->acronym;
            for ($i = 1; $i < 11; $i++) {
                if (isset($result) && $result->{"position" . $i} == $pilote->id)
                    $id_pilotes[$pilote->id][1] = "position$i";
            }
        }
        return view('results/show')->withBets($bets)->withGp($gp)->withIdPilotes($id_pilotes)->withResult($result);
    }
}
