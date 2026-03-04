<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav x-data="{ mobileOpen: false, userOpen: false }" 
        class="fixed top-0 left-0 w-full z-50 bg-pure-white/90 backdrop-blur-md border-b border-cloud-blue shadow-sm transition-all duration-300">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.location='{{ url('/') }}'">
                        <span class="font-bold text-xl tracking-tight text-deep-navy">ELO Generator</span>
                    </div>

                    <div class="hidden sm:ml-8 sm:flex">
                        <a href="{{ url('/') }}" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                        {{ Request::is('/') ? 'border-royal-blue text-deep-navy font-bold' : 'border-transparent text-slate-grey hover:text-royal-blue hover:border-cloud-blue' }}">
                            หน้าหลัก
                        </a>
                    </div>
                    @if(Auth::check() && Auth::user()->role_id == 1)
                        <div class="hidden sm:flex">
                            <div class="hidden sm:ml-8 sm:flex">
                                <a href="{{ url('/management') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                                {{ Request::is('management*') ? 'border-royal-blue text-deep-navy font-bold' : 'border-transparent text-slate-grey hover:text-royal-blue hover:border-cloud-blue' }}">
                                    การจัดการ
                                </a>
                            </div>
                            <div class="hidden sm:ml-8 sm:flex">
                                <a href="{{ url('/notification') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                                {{ Request::is('notification*') ? 'border-royal-blue text-deep-navy font-bold' : 'border-transparent text-slate-grey hover:text-royal-blue hover:border-cloud-blue' }}">
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
                                        class="bg-pure-white flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-blue/50 items-center gap-2 px-3 py-1 border border-cloud-blue hover:bg-cloud-blue/20 transition">
                                    <span class="sr-only">Open user menu</span>
                                    
                                    <div class="h-8 w-8 rounded-full overflow-hidden flex items-center justify-center border border-cloud-blue {{ !Auth::user()->profile ? 'bg-cloud-blue/50' : '' }}">
                                        @if(Auth::user()->profile)
                                            <img src="{{ asset('storage/' . Auth::user()->profile) }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-royal-blue font-bold text-sm">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                                        @endif
                                    </div>

                                    <span class="text-deep-navy font-medium hidden md:block">{{ Auth::user()->name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-grey" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-pure-white border border-cloud-blue ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                style="display: none;">
                                
                                <div class="px-4 py-2 border-b border-cloud-blue">
                                    <p class="text-xs text-slate-grey">Signed in as</p>
                                    <p class="text-sm font-semibold text-deep-navy truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-grey hover:bg-cloud-blue/30 hover:text-deep-navy transition-colors">ตั้งค่าโปรไฟล์</a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-royal-blue hover:text-deep-navy px-3 py-2 rounded-md text-sm font-bold transition-colors">เข้าสู่ระบบ</a>
                    @endauth
                </div>

                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-slate-grey hover:text-deep-navy hover:bg-cloud-blue/30 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-royal-blue" aria-controls="mobile-menu" aria-expanded="false">
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

        <div x-show="mobileOpen" class="sm:hidden bg-pure-white border-t border-cloud-blue shadow-lg" id="mobile-menu" style="display: none;">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('/') ? 'bg-cloud-blue/30 border-royal-blue text-royal-blue' : 'border-transparent text-slate-grey hover:bg-cloud-blue/20 hover:border-cloud-blue hover:text-deep-navy' }}">
                    หน้าหลัก
                </a>
                <a href="{{ url('/management') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('management*') ? 'bg-cloud-blue/30 border-royal-blue text-royal-blue' : 'border-transparent text-slate-grey hover:bg-cloud-blue/20 hover:border-cloud-blue hover:text-deep-navy' }}">
                    การจัดการ
                </a>
                <a href="{{ url('/notification') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ Request::is('notification*') ? 'bg-cloud-blue/30 border-royal-blue text-royal-blue' : 'border-transparent text-slate-grey hover:bg-cloud-blue/20 hover:border-cloud-blue hover:text-deep-navy' }}">
                    แจ้งเตือน
                </a>
            </div>
            
            @auth
            <div class="pt-4 pb-4 border-t border-cloud-blue">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full overflow-hidden flex items-center justify-center border border-cloud-blue {{ !Auth::user()->profile ? 'bg-cloud-blue/50' : '' }}">
                            @if(Auth::user()->profile)
                                <img src="{{ asset('storage/' . Auth::user()->profile) }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-royal-blue font-bold text-lg">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-bold text-deep-navy">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-slate-grey">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-slate-grey hover:text-deep-navy hover:bg-cloud-blue/20 transition-colors">
                        ตั้งค่าโปรไฟล์
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
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