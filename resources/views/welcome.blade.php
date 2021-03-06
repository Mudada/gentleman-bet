@extends('layouts.app')

@section('content')
    <body>
                <div class="welcome-banner" style='background-image: url("img/welcome-banner.jpg"); background-color: #2C3E50; background-size:100%; background-repeat: no-repeat;'>
                <!--<img src="" alt="">-->
            </div>
            <div class="flex-center position-ref full-height">
                <div class="content container">
                    <div class="row welcome-content">
                        <div class="col-xs-12 col-md-6">
                            <div class="gp-container">
                                <div class="gp">
                                    @if(isset($gp))
                                        <a href="{{action('GrandPrixController@show', ['id' => $gp->id])}}">
                                            <img class="gp-img" src="{{ $gp->flag()}}" />
                                            <p>
                                                {{$gp->name}}
                                            </p>
                                        </a>
                                    @else
                                        Aucun Grands Prix en cours
                                    @endif
                                </div>
                            </div>
                            @if (isset($last))
                                    <div class="links">
                                        <a class="yellow"  href="{{action('ResultsController@show', ['gp'=> $last->id])}}">
                                            Dernier Résultats
                                        </a>
                                    </div>
                            @endif
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="links-container">
                                <div>
                                    <div class="links">
                                    @if (Auth::user() && Auth::user()->role == 'admin')
                                    @endif
                                    @if(isset($gp))
                                        <a class="blue" href="{{action('SeasonsController@show', ['id' => $gp->season->id])}}">Calendrier</a>
                                        <a class="red" href="{{action('SeasonsController@showResults', ['id' => $gp->season->id])}}">Classement</a>
                                    @endif
                                        <!--<a class="red" href="#">Classement</a>-->
                                        <a href="{{action('SeasonsController@showAll')}}">Historique</a>
                                        <a style="background-color: #e79441;" href="{{action('PilotesController@show')}}">Pilotes</a>
                                        <a class="green" href="{{url("reglement")}}">Réglement</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
@endsection
