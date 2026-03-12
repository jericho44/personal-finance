<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Landing Page</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/extends/images/logo.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/extends/css/ewp.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/extends/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    @vite('resources/css/app.css')
</head>


<script>
    var apiUrl = "{{ url('/api/') }}";
    var apiWebUrl = "{{ url('/api-web/') }}";
    var baseUrl = "{{ url('/') }}";
    var assetUrl = "{{ asset('/assets/') }}/";
</script>

<body id="kt_body"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
    style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
    <div id="app">
        <main-app></main-app>
    </div>

    <script>
        function reinitializeAllPlugin() {
            $(".drawer-overlay").remove();
            setTimeout(() => {
                KTDialer.init();
                KTDrawer.init();
                KTImageInput.init();
                KTMenu.createInstances()
                KTPasswordMeter.init();
                KTScroll.init();
                KTScrolltop.init();
                KTSticky.init();
                KTSwapper.init();
                KTToggle.init();
                KTUtil.onDOMContentLoaded((function() {
                    KTApp.init()
                })), window.addEventListener("load", (function() {
                    KTApp.initPageLoader()
                })), "undefined" != typeof module && void 0 !== module.exports && (module.exports = KTApp);

                KTUtil.onDOMContentLoaded((function() {
                    KTLayoutAside.init()
                }));


                KTUtil.onDOMContentLoaded((function() {
                    KTLayoutSearch.init()
                }));

                KTUtil.onDOMContentLoaded((function() {
                    KTLayoutToolbar.init()
                }));

            }, 100);

            setTimeout(() => {
                $('body').attr('data-kt-drawer-aside', 'off');
                $('body').attr('data-kt-drawer', 'off');
                $('body').attr('data-kt-aside-minimize', 'off');

                $(".drawer-overlay").remove();
            }, 10);


            $("#kt_aside_mobile_toggle").on('click', function() {
                setTimeout(() => {
                    $('.drawer-overlay').each(function() {
                        let checkLength = $(".drawer-overlay").length;

                        if (checkLength > 1) {
                            $(this).remove();
                        }

                    });
                }, 10);
            });

        }


        function reinitializeKTMenuPlugin() {
            KTMenu.createInstances()
        }
    </script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @vite('resources/js/app.ts')
</body>

</html>
