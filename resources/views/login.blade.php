
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="@lang('sanjab::sanjab.dir')" class="theme-{{ config('sanjab.theme.color') }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@lang('sanjab::sanjab.login') | {{ config('app.name') }}</title>
        <link rel="stylesheet" href="{{ sanjab_mix('css/sanjab.css') }}">
        @yield('header')

        <style>
            .page-header:after, .page-header:before {
                display: none;
            }
        </style>
    </head>

    <body class="off-canvas-sidebar">
        <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
            <div class="container">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="wrapper wrapper-full-page">
            <div class="page-header login-page header-filter"
                filter-color="black" style="background-image:
                url('@if($sanjabImage){{ $sanjabImage['image'] }}@else{{ 'https://source.unsplash.com/daily' }}@endif'); background-size:
                cover; background-position: center center;align-items: center;"
                data-color="orange">
                <div class="container" style="height: auto;">
                    <div class="row align-items-center">

                        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
                            <form class="form" method="POST"
                                action="{{ route('sanjab.auth.login') }}">
                                @csrf
                                <div class="card card-login card-hidden mb-3">
                                    <div class="card-header card-header-sanjab text-center">
                                        <h4 class="card-title"><strong>@lang('sanjab::sanjab.login')</strong></h4>
                                        <div class="social-line">
                                            <a href="#pablo" class="btn
                                                btn-just-icon btn-link
                                                btn-white">
                                                <i class="fa
                                                    fa-facebook-square"></i>
                                            </a>
                                            <a href="#pablo" class="btn
                                                btn-just-icon btn-link
                                                btn-white">
                                                <i class="fa fa-twitter"></i>
                                            </a>
                                            <a href="#pablo" class="btn
                                                btn-just-icon btn-link
                                                btn-white">
                                                <i class="fa fa-google-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="bmd-form-group">
                                            <div class="input-group">
                                                <div
                                                    class="input-group-prepend">
                                                    <span
                                                        class="input-group-text">
                                                        <i
                                                            class="material-icons">person</i>
                                                    </span>
                                                </div>
                                                <input type="text" name="{{ config('sanjab.login.username') }}"
                                                    class="form-control"
                                                    placeholder="{{ config('sanjab.login.title') }}"
                                                    value="{{ old(config('sanjab.login.username')) }}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="bmd-form-group mt-3">
                                            <div class="input-group">
                                                <div
                                                    class="input-group-prepend">
                                                    <span
                                                        class="input-group-text">
                                                        <i
                                                            class="material-icons">lock_outline</i>
                                                    </span>
                                                </div>
                                                <input type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control"
                                                    placeholder="@lang('sanjab::sanjab.password')"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="form-check mr-auto ml-3
                                            mt-3">
                                            <label class="form-check-label">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="remember">
                                                    @lang('sanjab::sanjab.remember_me')
                                                <span class="form-check-sign">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>

                                        @if(config('sanjab.login.recaptcha'))
                                            <br>
                                            <div class="form-group">
                                                <div class="g-recaptcha" data-sitekey="{{ config('sanjab.recaptcha.site_key') }}"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer justify-content-center">
                                        <button type="submit" class="btn btn-sanjab btn-link btn-lg">@lang('sanjab::sanjab.login')</button>
                                    </div>
                                </div>
                            </form>
                            @if($sanjabImage && isset($sanjabImage['author']))<a class="image-author" href="{{ isset($sanjabImage['link']) ? $sanjabImage['link'] : '#' }}" title="@lang('sanjab::sanjab.photo_by_:author', ['author' => $sanjabImage['author']])" target="_blank">@lang('sanjab::sanjab.photo_by_:author', ['author' => $sanjabImage['author']])</a>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <script>
            window.sanjab = {
                config: @json(config('sanjab'))
            };
        </script>
        @yield('before_scripts')
        <script src="{{ route('sanjab.helpers.translation.js', ['locale'=> App::getLocale()]) }}"></script>
        <script src="{{ sanjab_mix('js/sanjab.js') }}"></script>
        @if(config('sanjab.login.recaptcha'))
            <script src="https://www.google.com/recaptcha/api.js?hl={{ App::getLocale() }}" async defer></script>
        @endif
        @yield('footer')
    </body>
</html>
