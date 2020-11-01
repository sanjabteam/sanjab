@extends('sanjab::master')

@section('title', trans('sanjab::sanjab.icons'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-sanjab">
                    <h4 class="card-title">@lang('sanjab::sanjab.icons')</h4>
                </div>
                <div class="card-body sanjab-icons-list">
                    <div class="row">
                        @foreach ($icons as $iconConstant => $icon)
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="card bg-secondary m-1">
                                    <div class="card-header text-center">
                                        {{ $icon }}
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center"><i class="material-icons">{{ $icon }}</i></h5>
                                        <p class="text-center"><small class="card-text">MaterialIcons::{{ $iconConstant }}</small></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
