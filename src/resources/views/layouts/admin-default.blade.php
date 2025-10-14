<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ Colors::getTheme() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {!! SEOMeta::generate() !!}
    <title>
        @hasSection ('page_title')
            @yield('page_title') | {{ Settings::getOrgName() }}
        @else
            {{ Settings::getOrgName() }} Admin
        @endif
    </title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Settings::getOrgFavicon() }}">

		<!-- Admin CSS -->
		<link href="/css/admin.css" rel="stylesheet">
		<link href="/css/jquery-ui.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
				<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Jquery Core JavaScript -->
		<script src="/js/vendor.js"></script>

    <script>
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false;
        }

        function ConfirmSubmit() {
            var x = confirm("Are you sure you want to submit?");
            if (x)
                return true;
            else
                return false;
        }
        jQuery(document).ready(function() {
            jQuery('.wysiwyg-editor').summernote({
                height: 300,
            });
            jQuery('.wysiwyg-editor-small').summernote({
                height: 150,
            });
        });

        // Auto-hide/show navbar on scroll (mobile only)
        document.addEventListener('DOMContentLoaded', function() {
            let lastScrollTop = 0;
            const navbar = document.querySelector('.navbar');
            const delta = 5;
            const navbarHeight = navbar.offsetHeight;

            window.addEventListener('scroll', function() {
                // Only apply auto-hide on mobile (screen width < 768px)
                if (window.innerWidth >= 768) {
                    navbar.style.transform = 'translateY(0)';
                    return;
                }

                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (Math.abs(lastScrollTop - scrollTop) <= delta) {
                    return;
                }
                
                if (scrollTop > lastScrollTop && scrollTop > navbarHeight) {
                    // Scrolling down
                    navbar.style.transform = 'translateY(-100%)';
                    navbar.style.transition = 'transform 0.3s ease-in-out';
                } else {
                    // Scrolling up
                    navbar.style.transform = 'translateY(0)';
                    navbar.style.transition = 'transform 0.3s ease-in-out';
                }
                
                lastScrollTop = scrollTop;
            });
        });
    </script>

</head>

<body>
    <!-- <div id="wrapper"> -->
    @include ('layouts._partials._admin.topnavigation')
    <div class="container-fluid">
        <div class="row">

            @include ('layouts._partials._admin.sidenavigation')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">


                @yield('content')

                <div class="alert-fixed">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if (Session::has('alert-' . $msg))
                            <p class="alert  alert-{{ $msg }} alert-dismissible fade show">
                                <b>{{ Session::get('alert-' . $msg) }}</b> <a href="#"
                                    class="btn-close text-decoration-none" data-bs-dismiss="alert"
                                    aria-label="close"></a>
                            </p>
                        @endif
                    @endforeach
                </div>
                @if ($errors->any())
                    <div class="alert alert-fixed alert-danger alert-dismissible fade show" role="alert">
                        <h4 mt-0>Errors occured</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>
                        <button type="button" class="btn-close text-decoration-none" data-bs-dismiss="alert"
                            aria-label="Close">

                        </button>
                    </div>
                @endif
            </main>
        </div>
    </div>
    <!-- </div> -->
</body>

</html>
