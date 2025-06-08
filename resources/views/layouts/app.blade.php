<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AVSEC App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    @auth
        @include('layouts.navbar')

        <!-- Mobile Sidebar Overlay -->
        <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

        <div class="flex h-full"></div>
            @include('layouts.sidebar')
            <main class="flex-1 min-h-screen pt-16 px-4 lg:pt-0 lg:ml-64 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <main class="py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    @endauth

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');
            const menuClosedIcon = document.getElementById('menu-closed-icon');
            const menuOpenIcon = document.getElementById('menu-open-icon');

            if (mobileMenuButton && sidebar && overlay) {
                function toggleMobileMenu() {
                    const isOpen = !sidebar.classList.contains('-translate-x-full');

                    if (isOpen) {
                        // Close menu
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                        menuClosedIcon.classList.remove('hidden');
                        menuOpenIcon.classList.add('hidden');
                    } else {
                        // Open menu
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        menuClosedIcon.classList.add('hidden');
                        menuOpenIcon.classList.remove('hidden');
                    }
                }

                // Event listeners
                mobileMenuButton.addEventListener('click', toggleMobileMenu);
                overlay.addEventListener('click', toggleMobileMenu);

                // Close menu when clicking on a link (mobile only)
                const sidebarLinks = sidebar.querySelectorAll('a, button');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 1024) { // lg breakpoint
                            toggleMobileMenu();
                        }
                    });
                });

                // Handle window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        // Desktop view - ensure sidebar is visible and overlay is hidden
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.add('hidden');
                        menuClosedIcon.classList.remove('hidden');
                        menuOpenIcon.classList.add('hidden');
                    } else {
                        // Mobile view - ensure sidebar is hidden initially
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
