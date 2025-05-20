<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ mighty_language_direction() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ \App\Helpers\LogoHelper::getLogo('site_favicon') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- ShadCN Styles -->
    <link rel="stylesheet" href="{{ asset('css/shadcn.css') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}">

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">

    @if(isset($assets) && in_array('datatable', $assets))
        <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    @endif

    @if(isset($assets) && in_array('select2', $assets))
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    @endif

    @if(isset($assets) && in_array('leaflet', $assets))
        <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
    @endif

    @if(mighty_language_direction() == 'rtl')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Page-specific styles -->
    @stack('styles')

    <!-- Alpine.js v3 (Latest) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme initialization -->
    <script>
        // Check for saved theme preference or use OS preference
        const theme = localStorage.getItem('theme') ||
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        // Apply theme class to html element
        document.documentElement.classList.toggle('dark', theme === 'dark');
    </script>
</head>

<body class="min-h-screen bg-background font-sans antialiased">
    <!-- Loading indicator -->
    <div id="loading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm transition-opacity duration-300">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>

    <!-- Page layout -->
    <div class="relative flex min-h-screen flex-col">
        <!-- Header -->
        @include('partials.shadcn._header')

        <!-- Sidebar -->
        @include('partials.shadcn._sidebar')

        <!-- Main content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 ml-0 lg:ml-64 transition-all duration-300">
            <div class="container mx-auto">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        @include('partials.shadcn._footer')
    </div>

    <!-- Remote modal container -->
    <div id="remoteModelData" class="modal fade" role="dialog"></div>

    <!-- Toast container for notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <!-- Core JavaScript -->
    <script src="{{ asset('js/shadcn-bundle.min.js') }}"></script>
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <script src="{{ asset('js/shadcn-components.js') }}"></script>

    <!-- Lazy loading -->
    <script src="{{ asset('js/lozad.min.js') }}"></script>
    <script>
        const observer = lozad('.lozad', {
            loaded: function (el) {
                el.classList.add('loaded');
            }
        });
        observer.observe();
    </script>

    <!-- Additional scripts based on page needs -->
    @if(isset($assets) && in_array('datatable', $assets))
        <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    @endif

    @if(isset($assets) && in_array('chart', $assets))
        <script src="{{ asset('js/chart.min.js') }}"></script>
    @endif

    @if(isset($assets) && in_array('apexchart', $assets))
        <script src="{{ asset('js/apexcharts.min.js') }}"></script>
    @endif

    @if(isset($assets) && in_array('leaflet', $assets))
        <script src="{{ asset('js/leaflet.js') }}"></script>
    @endif

    <!-- Analytics -->
    @if(app()->environment('production'))
        <!-- Hotjar -->
        <script>
            (function (h, o, t, j, a, r) {
                h.hj = h.hj || function () { (h.hj.q = h.hj.q || []).push(arguments) };
                h._hjSettings = { hjid:{{ env('HOTJAR_ID', '') }}, hjsv: 6 };
                a = o.getElementsByTagName('head')[0];
                r = o.createElement('script'); r.async = 1;
                r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                a.appendChild(r);
            })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
        </script>

        <!-- Microsoft Clarity -->
        <script>
            (function (c, l, a, r, i, t, y) {
                c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
                t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "{{ env('CLARITY_ID', '') }}");
        </script>
    @endif

    <!-- Page-specific scripts -->
    @stack('scripts')

    <!-- Initialize page -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hide loading indicator
            document.getElementById('loading').style.opacity = '0';
            setTimeout(function () {
                document.getElementById('loading').style.display = 'none';
            }, 300);

            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(tooltip => {
                const content = tooltip.getAttribute('data-tooltip');
                tooltip.setAttribute('title', content);
            });
        });
    </script>
</body>

</html>