@extends('sanjab::master')

@section('title', $properties->title)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_app">
                <material-card title="{{ $properties->title }}" description="{{ $properties->description }}">
                    <cards-list :cards='@json($cards)' :data='@json($cardsData)' />
                </material-card>
            </div>
        </div>
    </div>
@endsection

