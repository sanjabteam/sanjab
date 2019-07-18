@extends('sanjab::master')

@section('title', $properties->titles)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_app">
                <material-card title="{{ $properties->titles }}" description="{{ $properties->description }}">
                    <widgets-list :widgets='@json($widgets)' :actions='@json($actions)' :properties='@json($properties)' />
                </material-card>
            </div>
        </div>
    </div>
@endsection
