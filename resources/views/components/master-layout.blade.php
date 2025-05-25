<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ mighty_language_direction() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('partials._head')

    <!-- Vite Assets -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])

    <!-- jQuery (Load first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Alpine.js v3 (Latest) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js is loaded and ready!');
        });

        // Wait for jQuery to be available
        document.addEventListener('DOMContentLoaded', function () {
            // Check if jQuery is loaded
            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            console.log('jQuery ready, initializing navbar...');

            // Check if elements exist
            console.log('Collapse triggers found:', $('[data-toggle="collapse"]').length);
            console.log('Submenu elements found:', $('.submenu.collapse').length);
            console.log('Navbar container found:', $('#navbarSupportedContent').length);

            // Remove transitions only from sidebar dropdown menus (not all icons)
            $('.mm-sidebar-menu .side-menu .submenu, .mm-sidebar-menu .side-menu .submenu li, .mm-sidebar-menu .side-menu .submenu li a').css({
                'transition': 'none !important',
                'animation': 'none !important',
                'transform': 'none !important'
            });

            // Force immediate layout for any already open menus
            $('.submenu.collapse.show').each(function () {
                var $menu = $(this);
                $menu.css({
                    'display': 'block !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important',
                    'height': 'auto !important',
                    'transition': 'none !important'
                });

                $menu.find('li').css({
                    'display': 'list-item !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important',
                    'transition': 'none !important'
                });

                $menu.find('li a').css({
                    'display': 'block !important',
                    'padding': '8px 15px 8px 40px !important',
                    'transition': 'none !important'
                });
            });

            // Disable any MetisMenu initialization that might interfere
            if (typeof $.fn.metisMenu !== 'undefined') {
                console.log('MetisMenu detected, disabling...');
                // Prevent MetisMenu from being initialized
                $.fn.metisMenu = function() { return this; };
            }

            // Handle collapse clicks with jQuery - High priority event handler
            $(document).off('click', '[data-toggle="collapse"]'); // Remove any existing handlers
            $(document).on('click.customCollapse', '[data-toggle="collapse"]', function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                var $this = $(this);
                var target = $this.attr('data-target');
                var $target = $(target);

                console.log('Custom Collapse Handler - Clicked:', $this.text().trim());
                console.log('Custom Collapse Handler - Target:', target);
                console.log('Custom Collapse Handler - Target element found:', $target.length > 0);
                console.log('Custom Collapse Handler - Target children:', $target.children().length);

                if ($target.length) {
                    // Check if this menu is currently open
                    var isCurrentlyOpen = $target.hasClass('show');
                    console.log('Custom Collapse Handler - Is currently open:', isCurrentlyOpen);

                    // Close ALL open menus first (including the current one)
                    $('.submenu.collapse.show').removeClass('show')
                        .each(function () {
                            $('[data-target="#' + this.id + '"]').attr('aria-expanded', 'false');
                        });

                    // If the clicked menu was NOT open, then open it
                    if (!isCurrentlyOpen) {
                        // Remove any existing transitions temporarily
                        $target.css('transition', 'none');
                        $target.find('*').css('transition', 'none');

                        $target.addClass('show');
                        $this.attr('aria-expanded', 'true');

                        // Force immediate visibility with the new layout
                        $target.css({
                            'display': 'block !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important',
                            'height': 'auto !important',
                            'overflow': 'visible !important',
                            'transform': 'none !important',
                            'transition': 'none !important'
                        });

                        // Force children to be visible immediately in new layout
                        $target.find('li').css({
                            'display': 'list-item !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important',
                            'height': 'auto !important',
                            'transform': 'none !important',
                            'transition': 'none !important'
                        });

                        $target.find('li a').css({
                            'display': 'block !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important',
                            'height': 'auto !important',
                            'padding': '8px 15px 8px 40px !important',
                            'transform': 'none !important',
                            'transition': 'none !important'
                        });

                        console.log('Custom Collapse Handler - Menu opened instantly');
                    } else {
                        console.log('Custom Collapse Handler - Menu closed (was already open)');
                    }
                } else {
                    console.log('Custom Collapse Handler - Target element not found!');
                }

                return false; // Prevent any other handlers from running
            });
        });
    </script>

    <!-- Fix for oversized SVG icons -->
    <style>
        svg {
            max-width: 24px !important;
            max-height: 24px !important;
        }

        /* Exception for logos and other specific SVGs */
        .logo svg,
        .brand-logo svg,
        .chart svg,
        .map svg {
            max-width: none !important;
            max-height: none !important;
        }

        /* Alpine.js navbar enhancements */
        .mm-arrow-right {
            transition: transform 0.3s ease-in-out;
        }

        .mm-arrow-right.rotate-90 {
            transform: rotate(90deg);
        }

        .mm-arrow-right.rotate-180 {
            transform: rotate(180deg);
        }

        /* Force submenu visibility when shown - instant, no transitions */
        .submenu.collapse.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            position: relative !important;
            transform: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            transition: none !important;
            animation: none !important;
        }

        /* Remove transitions only from sidebar dropdown menus */
        .mm-sidebar-menu .side-menu .submenu,
        .mm-sidebar-menu .side-menu .submenu li,
        .mm-sidebar-menu .side-menu .submenu li a {
            transition: none !important;
            animation: none !important;
            transform: none !important;
        }

        .submenu.collapse.show li {
            display: list-item !important;
            opacity: 1 !important;
            visibility: visible !important;
            height: auto !important;
            line-height: normal !important;
            margin: 0 !important;
            padding: 0 !important;
            position: relative !important;
            transform: none !important;
        }

        .submenu.collapse.show li a {
            display: block !important;
            padding: 8px 15px 8px 40px !important;
            color: #6c757d !important;
            text-decoration: none !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            height: auto !important;
            min-height: 35px !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            transform: none !important;
            background: transparent !important;
            border: none !important;
            transition: none !important;
            animation: none !important;
        }

        /* Ensure immediate layout application */
        .submenu.collapse.show li a:before,
        .submenu.collapse.show li a:after {
            transition: none !important;
            animation: none !important;
        }

        /* Override any CSS transitions from SCSS files */
        .mm-sidebar-menu .side-menu .submenu {
            transition: none !important;
            animation: none !important;
        }

        /* Remove transitions only from sidebar dropdown arrows */
        .mm-sidebar-menu .side-menu .submenu li a .mm-arrow-right {
            transition: none !important;
            animation: none !important;
        }

        /* Force immediate visibility for all sidebar elements */
        .mm-sidebar-menu .side-menu li.active .submenu.collapse.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transition: none !important;
            animation: none !important;
        }

        .submenu.collapse.show li a:hover {
            background-color: rgba(0, 123, 255, 0.1) !important;
            color: #007bff !important;
        }

        .submenu.collapse:not(.show) {
            display: none !important;
        }

        /* Navbar navigation container - always visible */
        .navbar-nav-container {
            display: flex !important;
            align-items: center !important;
            margin-left: auto !important;
        }

        .navbar-nav.navbar-list {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        .navbar-nav.navbar-list li {
            display: flex !important;
            align-items: center !important;
            margin: 0 5px !important;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .navbar-nav.navbar-list li {
                margin: 0 2px !important;
            }

            .navbar-nav.navbar-list .nav-link {
                padding: 0.5rem 0.3rem !important;
            }
        }

        /* Hide navbar toggler since we don't need it */
        .navbar-toggler {
            display: none !important;
        }

        /* Override any conflicting styles */
        .mm-sidebar-menu .side-menu .submenu.collapse.show {
            display: block !important;
            height: auto !important;
        }

        .mm-sidebar-menu .side-menu .submenu.collapse.show li {
            display: list-item !important;
            height: auto !important;
        }
    </style>

</head>

<body class="" id="app">

    @include('partials._body')

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</html>