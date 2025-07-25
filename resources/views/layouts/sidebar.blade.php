<div id="sidebar"
    class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed top-16 lg:top-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:pt-4">
    <!-- <div class="mb-8 hidden lg:block">
        <h2 class="text-2xl font-bold mb-2">AVSEC App</h2>
        <p class="text-sm text-gray-400">{{ auth()->user()->role->name }}</p>
    </div> -->

    <nav class="lg:mt-20 sm:mt-0">
        <ul class="space-y-2">
            <!-- Dashboard - All Roles -->
            <li>
                <a href="{{ url('/dashboard/' . auth()->user()->role->name) }}"
                    class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->is('dashboard/*') ? 'bg-gray-700' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Superadmin Menu Items -->
            @if(auth()->user()->role->name === 'superadmin')
             <li x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-white">Master Data</span>
                    </div>
                    <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <ul x-show="open" class="pl-4 mt-2 space-y-2">

                    <li>
                        <a href="{{ route('users-management.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>User Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('equipment-locations.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>Equipment location</span>
                        </a>
                    </li>
                </ul>
            </li>

            @endif

            <!-- Common Menu Items for All Roles -->
            <li x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-white">Daily Test</span>
                    </div>
                    <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <ul x-show="open" class="pl-4 mt-2 space-y-2">
                    <li>
                        <a href="{{ route('daily-test.hhmd') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>HHMD</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('daily-test.wtmd') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>WTMD</span>
                        </a>
                    </li>

                    <li x-data="{ openXray: false }">
                        <button @click="openXray = !openXray"
                            class="flex items-center justify-between w-full py-2 px-4 rounded hover:bg-gray-700 no-mobile-close">
                            <span>X-RAY</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openXray }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="openXray" class="pl-4 mt-2 space-y-2">
                            <li>
                                <a href="{{ route('daily-test.xraycabin') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                                    <span>XRAY CABIN</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('daily-test.xraybagasi') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                                    <span>XRAY BAGASI</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="mt-6">
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Logbook Pos Jaga</span>
                </a>
            </li>
            <li class="mt-6">
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Check List CCTV</span>
                </a>
            </li>
            <li class="mt-6">
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Check List Kendaraan</span>
                </a>
            </li>
            <li class="mt-6">
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Sweeping PI</span>
                </a>
            </li>
            <li class="mt-6">
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Laporan Supervisor</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>PM dan IK</span>
                </a>
            </li>
            @if(auth()->user()->role->name === 'superadmin')
            <li>
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Export PDF</span>
                </a>
            </li>
            @endif

            <!-- Logout for Desktop (hidden on mobile as it's in navbar) -->
            <li class="hidden lg:block">
                <form method="GET" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center p-2 rounded hover:bg-gray-700 w-full text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>
