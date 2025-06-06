<nav class="bg-blue-800 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="#" class="text-white font-bold text-xl">AVSEC App</a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Dashboard
                    </a>
                    @if(auth()->user()->role->name === 'superadmin')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        User Management
                    </a>
                    @endif
                    
                    @if(auth()->user()->role->name === 'supervisor' || auth()->user()->role->name === 'superadmin')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Reports
                    </a>
                    @endif
                    
                    @if(auth()->user()->role->name === 'officer')
                    <a href="#" class="border-transparent text-white hover:border-white hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Submit Report
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- User Dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="ml-3 relative">
                    <div class="flex items-center">
                        <span class="mr-2">{{ auth()->user()->name }}</span>
                        <form method="GET" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white text-sm px-3 py-1 rounded">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
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
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div id="mobile-menu" class="sm:hidden hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="#" class="bg-blue-700 text-white block pl-3 pr-4 py-2 text-base font-medium">
                Dashboard
            </a>
            
            @if(auth()->user()->role->name === 'superadmin')
            <a href="#" class="text-white hover:bg-blue-700 block pl-3 pr-4 py-2 text-base font-medium">
                User Management
            </a>
            @endif
            
            @if(auth()->user()->role->name === 'supervisor' || auth()->user()->role->name === 'superadmin')
            <a href="#" class="text-white hover:bg-blue-700 block pl-3 pr-4 py-2 text-base font-medium">
                Reports
            </a>
            @endif
            
            @if(auth()->user()->role->name === 'officer')
            <a href="#" class="text-white hover:bg-blue-700 block pl-3 pr-4 py-2 text-base font-medium">
                Submit Report
            </a>
            @endif
            
            <div class="pt-4 pb-3 border-t border-blue-700">
                <div class="flex items-center px-4">
                    <div class="text-base font-medium text-white">{{ auth()->user()->name }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <form method="GET" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white hover:bg-blue-700 block px-3 py-2 text-base font-medium w-full text-left">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Toggle mobile menu
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        const menuClosedIcon = document.getElementById('menu-closed-icon');
        const menuOpenIcon = document.getElementById('menu-open-icon');
        
        mobileMenu.classList.toggle('hidden');
        menuClosedIcon.classList.toggle('hidden');
        menuOpenIcon.classList.toggle('hidden');
    });
</script>