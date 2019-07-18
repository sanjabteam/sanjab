@extends('sanjab::master')

@section('title', isset($item) ? trans('sanjab::sanjab.edit_:item', ['item' => $properties->title]) : trans('sanjab::sanjab.create_:item', ['item' => $properties->title]))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_app">
                <material-card title="{{ isset($item) ? trans('sanjab::sanjab.edit_:item', ['item' => $properties->title]) : trans('sanjab::sanjab.create_:item', ['item' => $properties->title]) }}" description="{{ $properties->description }}">
                    <widgets-form :widgets='@json($widgets)' :properties='@json($properties)' @isset($item) :item='@json($item)' @endisset />
                </material-card>
            </div>
        </div>
    </div>
@endsection
