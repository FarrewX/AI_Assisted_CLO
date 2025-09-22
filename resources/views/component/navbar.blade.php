<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- resources/views/layouts/navbar.blade.php -->
    <nav class="w-full bg-white shadow-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
            
            <!-- Desktop Menu -->
            <div class="flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-lg font-bold">ELO Generator</a>
                <a href="{{ url('/about') }}" class="hidden md:flex space-x-8 mt-1.5 text-sm font-medium hover:text-blue-600">about</a>
                <a href="{{ url('/guide') }}" class="hidden md:flex space-x-8 mt-1.5 text-sm font-medium hover:text-blue-600">วิธีการใช้งานระบบ</a>

                @if(Auth::check() && Auth::user()->role_id == 1)
                    <a href="{{ url('/notification') }}" class="hidden md:flex space-x-8 mt-1.5 text-sm font-medium hover:text-blue-600">
                        notification
                    </a>
                    <a href="{{ url('/setting') }}" class="hidden md:flex space-x-8 mt-1.5 text-sm font-medium hover:text-blue-600">
                        setting
                    </a>
                @endif
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ url('/form') }}" class="hidden md:inline-block bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                    Get start
                </a>
                <div class="hidden md:inline-block relative inline-block text-left">
                <!-- ไอคอนผู้ใช้ -->
                <button id="userMenuButton" type="button"
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                    aria-expanded="true" aria-haspopup="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 16 16" class="w-5 h-5 text-gray-700">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4-3a4 4 0 1 
                                1-8 0 4 4 0 0 1 8 0zM14 14s-1-1.5-6-1.5S2 14 2 
                                14s1-4 6-4 6 4 6 4z"/>
                    </svg>
                </button>

                <!-- Dropdown ออกจากระบบ -->
                <div id="userDropdown"
                    class="hidden absolute right-0 mt-2 w-36 bg-white rounded-md shadow-lg py-1 z-50">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100 hover:text-red-700">
                            ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>

                <!-- Hamburger button (mobile only) -->
                <button @click="open = !open" class="md:hidden p-2 rounded-md hover:bg-gray-100">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div 
            class="fixed inset-y-0 right-0 w-64 z-50 flex"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <!-- Overlay (พื้นหลังดำโปร่งคลิกปิดได้) -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>

            <!-- Sidebar -->
            <div 
                class="relative w-64 bg-white shadow-lg h-full z-50 p-6"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                <!-- Close button -->
                <button @click="open = false" class="absolute top-4 right-7.5 text-gray-600 hover:text-gray-900">
                    ✕
                </button>

                <nav class="space-y-4 mt-8">
                    <a href="{{ url('/about') }}" class="block text-base font-medium hover:text-blue-600">about</a>
                    <a href="{{ url('/guide') }}" class="block text-base font-medium hover:text-blue-600">วิธีการใช้งานระบบ</a>
                    
                    @if(Auth::check() && Auth::user()->role_id == 1)
                        <a href="{{ url('/notification') }}" class="block text-base font-medium hover:text-blue-600">notification</a>
                        <a href="{{ url('/setting') }}" class="block text-base font-medium hover:text-blue-600">setting</a>
                    @endif
                    
                    <a href="{{ url('/form') }}" class="block bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                        Get start
                    </a>
                    <a class="flex w-10 h-10 items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" 
                            viewBox="0 0 16 16" class="w-6 h-6 text-gray-700">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4-3a4 4 0 1 
                                    1-8 0 4 4 0 0 1 8 0zM14 14s-1-1.5-6-1.5S2 14 2 
                                    14s1-4 6-4 6 4 6 4z"/>
                        </svg>
                    </a>
                    <form class="block text-base font-medium text-red-500 hover:text-red-700" action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn">ออกจากระบบ</button>
                    </form>
                </nav>
            </div>
        </div>
    </nav>
</body>
<script>
    const userButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');

    userButton.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    // ปิด dropdown ถ้าคลิกนอก
    window.addEventListener('click', (e) => {
        if (!userButton.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });
</script>
</html>
