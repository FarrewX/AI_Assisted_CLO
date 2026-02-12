<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ELO_Generator</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/professor_course.js'])
        @else
        @endif
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container .select2-selection--single { height: 42px; display: flex; align-items: center; border-color: #d1d5db; border-radius: 0.5rem; }
            .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
        </style>
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased">
        @include('component.navbar')

        <div class="container mx-auto px-4 py-8 pt-24 max-w-6xl">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">จัดการอาจารย์รายวิชา</h1>
                    <p class="text-gray-500 text-sm mt-1">กำหนดอาจารย์ผู้สอนรายวิชาในหลักสูตร</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <form method="GET" action="{{ route('courses') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">เลือกหลักสูตร</label>
                        <select id="curriculum" name="curriculum_id" class="w-full">
                            <option value="">-- เลือกหลักสูตร --</option>
                            @foreach($curriculum as $cur)
                                <option value="{{ $cur->id }}" {{ $curriculumId == $cur->id ? 'selected' : '' }}>
                                    {{ $cur->curriculum_year }} - {{ $cur->curriculum_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request('curriculum_id'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">เลือกรายวิชา</label>
                            @if(count($courses) > 0)
                                <select id="course" name="course_id" class="w-full">
                                    <option value="">-- เลือกรายวิชา --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_code }} {{ $course->course_name_th }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    ไม่พบรายวิชาในหลักสูตรนี้
                                </div>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

            @if(isset($professor))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h2 class="font-bold text-gray-800">รายชื่ออาจารย์ผู้สอน</h2>
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ count($professor) }} ท่าน</span>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">อาจารย์</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">ปีการศึกษา</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">ภาคเรียน</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">มคอ.</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @forelse($professor as $t)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                                            {{ substr($t->user->name ?? 'U', 0, 1) }}
                                                        </div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $t->user->name ?? 'Unknown' }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                                    {{ $t->year < 2400 ? $t->year + 543 : $t->year }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                                    {{ $t->term }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $t->TQF }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                                    <button type="button" class="edit-btn text-amber-500 hover:text-amber-700 transition p-1 rounded-md hover:bg-amber-50"
                                                        data-id="{{ $t->id }}"
                                                        data-user-id="{{ $t->user_id }}"
                                                        data-year="{{ $t->year < 2400 ? $t->year + 543 : $t->year }}"
                                                        data-term="{{ $t->term }}"
                                                        data-tqf="{{ $t->TQF }}"
                                                        data-update-url="{{ route('professor.update', [$courseId, $t->id]) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </button>
                                                    
                                                    <form method="POST" action="{{ route('professor.destroy', [$courseId, $t->id]) }}" class="inline-block delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="delete-btn text-red-500 hover:text-red-700 transition p-1 rounded-md hover:bg-red-50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                                    ยังไม่มีข้อมูลอาจารย์ผู้สอนในรายวิชานี้
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                เพิ่มอาจารย์ผู้สอน
                            </h2>
                            
                            <form method="POST" action="{{ route('professor.store', $courseId) }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="curriculum_id" value="{{ $curriculumId }}">
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">เลือกอาจารย์</label>
                                    <select name="user_id" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($users as $user)
                                            <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">ปีการศึกษา</label>
                                        <input type="number" name="year" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                               value="{{ date('Y') + 543 }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">ภาคเรียน</label>
                                        <select name="term" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">มคอ.</label>
                                    <select name="TQF" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="3">มคอ. 3</option>
                                        <option value="5" disabled>มคอ. 5</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                    บันทึกข้อมูล
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="edit-modal" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 hidden backdrop-blur-sm transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all scale-100">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">แก้ไขข้อมูล</h3>
                    <p class="text-sm text-gray-500">แก้ไขรายละเอียดการสอน</p>
                </div>
                
                <form id="edit-form" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="curriculum_id" value="{{ $curriculumId ?? '' }}">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 text-left">อาจารย์ผู้สอน</label>
                        <select name="user_id" id="edit-user-input" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 text-left">ปีการศึกษา</label>
                            <input type="number" name="year" id="edit-year-input" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 text-left">ภาคเรียน</label>
                            <select name="term" id="edit-term-input" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 text-left">มคอ.</label>
                        <select name="TQF" id="edit-TQF-input" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="3">มคอ. 3</option>
                            <option value="5" disabled>มคอ. 5</option>
                        </select>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" id="edit-cancel" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2.5 rounded-xl font-medium transition">ยกเลิก</button>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium shadow-md transition">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 hidden backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center transform transition-all scale-100">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">ยืนยันการลบ</h3>
                <p class="text-gray-500 text-sm mb-6">คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                <div class="flex gap-3">
                    <button id="delete-cancel" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2.5 rounded-xl font-medium transition">ยกเลิก</button>
                    <button id="delete-confirm" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl font-medium shadow-md transition">ลบข้อมูล</button>
                </div>
            </div>
        </div>

        @if ($errors->any())
        <div id="error-modal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-[60] backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center mx-4 transform scale-100">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">เกิดข้อผิดพลาด</h3>
                <ul class="text-sm text-red-600 bg-red-50 p-3 rounded-lg text-left list-disc pl-5 space-y-1 mb-6 border border-red-100">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button onclick="closeSessionModal()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl font-medium shadow-sm transition">ตกลง</button>
            </div>
        </div>
        @endif

        @if(session('success') || session('error'))
        <div id="session-modal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-[60] backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center mx-4 transform scale-100">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 {{ session('error') ? 'bg-red-100' : 'bg-green-100' }}">
                    @if(session('error'))
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @else
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}</h3>
                <p class="text-gray-500 text-sm mb-6">{{ session('success') ?? session('error') }}</p>
                <button onclick="closeSessionModal()" class="w-full py-2.5 rounded-xl font-medium shadow-sm transition text-white {{ session('error') ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                    ตกลง
                </button>
            </div>
        </div>
        @endif

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </body>
</html>