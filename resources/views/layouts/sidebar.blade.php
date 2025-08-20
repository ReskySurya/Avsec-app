@php
$isDailyTestOpen = request()->is('daily-test/*');
$isMasterDataOpen = request()->is('master-data/*');
$isXrayOpen = request()->is('daily-test/xray*');
$isLogbookOpen = request()->is('logbook*');
$isLogbookPosJagaOpen = request()->is('logbook/posjaga*');
$isChecklistOpen = request()->is('checklist*');
@endphp

<div id="sidebar"
    class="bg-gray-800 text-white w-72 h-screen p-4 fixed top-16 lg:top-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:pt-4 overflow-y-auto">
    <nav class="lg:mt-20 sm:mt-0">
        <ul class="space-y-2">
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

            @if(auth()->user()->role->name === 'superadmin')
            <li x-data="{ open: {{ $isMasterDataOpen ? 'true' : 'false' }} }">
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
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <ul x-show="open" class="pl-8 mt-2 space-y-2">
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
                    <li>
                        <a href="{{ route('tenant-management.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>Tenant Management</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Daily Test -->
            <li x-data="{ open: {{ $isDailyTestOpen ? 'true' : 'false' }} }">
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
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>

                </button>
                <ul x-show="open" class="pl-8 mt-2 space-y-2">
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

                    <!-- X-RAY -->
                    <li x-data="{ openXray: {{ $isXrayOpen ? 'true' : 'false' }} }">
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

            <li x-data="{ open: {{ $isLogbookOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-white">Logbook</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <ul x-show="open" class="pl-8 mt-2 space-y-2">
                    <!-- Submenu Pos Jaga -->
                    <li x-data="{ openPosJaga: {{ $isLogbookPosJagaOpen ? 'true' : 'false' }} }">
                        <a href="{{ route('logbook.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook/posjaga') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Pos Jaga</span>
                        </a>
                    </li>

                    <!-- Menu Lainnya -->
                    @if(auth()->user()->role->name === 'officer')
                    <li>
                        <a href="{{ route('logbookSweppingPI.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook-sweppingpi') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Sweeping PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logbookRotasi.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook-rotasi') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Rotasi</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('sweepingPI.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('sweepingpi') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Sweeping PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('supervisor.logbook-rotasi.list') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook-rotasi/list') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Rotasi</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            <li x-data="{ open: {{ $isChecklistOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-white">Checklist</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <ul x-show="open" class="pl-8 mt-2 space-y-2">
                    <!-- Submenu Checklist -->
                    @if(auth()->user()->role->name === 'officer')
                    <li>
                        <a href="#"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-motor-patroli') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Motor Patroli</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-perawatan-patroli') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Perawatan Patroli</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-penyisiran') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Penyisiran Terminal B</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('form-catatan-pi') ? 'bg-gray-700' : '' }}">
                            <span>Form Catatan PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('buku-pemeriksaan-manual') ? 'bg-gray-700' : '' }}">
                            <span>Buku Pemeriksaan Manual</span>
                        </a>
                    </li>
                    @else
                    <!-- <li>
                        <a href="{{ route('sweepingPI.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('sweepingpi') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Sweeping PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('supervisor.logbook-rotasi.list') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook-rotasi/list') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Rotasi</span>
                        </a>
                    </li> -->
                    @endif
                </ul>
            </li>




            @if(auth()->user()->role->name === 'superadmin')
            <li>
                <a href="{{ route('export.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" ...></svg>
                    <span>Export PDF</span>
                </a>
            </li>
            @endif
            <!-- Other static menu items -->
            <li>
                <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" ...></svg>
                    <span>PM dan IK</span>
                </a>
            </li>


            <li class="hidden lg:block">
                <form method="GET" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center p-2 rounded hover:bg-gray-700 w-full text-left">
                        <svg class="h-5 w-5 mr-3" ...></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>