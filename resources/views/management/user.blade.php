<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้งาน | User Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/user.js'])
    @endif
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800"> 
    @include('component.navbar')

    <div class="max-w-7xl mx-auto pt-24 pb-12 px-4 sm:px-6 lg:px-8"> 
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    จัดการผู้ใช้งาน (User Management)
                </h1>
                <p class="text-slate-500 mt-2 ml-1">ดูรายชื่อ เปลี่ยนบทบาท และจัดการสถานะผู้ใช้งานในระบบ</p>
            </div>
            
            <button id="btn-add-user" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md transition-all duration-200">
                <i class="fa-solid fa-user-plus"></i>
                <span>เพิ่มผู้ใช้งาน</span>
            </button>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
            <form method="GET" action="" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="ค้นหาด้วยชื่อ, อีเมล หรือ User ID" value="{{ request('search') }}">
                </div>
                <button type="submit" class="bg-slate-100 text-slate-600 hover:bg-slate-200 px-4 py-2.5 rounded-lg font-medium transition-colors">
                    ค้นหา
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">User Info</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">User ID</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Last Login</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors duration-150 {{ $user->trashed() ? 'bg-slate-50/80' : '' }}">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 {{ $user->trashed() ? 'grayscale opacity-50' : '' }}">
                                            @if($user->avatar)
                                                <img class="h-10 w-10 rounded-full object-cover shadow-sm border border-slate-200" 
                                                    src="{{ asset('storage/' . $user->avatar) }}" 
                                                    alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 flex items-center justify-center text-blue-500 font-bold text-lg shadow-sm">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold {{ $user->trashed() ? 'text-slate-500 line-through' : 'text-slate-900' }}">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-mono border border-slate-200">
                                        #{{ $user->user_id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-slate-100 text-slate-600 border-slate-200">
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user->trashed())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600 border border-red-200">
                                            <i class="fa-solid fa-ban"></i> ปิดการใช้งาน
                                        </span>
                                    @elseif($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($user->trashed())
                                        <button class="btn-restore-user inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors border border-emerald-200 shadow-sm"
                                            title="กู้คืนผู้ใช้งาน"
                                            data-id="{{ $user->user_id }}" 
                                            data-name="{{ $user->name }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>กู้คืนผู้ใช้งาน</span>
                                        </button>
                                    @else
                                        <button class="btn-edit-role text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors mr-2"
                                            title="เปลี่ยนบทบาท"
                                            data-id="{{ $user->user_id }}" data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}" data-role-id="{{ $user->role_id }}"
                                            data-avatar="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}">
                                            <i class="fa-solid fa-user-tag"></i>
                                        </button>
                                        
                                        <button class="btn-delete-user text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors"
                                            title="ลบผู้ใช้งาน"
                                            data-id="{{ $user->user_id }}" data-name="{{ $user->name }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-users-slash text-4xl mb-3"></i><br>
                                    <span class="text-lg font-medium">ไม่พบข้อมูลผู้ใช้งาน</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <div id="edit-role-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                <h3 class="text-xl font-bold text-slate-800">เปลี่ยนบทบาท (Change Role)</h3>
                <button type="button" class="btn-close-modal text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form id="edit-role-form" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 mb-4">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold overflow-hidden border border-slate-200 relative">
                            <img id="modal-user-img" src="" class="hidden h-full w-full object-cover">
                            <span id="modal-user-avatar" class="text-lg">U</span>
                        </div>
                        <div>
                            <p id="modal-user-name" class="text-sm font-bold text-slate-800">User Name</p>
                            <p id="modal-user-email" class="text-xs text-slate-500">user@email.com</p>
                        </div>
                    </div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">เลือกบทบาทใหม่</label>
                    <select name="role" id="modal-role-select" class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5">
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" class="btn-close-modal flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition">ยกเลิก</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium shadow-md transition">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>

    <div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg transform transition-all scale-100 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                <h3 class="text-xl font-bold text-slate-800">เพิ่มผู้ใช้งานใหม่ (Add User)</h3>
                <button type="button" class="btn-close-add-modal text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('management.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อ-นามสกุล</label>
                        <input type="text" name="name" required class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5" placeholder="สมชาย ใจดี">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อผู้ใช้ (Username)</label>
                        <input type="text" name="username" required class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5" placeholder="somchai01">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">อีเมล</label>
                        <input type="email" name="email" required class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5" placeholder="somchai@example.com">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">รหัสผ่าน</label>
                            <input type="password" name="password" required minlength="6" class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">ยืนยันรหัสผ่าน</label>
                            <input type="password" name="password_confirmation" required minlength="6" class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">บทบาท (Role)</label>
                        <select name="role_id" required class="block w-full border-slate-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5">
                            <option value="">-- เลือกบทบาท --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" class="btn-close-add-modal flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition">ยกเลิก</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium shadow-md transition">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all scale-100 text-center">
            
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4 border-4 border-red-50">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
            </div>

            <h3 class="text-xl font-bold text-slate-900 mb-2">ยืนยันการลบ?</h3>
            <p class="text-sm text-slate-500 mb-6">
                คุณต้องการลบผู้ใช้งาน <span id="delete-user-name" class="font-bold text-slate-800"></span> ใช่หรือไม่?<br>
                การกระทำนี้ไม่สามารถย้อนกลับได้
            </p>

            <form id="delete-user-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3 justify-center">
                    <button type="button" class="btn-close-delete-modal flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl font-medium shadow-md transition hover:shadow-lg">
                        ยืนยัน ลบเลย
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="restore-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all scale-100 text-center">
            
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-4 border-4 border-emerald-50">
                <i class="fa-solid fa-rotate-left text-3xl text-emerald-600"></i>
            </div>

            <h3 class="text-xl font-bold text-slate-900 mb-2">ยืนยันการเปิดใช้งาน?</h3>
            <p class="text-sm text-slate-500 mb-6">
                คุณต้องการเปิดการใช้งานผู้ใช้ <span id="restore-user-name" class="font-bold text-slate-800"></span> อีกครั้งใช่หรือไม่?
            </p>

            <form id="restore-user-form" method="POST">
                @csrf
                <div class="flex gap-3 justify-center">
                    <button type="button" class="btn-close-restore-modal flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl font-medium shadow-md transition">
                        ยืนยัน
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success') || session('error'))
        <div id="popup-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-2xl transform transition-all scale-100">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 {{ session('error') ? 'bg-red-100' : 'bg-green-100' }}">
                        @if(session('error'))
                            <i class="fa-solid fa-xmark text-2xl text-red-600"></i>
                        @else
                            <i class="fa-solid fa-check text-2xl text-green-600"></i>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">
                        {{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}
                    </h3>
                    <p class="text-sm text-slate-500 mb-6">
                        {{ session('success') ?? session('error') }}
                    </p>
                    <button onclick="document.getElementById('popup-notification').remove()" 
                        class="w-full inline-flex justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200
                        {{ session('error') ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>
    @endif

</body>
</html>