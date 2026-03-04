<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ | ELO Generator</title>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="text-gray-800">

    @include('component.navbar') 

    <div class="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 flex justify-center">
        
        <div class="w-full max-w-2xl">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl font-bold text-deep-navy">แก้ไขโปรไฟล์</h1>
                <p class="mt-2 text-sm text-sky-surge">จัดการข้อมูลส่วนตัวของคุณ</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6 mb-0 rounded-r-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-check text-green-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" 
                    class="p-6 sm:p-8 space-y-8"
                    x-data="{
                        initialName: '{{ Auth::user()->name }}',
                        initialEmail: '{{ Auth::user()->email }}',
                        name: '{{ Auth::user()->name }}',
                        email: '{{ Auth::user()->email }}',
                        photoName: null,
                        photoPreview: null,
                        hasChanged() {
                            return this.name !== this.initialName || 
                                    this.email !== this.initialEmail || 
                                    this.photoName !== null;
                        }
                    }">
                    
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pb-8 border-b border-gray-100">
                        <div class="relative group">
                            <div class="h-28 w-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-100 flex items-center justify-center relative cursor-pointer"
                                @click="$refs.photo.click()">
                                
                                <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600 text-4xl font-bold">
                                    @if(Auth::user()->profile)
                                        <img src="{{ asset('storage/' . Auth::user()->profile) }}" class="h-full w-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                
                                <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover" style="display: none;">
                                
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-camera text-white text-xl"></i>
                                </div>
                            </div>

                            <input type="file" id="photo" name="profile" class="hidden" accept="image/*"
                                x-ref="photo"
                                @change="
                                    const file = $refs.photo.files[0];
                                    if (file) {
                                        photoName = file.name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                ">
                        </div>

                        <div class="text-center sm:text-left flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">รูปโปรไฟล์</h3>
                            <p class="text-sm text-gray-500 mb-4">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</p>
                            
                            <button type="button" @click="$refs.photo.click()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fa-solid fa-upload mr-2"></i> อัปโหลดรูปใหม่
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-regular fa-id-card text-gray-400"></i> ข้อมูลส่วนตัว
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                                <input type="text" name="name" id="name" x-model="name"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 border transition-colors">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                                <i class="text-xs text-gray-500">(ใช้สำหรับเข้าสู่ระบบ และ รับการแจ้งเตือน)</i>
                                <input type="email" name="email" id="email" x-model="email"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 border transition-colors">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-500 mb-1">ชื่อผู้ใช้</label>
                                <i class="text-xs text-gray-500">(ชื่อผู้ใช้ไม่สามารถแก้ไขได้)</i>
                                <div class="block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-500 sm:text-sm py-2.5 px-3 select-none">
                                    {{ Auth::user()->username }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ url('/') }}" class="px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                            ยกเลิก
                        </a>
                        
                        <button type="submit" 
                            :disabled="!hasChanged()"
                            :class="{ 'opacity-50 cursor-not-allowed bg-gray-400': !hasChanged(), 'bg-[#035AA6] hover:bg-[#6CBAD9] shadow-sm': hasChanged() }"
                            class="inline-flex justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>