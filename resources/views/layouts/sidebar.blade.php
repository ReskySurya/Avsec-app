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
                    {{-- Dashboard Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->role->name === 'superadmin')
            <li x-data="{ open: {{ $isMasterDataOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        {{-- Master Data Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
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
                        <a href="{{ route('users-management.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>User Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('equipment-locations.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>Equipment location</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tenant-management.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>Tenant Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist-items.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>Checklist Items</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Daily Test -->
            @if(auth()->user()->role->name === 'officer')
            <li x-data="{ open: {{ $isDailyTestOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        {{-- Daily Test Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
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
                        <a href="{{ route('daily-test.hhmd') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                            <span>HHMD</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('daily-test.wtmd') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
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
                                <a href="{{ route('daily-test.xraycabin') }}"
                                    class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                                    <span>XRAY CABIN</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('daily-test.xraybagasi') }}"
                                    class="flex items-center py-2 px-4 rounded hover:bg-gray-700">
                                    <span>XRAY BAGASI</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            @else
            <li>
                <div class="flex items-center rounded hover:bg-gray-700 no-mobile-close">
                    {{-- Daily Test Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 ms-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <a href="{{ route('supervisor.dailytest-form') }}" class="flex items-center py-2 rounded hover:bg-gray-700 w-full">
                        <span>Daily Test</span>
                    </a>
                </div>
            </li>
            @endif

            <li x-data="{ open: {{ $isLogbookOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        {{-- Logbook Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
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
                    <!-- Menu Lainnya -->
                    @if(auth()->user()->role->name === 'officer')
                    <li x-data="{ openPosJaga: {{ $isLogbookPosJagaOpen ? 'true' : 'false' }} }">
                        <a href="{{ route('logbook.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook/posjaga') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Pos Jaga</span>
                        </a>
                    </li>
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
                        <a href="{{ route('supervisor.logbook-form') }}" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('posjaga') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Pos Jaga</span>
                        </a>
                    </li>
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
                    <li>
                        <a href="{{ route('logbook.chief.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('logbook-chief') ? 'bg-gray-700' : '' }}">
                            <span>Logbook Chief</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            <li x-data="{ open: {{ $isChecklistOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full py-2 px-2 rounded hover:bg-gray-700 no-mobile-close">
                    <div class="flex items-center">
                        {{-- Checklist Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
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
                        <a href="{{ route('checklist.kendaraan.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-kendaraan-patroli') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Kendaraan Patroli</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist.penyisiran.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-penyisiran') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Penyisiran Ruang Tunggu</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist.senpi.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-senpi') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Senjata Api</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist.pencatatanpi.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('form-catatan-pi') ? 'bg-gray-700' : '' }}">
                            <span>Form Pencatatan PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist.manualbook.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('buku-pemeriksaan-manual') ? 'bg-gray-700' : '' }}">
                            <span>Buku Pemeriksaan Manual</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{route('supervisor.checklist-kendaraan.list')}}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-kendaraan/list') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Kendaraan Patroli</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('supervisor.checklist-penyisiran.list') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-penyisiran/list') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Penyisiran Ruang Tunggu</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('checklist.senpi.index') }}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklis-senpi') ? 'bg-gray-700' : '' }}">
                            <span>Checklist Senjata Api</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('supervisor.form-pencatatan-pi.list')}}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-pencatatanpi/list') ? 'bg-gray-700' : '' }}">
                            <span>Form Pencatatan PI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('supervisor.checklist-manualbook.list')}}"
                            class="flex items-center py-2 px-4 rounded hover:bg-gray-700 {{ request()->is('checklist-manualbook/list') ? 'bg-gray-700' : '' }}">
                            <span>Buku Pemeriksaan Manual</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            @if(auth()->user()->role->name === 'superadmin')
            <li>
                <a href="{{ route('pmik.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    {{-- PM dan IK Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>PM dan IK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('export.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    {{-- Export PDF Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export PDF</span>
                </a>
            </li>
            @else
            <li>
                <a href="{{ route('pmik.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    {{-- PM dan IK Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>PM dan IK</span>
                </a>
            </li>
            @endif

            <li class="hidden lg:block">
                <form method="GET" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center p-2 rounded hover:bg-gray-700 w-full text-left">
                        {{-- Logout Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>
