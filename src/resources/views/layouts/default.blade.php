<!DOCTYPE html>
<html lang="en" class="full-height" data-bs-theme="{{ Colors::getTheme() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {!! SEOMeta::generate() !!}

    <link rel="icon" type="image/png" sizes="32x32" href="{{ Settings::getOrgFavicon() }}">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700&display=swap' rel='stylesheet'
        type='text/css' />
    <link href="/css/app.css?v={{ Helpers::getCssVersion() }}" rel=stylesheet />

    {!! OpenGraph::generate() !!}

    <title>
        @hasSection ('page_title')
            @yield ('page_title') | {{ Settings::getOrgName() }}
        @else
            {{ Settings::getOrgTagline() }} | {{ Settings::getOrgName() }}
        @endif
    </title>


    @yield ('prefetch')
</head>

<body class="full-height">

    @include ('layouts._partials.navigation')

    <div class="container" style="margin-top:50px;"></div>

    @yield ('content')
    <div class="alert-fixed">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if (Session::has('alert-' . $msg))
                <p class="alert  alert-{{ $msg }} alert-dismissible fade show">
                    <b>{{ Session::get('alert-' . $msg) }}</b> <a href="#" class="btn-close text-decoration-none"
                        data-bs-dismiss="alert" aria-label="close"></a>
                </p>
            @endif
        @endforeach
    </div>

    <script src="/js/vendor.js"></script>
    <script>
        jQuery(function() {
            jQuery('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    <br>
    <div class="stats  section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-4  text-center">
                    <div class="stats-number">
                        {{ Helpers::getEventTotal() }}
                    </div>
                    <hr />
                    <div class="stats-title">
                        @lang('layouts.events_weve_hosted')
                    </div>
                </div>

                <div class="col-md-4  text-center">
                    <div class="stats-number">
                        {{ Helpers::getEventParticipantTotal() }}
                    </div>
                    <hr />
                    <div class="stats-title">
                        @lang('layouts.players_weve_entertained')
                    </div>
                </div>

                <div class="col-md-4  text-center">
                    <div class="stats-number">
                        @lang('layouts.a_lot')
                    </div>
                    <hr />
                    <div class="stats-title">
                        {{ Settings::getFrontpageAlotTagline() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="d-none d-md-block">
                    <br><br>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <picture>
                        <source srcset="{{ Settings::getOrgLogo() }}.webp" type="image/webp">
                        <source srcset="{{ Settings::getOrgLogo() }}" type="image/jpeg">
                        <img class="img-fluid" src="{{ Settings::getOrgLogo() }}">
                    </picture>
                </div>
                <div class="col-lg-8 col-sm-12 col-md-12 text-center">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <h2>@lang('layouts.default_links')</h2>
                            <p class="d-none"><a href="/contact">@lang('layouts.default_contact_us')</a></p>
                            <p><a href="/news">@lang('layouts.default_news')</a></p>
                            <p><a href="/terms">@lang('layouts.default_terms_and_conditions')</a></p>
                            <p><a href="/legalnotice">@lang('layouts.default_legal_and_privacy')</a></p>
                            <p><a href="/about">@lang('layouts.default_about_us')</a></p>
                            <p><a href="/polls">@lang('layouts.default_polls')</a></p>
                            <p class="d-none">@lang('layouts.default_lan_guide')</p>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <h2>Connect</h2>
                            @if (Settings::getFacebookLink() != '')
                                <p><a target="_blank" rel="noreferrer"
                                        href="{{ Settings::getFacebookLink() }}">@lang('layouts.default_facebook')</a></p>
                            @endif
                            @if (Settings::getDiscordLink() != '')
                                <p><a target="_blank" rel="noreferrer"
                                        href="{{ Settings::getDiscordLink() }}">@lang('layouts.default_discord')</a></p>
                            @endif
                            @if (Settings::getSteamLink() != '')
                                <p><a target="_blank" rel="noreferrer"
                                        href="{{ Settings::getSteamLink() }}">@lang('layouts.default_steam')</a></p>
                            @endif
                            @if (Settings::getTwitterLink() != '')
                                <p><a target="_blank" rel="noreferrer"
                                        href="{{ Settings::getTwitterLink() }}">@lang('layouts.default_twitter')</a></p>
                            @endif
                            @if (Settings::getRedditLink() != '')
                                <p><a target="_blank" rel="noreferrer"
                                        href="{{ Settings::getRedditLink() }}">@lang('layouts.default_reddit')</a></p>
                            @endif
                        </div>
                        <div class="col-lg-12">
                            <p>© {{ Settings::getOrgName() }} {{ date('Y') }}. @lang('layouts.default_rights_reserved')</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <p>Powered By <a href="https://github.com/Lan2Play/eventula-manager">Eventula Event Manager</a></p>
                </div>
            </div>
        </div>
    </footer>
    @if ($errors->any())
        <div class="alert alert-fixed alert-danger alert-dismissible fade show" role="alert">
            <h4 mt-0>Errors occured</h4>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>
            <button type="button" class="btn-close text-decoration-none" data-bs-dismiss="alert" aria-label="Close">

            </button>
        </div>
    @endif

    @yield ('scripts')
</body>

</html>
