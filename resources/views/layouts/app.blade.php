<!DOCTYPE html>
<html lang="{{$currentLocale}}" default-lang="{{$defaultLocale}}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <link rel="stylesheet" href="{{ elixir('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ elixir('assets/css/site.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    @stack('styles')
    <!--[if lt IE 9]>
        <script src="/assets/js/legacy/html5shiv.min.js"></script>
        <script src="/assets/js/legacy/respond.min.js"></script>
    <![endif]-->
    <link rel='shortcut icon' type='image/x-icon' href='/assets/images/favicon.ico' />
    @include('partials.seo')
    @yield('head')
    @if (!empty(env('GTM_CODE')))
    <!-- Google Tag Manager -->
    <script>
        dataLayer = [{'event': '{{ $userType }}'}];

        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(

                {'gtm.start': new Date().getTime(),event:'gtm.js'}
        );var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ env('GTM_CODE') }}');
    </script>
    <!-- End Google Tag Manager -->
    @endif
</head>
<body data-user-type="{{ $userType }}" class="loading {{ starts_with(\Route::currentRouteName(), 'page.pairs')?'pairs-page':'' }} {{ \Route::currentRouteName()=='homepage'?'index-page':'' }}">{{-- data-spy="scroll" data-target=".list_fixed_menu" data-offset="50"--}}
    @if (!empty(env('GA_CODE')))
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            @if (!$user)
            ga('set', 'dimension-by-user', 'user-anonymous');
            @elseif($user && (!isset($user->ngo) || !$user->ngo))
            ga('set', 'dimension-by-user', 'user-logged');
            @elseif($user && isset($user->ngo) && $user->ngo)
            ga('set', 'dimension-by-user', 'user-logged-ngo');
            @endif

            ga('create', '{{env('GA_CODE')}}', 'auto');
            ga('send', 'pageview');
        </script>
    @endif

    @if (!empty(env('GTM_CODE')))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ env('GTM_CODE') }}"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    @if (!empty(env('GTA_CODE')))
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id={{env('GTA_CODE')}}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{env('GTA_CODE')}}');</script>
    @endif

    <div class="wrapper-main">
        @if(!starts_with(\Route::currentRouteName(), 'page.pairs'))
        <div class="c-header">
            <div class="container">
                <a href="{{ route('page.home') }}" class="c-header__logo"></a>

                <div class="c-header__nav-wrap nav-header inline-layout">
                    <div class="js-menu menu-icon">
                        <span></span>
                    </div>
                    @include('partials.menu', [
                        'menu' => $main_menu,
                        'depth' => 0,
                        'locales' => $locales
                    ])
                    @if (!$user)
                        <div class="login_link">
                            <a href="#" data-formjs="open_login">{{t('user.login')}}</a>
                        </div>
                    @else
                    <div class="dropdown user_login">
                        <button class="dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="name">{{$user->full_name}}</span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @if($user->user->main_edrpou && !trim($user->user->edrpou))
                                <li><a href="{{ route('page.tenders') }}?edrpous={{ trim($user->user->main_edrpou) }}">{{t('user.customer_page')}}</a></li>
                            @elseif($user->user->edrpou)
                                <li><a href="{{ route('page.tenders') }}?edrpous={{ strtr(implode(',', explode("\n", trim($user->user->edrpou))), [' ' => '']) }}">{{t('user.customer_page')}}</a></li>
                            @endif
                            @if(isset($user->ngo) && $user->ngo)
                                <li><a href="{{ route('page.indicators') }}">{{t('user.go_tenders')}}</a></li>
                                <li><a href="{{ route('page.ngo', ['slug' => $user->ngo->slug]) }}">{{t('user.go_page')}}</a></li>
                            @endif
                            @if(isset($user->monitoring) && $user->monitoring && $user->group && $user->group->slug == 'admin')
                                <li><a href="{{ route('page.monitoring', ['slug' => $user->monitoring->slug]) }}">{{t('user.monitoring')}}</a></li>
                            @endif
                            @if(isset($user->superadmin) && $user->superadmin)
                                <li><a href="{{ route('ngo.reviews') }}">{{t('page.ngo.reviews')}}</a></li>
                            @endif
                            <li><a href="{{ route('logout') }}">{{t('user.logout')}}</a></li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @yield('content')

        <div class="last"></div>
    </div>

    @if(!starts_with(\Route::currentRouteName(), 'page.search') &&
        !starts_with(\Route::currentRouteName(), 'page.pairs') &&
        !starts_with(\Route::currentRouteName(), 'page.indicators'))
        @include('partials.footer', [
            'menu' => $bottom_menu
        ])
    @endif

    @if(!$hide_modal)
    <div id="overlay" class="overlay2"></div>
    <div class="modal_div show welcome-modal">

        <div class="modal_close"></div>
        <div class="content-holder">
            <h3>{{t('modal_window.title')}}</h3>
            <div class="desc-modal">
                {{t('modal_window.description')}}
            </div>
            <div class="list_link_item inline-layout">
                <a class="item" href="{{ route('search', ['search' => 'tender']) }}">
                        <div class="image-holder">
                            <img class="fake-img" src="/assets/images/welcome-popup1.png" alt="{{t('modal_window.alt_add_review_to_tender')}}"/>
                            <div class="img" style="background: url(/assets/images/welcome-popup1.png) center no-repeat;background-size: contain;"></div>
                        </div>
                        <p>{{t('modal_window.add_review_to_')}} <i>{{t('modal_window.add_review_to_tender')}}</i></p>
                </a>
                <a class="item" href="{{ route('page.complaints.type') }}">
                    <div class="image-holder">
                        <img class="fake-img" src="/assets/images/welcome-popup2.png" alt="{{t('modal_window.alt_add_review_to_complaint')}}"/>
                        <div class="img" style="background: url(/assets/images/welcome-popup2.png) center no-repeat;background-size: contain;"></div>
                    </div>
                    <p>{{t('modal_window.add_review_to_compl_')}} <i>{{t('modal_window.add_review_to_complaint')}}</i></p>
                </a>
                <a class="item" href="{{t('modal_window.support_url')}}">
                        <div class="image-holder">
                            <img class="fake-img" src="/assets/images/welcome-popup3.png" alt="{{t('modal_window.alt_support')}}"/>
                            <div class="img" style="background: url(/assets/images/welcome-popup3.png) center no-repeat;background-size: contain;"></div>
                        </div>
                        <p>{{t('modal_window.support_')}} <i>{{t('modal_window.support_dozorro')}}</i></p>
                </a>
            </div>
            <div class="desc-welcome">
                <p>
                    {{t('modal_window.read_')}}
                    <a href="{{ route('page.news') }}">{{t('modal_window.read_news')}}</a> {{t('modal_window.and_')}} <a href="{{ route('page.blog') }}">{{t('modal_window.read_blog')}}</a> {{t('modal_window.public_prozorro')}}
                </p>
                <a class="item" href="{{ route('page.tenders') }}">
                    <img src="/assets/images/logo_grey.png" title="DOZORRO">
                </a>
                <p>{{t('modal_window.slogan_text1')}}<br>{{t('modal_window.slogan_text2')}}</p>
            </div>
        </div>

    </div>
    @endif

    @if (!$userTypes->isEmpty())
        <div class="add-review-form" id="user-type-form" data-auth="{{ !empty($user) ? $user->user->id : '' }}" data-formjs="user_type">
            <h3>{{ t('type_specify_title') }}</h3>
            <button class="user-type-form_close"></button>
            <div class="container" style="position:relative;padding: 0px;">
                <div class="add-review-form__content" style="padding: 0;">
                    <div>
                        <ul>
                        @foreach($userTypes as $type)
                        <li>
                            <label class="radio-container" for="user-type-<?php echo $type->id ?>"><?php echo $type->name; ?>
                                <input name="user_type" type="radio" value="<?php echo $type->title; ?>" id="user-type-<?php echo $type->id ?>">
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (!$user)
    <div class="add-review-form" id="review_form2">
        <div style="width:100%;height:100%;position:absolute; z-index:1; top:0px; left:0px;display:none; background-color: rgba(255,255,255,.7)" loader></div>
        <div class="container" style="position:relative">
            <h3 data-profile style="display:none;text-align: center;">{{ t('profile.welcome.login.title') }}</h3>
            <p data-profile style="display:none;text-align: center;">{{ t('profile.welcome.login.description') }}</p>
            <div class="add-review-form__content">
                <div>
                    <a class="btn btn-block btn-social btn-facebook" href="/auth/facebook">
                        <span class="fa fa-facebook"></span> {{t('tender.login.facebook')}}
                    </a>
                    <a class="btn btn-block btn-social btn-google" href="/auth/google">
                        <span class="fa fa-google"></span> {{t('tender.login.google')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('partials.feedback')

    <script src="{{ elixir('assets/js/app.js') }}"></script>

    @if (env('YAMETRIC_CODE'))
        <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter{{env('YAMETRIC_CODE')}} = new Ya.Metrika({ id:{{env('YAMETRIC_CODE')}}, clickmap:true, trackLinks:true, accurateTrackBounce:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/{{env('YAMETRIC_CODE')}}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    @endif

    @stack('scripts')
</body>
</html>
