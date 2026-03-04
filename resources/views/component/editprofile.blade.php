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
<body class="font-sans antialiased">
    @include('component.navbar') 

    <div class="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 flex justify-center">
        
        <div class="w-full max-w-2xl">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl font-bold text-deep-navy">แก้ไขโปรไฟล์</h1>
                <p class="mt-2 text-sm text-slate-grey">จัดการข้อมูลส่วนตัวของคุณ</p>
            </div>

            <div class="bg-pure-white rounded-2xl shadow-sm border border-cloud-blue overflow-hidden">
                
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

                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pb-8 border-b border-cloud-blue">
                        <div class="relative group">
                            <div class="h-28 w-28 rounded-full overflow-hidden border-4 border-pure-white shadow-md bg-cloud-blue/30 flex items-center justify-center relative cursor-pointer"
                                @click="$refs.photo.click()">
                                
                                <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center bg-cloud-blue/50 text-royal-blue text-4xl font-bold">
                                    @if(Auth::user()->profile)
                                        <img src="{{ asset('storage/' . Auth::user()->profile) }}" class="h-full w-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                
                                <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover" style="display: none;">
                                
                                <div class="absolute inset-0 bg-deep-navy/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-camera text-pure-white text-xl"></i>
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
                            <h3 class="text-lg font-semibold text-deep-navy mb-1">รูปโปรไฟล์</h3>
                            <p class="text-sm text-slate-grey mb-4">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</p>
                            
                            <button type="button" @click="$refs.photo.click()" 
                                class="inline-flex items-center px-4 py-2 border border-cloud-blue shadow-sm text-sm font-medium rounded-lg text-deep-navy bg-pure-white hover:bg-cloud-blue/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-blue transition-colors">
                                <i class="fa-solid fa-upload mr-2 text-royal-blue"></i> อัปโหลดรูปใหม่
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-deep-navy flex items-center gap-2">
                            <i class="fa-regular fa-id-card text-royal-blue"></i> ข้อมูลส่วนตัว
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="name" class="block text-sm font-medium text-deep-navy mb-1">ชื่อ-นามสกุล</label>
                                <input type="text" name="name" id="name" x-model="name"
                                    class="block w-full rounded-lg border-cloud-blue text-deep-navy shadow-sm focus:border-royal-blue focus:ring-royal-blue/30 sm:text-sm py-2.5 px-3 border transition-colors">
                                @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="email" class="block text-sm font-medium text-deep-navy mb-1">อีเมล</label>
                                <i class="text-xs text-slate-grey">(ใช้สำหรับเข้าสู่ระบบ และ รับการแจ้งเตือน)</i>
                                <input type="email" name="email" id="email" x-model="email"
                                    class="block w-full mt-1 rounded-lg border-cloud-blue text-deep-navy shadow-sm focus:border-royal-blue focus:ring-royal-blue/30 sm:text-sm py-2.5 px-3 border transition-colors">
                                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-slate-grey mb-1">ชื่อผู้ใช้</label>
                                <i class="text-xs text-slate-grey/80">(ชื่อผู้ใช้ไม่สามารถแก้ไขได้)</i>
                                <div class="block w-full mt-1 rounded-lg border border-cloud-blue bg-cloud-blue/20 text-slate-grey sm:text-sm py-2.5 px-3 select-none">
                                    {{ Auth::user()->username }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-cloud-blue flex items-center justify-end gap-3">
                        <a href="{{ url('/') }}" class="px-4 py-2.5 border border-cloud-blue shadow-sm text-sm font-medium rounded-lg text-deep-navy bg-pure-white hover:bg-cloud-blue/20 focus:outline-none transition-colors">
                            ยกเลิก
                        </a>
                        
                        <button type="submit" 
                            :disabled="!hasChanged()"
                            :class="{ 'opacity-50 cursor-not-allowed bg-slate-grey text-pure-white': !hasChanged(), 'bg-royal-blue hover:bg-deep-navy text-pure-white shadow-sm': hasChanged() }"
                            class="inline-flex justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-blue transition-colors duration-200">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>