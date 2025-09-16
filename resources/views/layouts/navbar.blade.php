<nav class="bg-blue-800 text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class=" mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Mobile menu button - moved to left -->
                <div class="flex items-center lg:hidden mr-4">
                    <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon when menu is closed -->
                        <svg id="menu-closed-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Icon when menu is open -->
                        <svg id="menu-open-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                     {{-- <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-10 h-10 mr-2"> --}}
                    <a href="#" class="text-white font-bold text-xl">ASRS APP</a>
                </div>
<!--
                <button id="toggleSidebar" class="lg:inline-flex hidden items-center px-3 py-2 text-white  rounded">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button> -->


                <!-- Navigation Links - Hidden on mobile and tablet, shown on desktop -->
                <!-- <div class="hidden xl:ml-6 xl:flex xl:space-x-8">
                    <a href="{{ url('/dashboard/' . auth()->user()->role->name) }}" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->is('dashboard/*') ? 'border-white' : '' }}">
                        Dashboard
                    </a>
                    @if(auth()->user()->role->name === 'superadmin')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        User Management
                    </a>
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        System Settings
                    </a>
                    @endif

                    @if(auth()->user()->role->name === 'supervisor' || auth()->user()->role->name === 'superadmin')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Reports
                    </a>
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Analytics
                    </a>
                    @endif

                    @if(auth()->user()->role->name === 'officer')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Submit Report
                    </a>
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        My Reports
                    </a>
                    @endif
                </div> -->
            </div>

            <!-- User Dropdown -->
            <div class="flex items-center">
                <div class="ml-3 relative">
                    <div class="flex items-center">
                        <span class="mr-2 hidden sm:block text-sm">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white text-sm px-3 py-1 rounded flex items-center">
                                <span class="hidden sm:inline">Logout</span>
                                <svg class="sm:hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
