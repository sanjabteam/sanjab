@extends('sanjab::master')

@section('title', trans('sanjab::sanjab.unsupported_browser'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-sanjab">
                    <h4 class="card-title">@lang('sanjab::sanjab.unsupported_browser')</h4>
                </div>
                <div class="card-body unsupported-browser">
                    <div class="row">
                        <div class="col-sm-12">
                            <h1>{{ Agent::browser() }}</h1>
                            <p class="unsupported-description">@lang('sanjab::sanjab.your_browser_is_unsupported_use_another_one_from_down_below_instead')</p>
                        </div>
                        <div class="row w-100">
                            <div class="col-12 col-sm-4">
                                <img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/58.1.3/archive/chrome_1-11/chrome_1-11.svg" class="card-img-top p-5">
                                <div class="card-body">
                                    <h5 class="card-title">Google Chrome</h5>
                                    <p class="card-text">
                                        <a href="https://www.google.com/chrome" target="_blank" class="btn btn-success btn-block">
                                            @lang('sanjab::sanjab.download')
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/58.1.3/archive/opera_10-14/opera_10-14.svg" class="card-img-top p-5">
                                <div class="card-body">
                                    <h5 class="card-title">Opera</h5>
                                    <p class="card-text">
                                        <a href="https://www.opera.com" target="_blank" class="btn btn-danger btn-block">
                                            @lang('sanjab::sanjab.download')
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/58.1.3/archive/firefox_1.5-3/firefox_1.5-3.svg" class="card-img-top p-5">
                                <div class="card-body">
                                    <h5 class="card-title">Firefox</h5>
                                    <p class="card-text">
                                        <a href="https://www.mozilla.org/en-US/firefox" target="_blank" class="btn btn-warning btn-block">
                                            @lang('sanjab::sanjab.download')
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header')
    <style>
        .unsupported-browser h1 {
            text-align: center;
            text-shadow: rgb(235, 235, 235) 1px 1px 0px, rgb(235, 235, 235) 2px 2px 0px, rgb(235, 235, 235) 3px 3px 0px, rgb(235, 235, 235) 4px 4px 0px, rgb(235, 235, 235) 5px 5px 0px, rgb(236, 236, 236) 6px 6px 0px, rgb(236, 236, 236) 7px 7px 0px, rgb(236, 236, 236) 8px 8px 0px, rgb(236, 236, 236) 9px 9px 0px, rgb(236, 236, 236) 10px 10px 0px, rgb(236, 236, 236) 11px 11px 0px, rgb(237, 237, 237) 12px 12px 0px, rgb(237, 237, 237) 13px 13px 0px, rgb(237, 237, 237) 14px 14px 0px, rgb(237, 237, 237) 15px 15px 0px, rgb(237, 237, 237) 16px 16px 0px, rgb(238, 238, 238) 17px 17px 0px, rgb(238, 238, 238) 18px 18px 0px, rgb(238, 238, 238) 19px 19px 0px, rgb(238, 238, 238) 20px 20px 0px, rgb(238, 238, 238) 21px 21px 0px, rgb(238, 238, 238) 22px 22px 0px, rgb(239, 239, 239) 23px 23px 0px, rgb(239, 239, 239) 24px 24px 0px, rgb(239, 239, 239) 25px 25px 0px, rgb(239, 239, 239) 26px 26px 0px, rgb(239, 239, 239) 27px 27px 0px, rgb(240, 240, 240) 28px 28px 0px, rgb(240, 240, 240) 29px 29px 0px, rgb(240, 240, 240) 30px 30px 0px, rgb(240, 240, 240) 31px 31px 0px, rgb(240, 240, 240) 32px 32px 0px, rgb(240, 240, 240) 33px 33px 0px, rgb(241, 241, 241) 34px 34px 0px, rgb(241, 241, 241) 35px 35px 0px, rgb(241, 241, 241) 36px 36px 0px, rgb(241, 241, 241) 37px 37px 0px, rgb(241, 241, 241) 38px 38px 0px, rgb(242, 242, 242) 39px 39px 0px, rgb(242, 242, 242) 40px 40px 0px, rgb(242, 242, 242) 41px 41px 0px, rgb(242, 242, 242) 42px 42px 0px, rgb(242, 242, 242) 43px 43px 0px, rgb(242, 242, 242) 44px 44px 0px, rgb(243, 243, 243) 45px 45px 0px, rgb(243, 243, 243) 46px 46px 0px, rgb(243, 243, 243) 47px 47px 0px, rgb(243, 243, 243) 48px 48px 0px, rgb(243, 243, 243) 49px 49px 0px, rgb(244, 244, 244) 50px 50px 0px, rgb(244, 244, 244) 51px 51px 0px, rgb(244, 244, 244) 52px 52px 0px, rgb(244, 244, 244) 53px 53px 0px, rgb(244, 244, 244) 54px 54px 0px, rgb(244, 244, 244) 55px 55px 0px, rgb(245, 245, 245) 56px 56px 0px, rgb(245, 245, 245) 57px 57px 0px, rgb(245, 245, 245) 58px 58px 0px, rgb(245, 245, 245) 59px 59px 0px, rgb(245, 245, 245) 60px 60px 0px, rgb(245, 245, 245) 61px 61px 0px, rgb(246, 246, 246) 62px 62px 0px, rgb(246, 246, 246) 63px 63px 0px, rgb(246, 246, 246) 64px 64px 0px, rgb(246, 246, 246) 65px 65px 0px, rgb(246, 246, 246) 66px 66px 0px, rgb(247, 247, 247) 67px 67px 0px, rgb(247, 247, 247) 68px 68px 0px, rgb(247, 247, 247) 69px 69px 0px, rgb(247, 247, 247) 70px 70px 0px, rgb(247, 247, 247) 71px 71px 0px, rgb(247, 247, 247) 72px 72px 0px, rgb(248, 248, 248) 73px 73px 0px, rgb(248, 248, 248) 74px 74px 0px, rgb(248, 248, 248) 75px 75px 0px, rgb(248, 248, 248) 76px 76px 0px, rgb(248, 248, 248) 77px 77px 0px, rgb(249, 249, 249) 78px 78px 0px, rgb(249, 249, 249) 79px 79px 0px, rgb(249, 249, 249) 80px 80px 0px, rgb(249, 249, 249) 81px 81px 0px, rgb(249, 249, 249) 82px 82px 0px, rgb(249, 249, 249) 83px 83px 0px, rgb(250, 250, 250) 84px 84px 0px, rgb(250, 250, 250) 85px 85px 0px, rgb(250, 250, 250) 86px 86px 0px, rgb(250, 250, 250) 87px 87px 0px, rgb(250, 250, 250) 88px 88px 0px, rgb(251, 251, 251) 89px 89px 0px, rgb(251, 251, 251) 90px 90px 0px, rgb(251, 251, 251) 91px 91px 0px, rgb(251, 251, 251) 92px 92px 0px, rgb(251, 251, 251) 93px 93px 0px, rgb(251, 251, 251) 94px 94px 0px, rgb(252, 252, 252) 95px 95px 0px, rgb(252, 252, 252) 96px 96px 0px, rgb(252, 252, 252) 97px 97px 0px, rgb(252, 252, 252) 98px 98px 0px, rgb(252, 252, 252) 99px 99px 0px, rgb(253, 253, 253) 100px 100px 0px, rgb(253, 253, 253) 101px 101px 0px, rgb(253, 253, 253) 102px 102px 0px, rgb(253, 253, 253) 103px 103px 0px, rgb(253, 253, 253) 104px 104px 0px, rgb(253, 253, 253) 105px 105px 0px, rgb(254, 254, 254) 106px 106px 0px, rgb(254, 254, 254) 107px 107px 0px, rgb(254, 254, 254) 108px 108px 0px, rgb(254, 254, 254) 109px 109px 0px, rgb(254, 254, 254) 110px 110px 0px, rgb(255, 255, 255) 111px 111px 0px;
        }

        .unsupported-browser .unsupported-description {
            margin-top: 50px;
            text-align: center;
        }

        .unsupported-browser .card-group {
            padding: 0 20px 0px 20px;
        }

        .unsupported-browser .card-title {
            text-align: center;
        }

        .unsupported-browser .row {
            overflow: hidden;
        }
    </style>
@endsection
