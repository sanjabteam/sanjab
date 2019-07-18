@extends('sanjab::master')

@section('title', trans('sanjab::sanjab.show_:item', ['item' => $properties->title]))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_app">
                <material-card title="{{ trans('sanjab::sanjab.show_:item', ['item' => $properties->title]) }}" description="{{ $properties->description }}">
                    <widgets-form :readonly="true" :widgets='@json($widgets)' :properties='@json($properties)' @isset($item) :item='@json($item)' @endisset />
                </material-card>
            </div>
        </div>
    </div>
@endsection
