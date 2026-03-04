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
<body class="text-slate-grey"> 
    @include('component.navbar')

    <div class="max-w-7xl mx-auto pt-24 pb-12 px-4 sm:px-6 lg:px-8"> 
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-deep-navy flex items-center gap-3">
                    <div class="p-2.5 bg-cloud-blue/40 rounded-xl text-royal-blue border border-cloud-blue/50">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    จัดการผู้ใช้งาน (User Management)
                </h1>
                <p class="text-slate-grey mt-2 ml-1">ดูรายชื่อ เปลี่ยนบทบาท และจัดการสถานะผู้ใช้งานในระบบ</p>
            </div>
            
            <button id="btn-add-user" 
               class="inline-flex items-center gap-2 bg-royal-blue hover:bg-deep-navy text-pure-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-royal-blue/20 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-user-plus"></i>
                <span>เพิ่มผู้ใช้งาน</span>
            </button>
        </div>

        <div class="bg-pure-white p-4 rounded-xl shadow-sm border border-cloud-blue mb-6">
            <form method="GET" action="" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-grey/70"></i>
                    </div>
                    <input type="text" name="search" class="block w-full pl-11 pr-3 py-2.5 border border-cloud-blue rounded-lg leading-5 bg-pure-white placeholder-slate-grey/60 focus:outline-none focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue sm:text-sm text-deep-navy transition-all" placeholder="ค้นหาด้วยชื่อ, อีเมล หรือ User ID" value="{{ request('search') }}">
                </div>
                <button type="submit" class="bg-cloud-blue/40 text-deep-navy font-bold hover:bg-cloud-blue px-6 py-2.5 rounded-lg transition-colors">
                    ค้นหา
                </button>
            </form>
        </div>

        <div class="bg-pure-white shadow-sm rounded-2xl border border-cloud-blue overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-cloud-blue">
                    <thead class="bg-cloud-blue/30">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">User Info</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">User ID</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">Last Login</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-deep-navy uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-pure-white divide-y divide-cloud-blue/50">
                        @forelse($users as $user)
                            <tr class="hover:bg-cloud-blue/10 transition-colors duration-150 {{ $user->trashed() ? 'bg-cloud-blue/5 opacity-70' : '' }}">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 {{ $user->trashed() ? 'grayscale opacity-50' : '' }}">
                                            @if($user->profile)
                                                <img class="h-10 w-10 rounded-full object-cover shadow-sm border border-cloud-blue" 
                                                    src="{{ asset('storage/' . $user->profile) }}" 
                                                    alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-cloud-blue/40 flex items-center justify-center text-royal-blue font-bold text-lg shadow-sm border border-cloud-blue/50">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold {{ $user->trashed() ? 'text-slate-grey line-through' : 'text-deep-navy' }}">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-slate-grey">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-md bg-cloud-blue/30 text-deep-navy text-xs font-mono font-bold border border-cloud-blue">
                                        #{{ $user->user_id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-cloud-blue/20 text-deep-navy border-cloud-blue">
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user->trashed())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                            <i class="fa-solid fa-ban"></i> ปิดการใช้งาน
                                        </span>
                                    @elseif($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-cloud-blue/30 text-slate-grey border border-cloud-blue">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-grey"></span> Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-grey font-medium">
                                    {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($user->trashed())
                                        <button class="btn-restore-user inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors border border-emerald-200 shadow-sm"
                                            title="กู้คืนผู้ใช้งาน"
                                            data-id="{{ $user->user_id }}" 
                                            data-name="{{ $user->name }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>กู้คืนผู้ใช้งาน</span>
                                        </button>
                                    @else
                                        <button class="btn-edit-role text-royal-blue hover:text-deep-navy bg-cloud-blue/40 hover:bg-cloud-blue p-2 rounded-lg transition-colors mr-2"
                                            title="เปลี่ยนบทบาท"
                                            data-id="{{ $user->user_id }}" data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}" data-role-id="{{ $user->role_id }}"
                                            data-profile="{{ $user->profile ? asset('storage/' . $user->profile) : '' }}">
                                            <i class="fa-solid fa-user-tag"></i>
                                        </button>
                                        
                                        <button class="btn-delete-user text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors"
                                            title="ลบผู้ใช้งาน"
                                            data-id="{{ $user->user_id }}" data-name="{{ $user->name }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-grey bg-cloud-blue/10">
                                    <i class="fa-solid fa-users-slash text-4xl mb-3 text-cloud-blue"></i><br>
                                    <span class="text-lg font-bold text-deep-navy">ไม่พบข้อมูลผู้ใช้งาน</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-cloud-blue bg-cloud-blue/10">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <div id="edit-role-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100 border border-cloud-blue">
            <div class="flex justify-between items-center border-b border-cloud-blue pb-4 mb-4">
                <h3 class="text-xl font-bold text-deep-navy">เปลี่ยนบทบาท (Change Role)</h3>
                <button type="button" class="btn-close-modal text-slate-grey hover:text-red-500 transition bg-cloud-blue/30 hover:bg-cloud-blue p-1.5 rounded-full">
                    <i class="fa-solid fa-xmark text-lg px-0.5"></i>
                </button>
            </div>
            
            <form id="edit-role-form" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <div class="flex items-center gap-4 p-4 bg-cloud-blue/20 rounded-xl border border-cloud-blue mb-5">
                        <div class="h-12 w-12 rounded-full bg-pure-white flex items-center justify-center text-royal-blue font-bold overflow-hidden border border-cloud-blue relative shadow-sm">
                            <img id="modal-user-img" src="" class="hidden h-full w-full object-cover">
                            <span id="modal-user-profile" class="text-lg">U</span>
                        </div>
                        <div>
                            <p id="modal-user-name" class="text-sm font-bold text-deep-navy">User Name</p>
                            <p id="modal-user-email" class="text-xs text-slate-grey">user@email.com</p>
                        </div>
                    </div>

                    <label class="block text-sm font-bold text-deep-navy mb-2">เลือกบทบาทใหม่</label>
                    <select name="role" id="modal-role-select" class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy font-medium text-sm py-3 px-3 transition-all outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 mt-6 pt-2 border-t border-cloud-blue">
                    <button type="button" class="btn-close-modal flex-1 bg-cloud-blue/30 text-deep-navy py-2.5 rounded-xl font-bold hover:bg-cloud-blue transition-colors">ยกเลิก</button>
                    <button type="submit" class="flex-1 bg-royal-blue hover:bg-deep-navy text-pure-white py-2.5 rounded-xl font-bold shadow-md transition-colors">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>

    <div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100 max-h-[90vh] overflow-y-auto border border-cloud-blue">
            <div class="flex justify-between items-center border-b border-cloud-blue pb-4 mb-5">
                <h3 class="text-xl font-bold text-deep-navy">เพิ่มผู้ใช้งานใหม่ (Add User)</h3>
                <button type="button" class="btn-close-add-modal text-slate-grey hover:text-red-500 transition bg-cloud-blue/30 hover:bg-cloud-blue p-1.5 rounded-full">
                    <i class="fa-solid fa-xmark text-lg px-0.5"></i>
                </button>
            </div>
            
            <form action="{{ route('management.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy text-sm p-3 outline-none transition-all" placeholder="สมชาย ใจดี">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5">ชื่อผู้ใช้ (Username) <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy text-sm p-3 outline-none transition-all" placeholder="somchai01">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5">อีเมล <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy text-sm p-3 outline-none transition-all" placeholder="somchai@example.com">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-deep-navy mb-1.5">รหัสผ่าน <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required minlength="6" class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy text-sm p-3 outline-none transition-all" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-deep-navy mb-1.5">ยืนยันรหัสผ่าน <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required minlength="6" class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy text-sm p-3 outline-none transition-all" placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5">บทบาท (Role) <span class="text-red-500">*</span></label>
                        <select name="role_id" required class="block w-full border-cloud-blue rounded-xl shadow-sm focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue text-deep-navy font-medium text-sm p-3 outline-none transition-all bg-cloud-blue/10">
                            <option value="" hidden>-- เลือกบทบาท --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-4 border-t border-cloud-blue">
                    <button type="button" class="btn-close-add-modal flex-1 bg-cloud-blue/30 text-deep-navy py-2.5 rounded-xl font-bold hover:bg-cloud-blue transition-colors">ยกเลิก</button>
                    <button type="submit" class="flex-1 bg-royal-blue hover:bg-deep-navy text-pure-white py-2.5 rounded-xl font-bold shadow-md transition-colors">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-sm transform transition-all scale-100 text-center border border-cloud-blue">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 mb-5 border border-red-200">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-red-500"></i>
            </div>
            <h3 class="text-xl font-bold text-deep-navy mb-2">ยืนยันการลบ?</h3>
            <p class="text-sm text-slate-grey mb-4 font-medium">การกระทำนี้ไม่สามารถย้อนกลับได้</p>

            <div class="mb-6 p-3 bg-cloud-blue/20 rounded-xl border border-cloud-blue text-center">
                <p class="text-xs text-slate-grey mb-1">คุณต้องการลบผู้ใช้งาน</p>
                <p id="delete-user-name" class="font-bold text-red-600 text-sm break-words">-</p>
            </div>

            <form id="delete-user-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3 justify-center">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-pure-white py-2.5 rounded-xl font-bold shadow-md transition-colors">
                        ยืนยัน ลบเลย
                    </button>
                    <button type="button" class="btn-close-delete-modal flex-1 bg-cloud-blue/40 border border-cloud-blue text-deep-navy py-2.5 rounded-xl font-bold hover:bg-cloud-blue transition-colors">
                        ยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="restore-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-sm transform transition-all scale-100 text-center border border-cloud-blue">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 mb-5 border border-emerald-200">
                <i class="fa-solid fa-rotate-left text-3xl text-emerald-500"></i>
            </div>
            <h3 class="text-xl font-bold text-deep-navy mb-2">ยืนยันการเปิดใช้งาน?</h3>
            <p class="text-sm text-slate-grey mb-6 leading-relaxed font-medium">
                คุณต้องการเปิดการใช้งานผู้ใช้ <br><span id="restore-user-name" class="font-bold text-royal-blue text-base"></span><br>อีกครั้งใช่หรือไม่?
            </p>

            <form id="restore-user-form" method="POST">
                @csrf
                <div class="flex gap-3 justify-center">
                    <button type="button" class="btn-close-restore-modal flex-1 bg-cloud-blue/40 border border-cloud-blue text-deep-navy py-2.5 rounded-xl font-bold hover:bg-cloud-blue transition-colors">
                        ยกเลิก
                    </button>
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-pure-white py-2.5 rounded-xl font-bold shadow-md transition-colors">
                        ยืนยัน
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success') || session('error'))
        <div id="popup-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-sm p-8 mx-4 bg-pure-white shadow-2xl rounded-2xl transform transition-all scale-100 border border-cloud-blue">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-5 border {{ session('error') ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                        @if(session('error'))
                            <i class="fa-solid fa-xmark text-3xl text-red-500"></i>
                        @else
                            <i class="fa-solid fa-check text-3xl text-green-500"></i>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-deep-navy mb-2">
                        {{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}
                    </h3>
                    <p class="text-sm text-slate-grey mb-8 font-medium">
                        {{ session('success') ?? session('error') }}
                    </p>
                    <button onclick="document.getElementById('popup-notification').remove()" 
                        class="w-full inline-flex justify-center rounded-xl px-4 py-3 text-sm font-bold text-pure-white shadow-sm transition-all duration-200 focus:outline-none focus:ring-4
                        {{ session('error') ? 'bg-red-600 hover:bg-red-700 focus:ring-red-200' : 'bg-royal-blue hover:bg-deep-navy focus:ring-royal-blue/30' }}">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>
    @endif

</body>
</html>