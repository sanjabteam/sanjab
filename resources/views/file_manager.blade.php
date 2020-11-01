@extends('sanjab::master', ['simple' => $popup])

@section('title', trans('sanjab::sanjab.file_manager'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_elfinder">
            </div>
        </div>
    </div>
@endsection

@section('header')
    <link rel="stylesheet" href="/vendor/sanjab/thirdparty/elfinder/css/elfinder.min.css" />
    <link rel="stylesheet" href="/vendor/sanjab/thirdparty/elfinder/css/theme-light.min.css" />
    <style>
        @if ($popup)
        .ui-resizable-handle {
            display: none !important;
        }
        @endif
    </style>
@endsection

@section('footer')
    <script src="/vendor/sanjab/thirdparty/elfinder/js/elfinder.min.js"></script>
    <script src="/vendor/sanjab/thirdparty/elfinder/js/extras/editors.default.min.js"></script>
    <script src="/vendor/sanjab/thirdparty/elfinder/js/extras/quicklook.googledocs.min.js"></script>

    @if (file_exists(public_path("vendor/sanjab/thirdparty/elfinder/js/i18n/elfinder.".App::getLocale().".js")))
        <script src="/vendor/sanjab/thirdparty/elfinder/js/i18n/elfinder.{{ App::getLocale() }}.js"></script>
    @endif
    <script>
        $(document).ready(function(){
            $('#sanjab_elfinder').elfinder({
                cssAutoLoad : false,
                url  : @json(route('sanjab.file_manager.connector').(request('disk') ? '?disks[]='.request('disk'):'')),
                customHeaders: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                @if (file_exists(public_path("vendor/sanjab/thirdparty/elfinder/js/i18n/elfinder.".App::getLocale().".js")))
                    lang : @json(App::getLocale()),
                @endif
                @if ($popup)
                    @if (request('multiple') == 'true')
                        commandsOptions: {
                            getfile: { multiple: true }
                        },
                    @endif
                    getFileCallback: function(files) {
                        if (! Array.isArray(files)) {
                            files = [files];
                        }
                        @if (request('max'))
                            if (files.length > {{ request('max') }}) {
                                sanjabError(sanjabTrans('the_maximum_number_of_files_is_:count', {'count': @json(request('max'))}));
                                return;
                            }
                        @endif
                        for (var i in files) {
                            @if (request('maxsize'))
                                if (parseInt(parseInt(files[i].size)/1000) + 1 > {{ request('maxsize') }}) {
                                    sanjabError(sanjabTrans('the_maximum_size_of_files_is_:size', {'size': @json(request('maxsize'))}));
                                    return;
                                }
                            @endif
                        }
                        window.postMessage({type: 'sanjab-elfinder-file-selected', files: files}, '*');
                        window.addEventListener("message", function (e) {
                            if (typeof e.data === 'object' && e.data.type == 'sanjab-elfinder-file-selected' && typeof e.data.files == 'object' ) {
                                setTimeout(function () {
                                    parent.window.close();
                                    window.close();
                                }, 200);
                            }
                        });
                    }
                @endif
            });
        });

    </script>
@endsection
