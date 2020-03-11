
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="@lang('sanjab::sanjab.dir')" class="theme-{{ config('sanjab.theme.color') }}">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="Powered By" content="Sanjab - http://sanjabteam.github.io/">
        <title>@if(! empty($__env->yieldContent('title'))) @yield('title') - @endif{{ config('app.name') }}</title>

        <link rel="stylesheet" href="{{ sanjab_mix('css/sanjab.css') }}">

        @yield('header')
    </head>

    <body class="@if(!Route::is('sanjab.unsupported-browser') && time() - Session::get('sanjab_hide_screen_saver') > 300) screen-saver @endif">
        <div class="wrapper">
            @if(!isset($simple) || !$simple)
                <div class="sidebar"
                    data-background-color="black"
                    data-image="@if($sanjabImage){{ $sanjabImage['image'] }}@else{{ 'https://source.unsplash.com/daily' }}@endif">
                    <div class="logo">
                        <a href="{{ url('/') }}" target="_blank" class="simple-text logo-normal">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="screen-saver-content">
                        <div>
                            <h1>@if(time() - Session::get('sanjab_hide_screen_saver') > 7200) @lang('sanjab::sanjab.welcome_back') @endif</h1>
                            @if($sanjabImage && isset($sanjabImage['author']))<a class="image-author" href="{{ isset($sanjabImage['link']) ? $sanjabImage['link'] : '#' }}" title="@lang('sanjab::sanjab.photo_by_:author', ['author' => $sanjabImage['author']])" target="_blank">@lang('sanjab::sanjab.photo_by_:author', ['author' => $sanjabImage['author']])</a>@endif
                        </div>
                    </div>
                    <div class="sidebar-wrapper">
                        <div id="sanjab_navbar_mobile_app">
                            <navbar-search></navbar-search>
                            <navbar-menu :items='@json($sanjabNotificationItems)' :mobile="true"></navbar-menu>
                        </div>
                        <ul class="nav">
                            @foreach($sanjabMenuItems as $menuKey => $menuItem)
                                @if($menuItem->hasChildren())
                                    @if (count($menuItem->getChildren()) > 0)
                                        <li class="nav-item @if($menuItem->isActive()) active @endif">
                                            <a class="nav-link" data-toggle="collapse" href="#sanjabMenuItem{{ $menuKey }}" aria-expanded="{{ $menuItem->isActive() ? 'true' : 'false' }}">
                                                <i class="material-icons">{{ $menuItem->icon }}</i>
                                                <p>
                                                    {{ $menuItem->title }}
                                                    <b class="caret"></b>
                                                </p>
                                            </a>
                                            <div class="collapse @if($menuItem->isActive()) show @endif" id="sanjabMenuItem{{ $menuKey }}">
                                                <ul class="nav">
                                                    @foreach($menuItem->getChildren() as $childMenu)
                                                        <li class="nav-item @if($childMenu->isActive()) active @endif">
                                                            <a class="nav-link" href="{{ $childMenu->url }}">
                                                                <i class="material-icons">{{ $childMenu->icon }}</i>
                                                                <span class="sidebar-normal">{{ $childMenu->title }}</span>
                                                                @if(! empty($childMenu->getBadgeValue()))
                                                                    <span class="badge badge-{{ $childMenu->badgeVariant }}">{{ $childMenu->getBadgeValue() }}</span>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                @else
                                    <li class="nav-item @if($menuItem->isActive()) active @endif">
                                        <a class="nav-link" href="{{  $menuItem->url }}" @if($menuItem->target) target="{{ $menuItem->target }}" @endif>
                                            <i class="material-icons">{{ $menuItem->icon }}</i>
                                            <p>{{ $menuItem->title }}</p>
                                            @if(! empty($menuItem->getBadgeValue()))
                                                <span class="badge badge-{{ $menuItem->badgeVariant }}">{{ $menuItem->getBadgeValue() }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div class="@if(!isset($simple) || !$simple) main-panel @endif">
                @if(!isset($simple) || !$simple)
                    <!-- Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-transparent
                        navbar-absolute fixed-top">
                        <div class="container-fluid">
                            <div class="navbar-wrapper">
                                <a class="navbar-brand">@yield('title')</a>
                            </div>
                            <button class="navbar-toggler" type="button"
                                data-toggle="collapse"
                                aria-controls="navigation-index"
                                aria-expanded="false" aria-label="Toggle
                                navigation">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="navbar-toggler-icon icon-bar"></span>
                                <span class="navbar-toggler-icon icon-bar"></span>
                                <span class="navbar-toggler-icon icon-bar"></span>
                            </button>

                            <div id="sanjab_navbar_app">
                                <b-collapse is-nav>
                                    <navbar-search></navbar-search>
                                    <navbar-menu :items='@json($sanjabNotificationItems)'></navbar-menu>
                                </b-collapse>
                            </div>
                        </div>
                    </nav>
                    <!-- End Navbar -->
                @endif
                <div class="content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
                @if(!isset($simple) || !$simple)
                    <footer class="footer">
                        <div class="container-fluid">
                            <nav class="float-left">
                                <ul>
                                    @if(is_array(config('sanjab.theme.footer_links')))
                                        @foreach(config('sanjab.theme.footer_links') as $footerLink)
                                            @if(is_array($footerLink))
                                                <li>
                                                    <a href="{{ $footerLink['link'] ?? '' }}" target="_blank">
                                                        {{ $footerLink['title'] ?? '' }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </nav>
                            <div class="copyright float-right">
                                {!! config('sanjab.theme.footer_note') !!}
                            </div>
                        </div>
                    </footer>
                @endif
            </div>
        </div>

        <script>
            window.sanjab = {
                config: @json(config('sanjab')),
                app: {
                    locale: @json(App::getLocale())
                },
                serverTimeDiff: parseInt(Date.now()/1000) - {{ now()->timestamp }},
                notificationEventSourceEnabled: @json(Schema::hasTable('notifications') && in_array('Illuminate\Notifications\Notifiable', class_uses(Sanjab::userModel())))
            };
        </script>
        @yield('before_scripts')
        <script src="{{ route('sanjab.helpers.translation.js', ['locale' => App::getLocale()]) }}"></script>
        <script src="{{ sanjab_mix('js/sanjab.js') }}"></script>
        @if(Session::has('sanjab_success'))
            <script>
                $(document).ready(function () {
                    sanjabToast(@json(Session::get('sanjab_success')));
                });
            </script>
        @endif
        @yield('footer')
    </body>

</html>
