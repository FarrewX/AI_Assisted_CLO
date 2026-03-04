<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- resources/views/layouts/navbar.blade.php -->
    <nav x-data="{ mobileOpen: false, userOpen: false }" 
        class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm transition-all duration-300">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                <!-- Desktop Menu -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.location='{{ url('/') }}'">
                        <span class="font-bold text-xl tracking-tight text-gray-800">ELO Generator</span>
                    </div>

                    <div class="hidden sm:ml-8 sm:flex">
                        <a href="{{ url('/') }}" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                        {{ Request::is('/') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-gray-300' }}">
                            หน้าหลัก
                        </a>
                    </div>
                    @if(Auth::check() && Auth::user()->role_id == 1)
                        <div class="hidden sm:flex">
                            <div class="hidden sm:ml-8 sm:flex">
                                <a href="{{ url('/management') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                                {{ Request::is('management*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-gray-300' }}">
                                    การจัดการ
                                </a>
                            </div>
                            <div class="hidden sm:ml-8 sm:flex">
                                <a href="{{ url('/notification') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                                {{ Request::is('notification*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-gray-300' }}">
                                    แจ้งเตือน
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    @auth
                        <div class="ml-3 relative">
                            <div>
                                <button @click="userOpen = !userOpen" 
                                        @click.away="userOpen = false"
                                        class="bg-white flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 items-center gap-2 px-3 py-1 border border-gray-200 hover:bg-gray-50 transition">
                                    <span class="sr-only">Open user menu</span>
                                    
                                    <div class="h-8 w-8 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 {{ !Auth::user()->profile ? 'bg-blue-100' : '' }}">
                                        @if(Auth::user()->profile)
                                            <img src="{{ asset('storage/' . Auth::user()->profile) }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-blue-600 font-bold text-sm">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                                        @endif
                                    </div>

                                    <span class="text-gray-700 font-medium hidden md:block">{{ Auth::user()->name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="userOpen"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                style="display: none;">
                                
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs text-gray-500">Signed in as</p>
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">ตั้งค่าโปรไฟล์</a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">เข้าสู่ระบบ</a>
                    @endauth
                </div>

                <!-- Hamburger button (mobile only) -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen" class="sm:hidden bg-white border-t border-gray-100" id="mobile-menu" style="display: none;">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('/') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }}">
                    หน้าหลัก
                </a>
                <a href="{{ url('/management') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('management*') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }}">
                    การจัดการ
                </a>
                <a href="{{ url('/notification') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('notification*') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }}">
                    แจ้งเตือน
                </a>
            </div>
            
            @auth
            <div class="pt-4 pb-4 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 {{ !Auth::user()->profile ? 'bg-blue-100' : '' }}">
                            @if(Auth::user()->profile)
                                <img src="{{ asset('storage/' . Auth::user()->profile) }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-blue-600 font-bold text-lg">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                        ตั้งค่าโปรไฟล์
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-red-600 hover:text-red-800 hover:bg-gray-50">
                            ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>
</body>
</html>