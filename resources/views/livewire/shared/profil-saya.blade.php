<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Profil Pengguna & Keamanan"
        :steps="[
            ['title' => 'Biodata & Identitas', 'desc' => 'Perbarui nama lengkap, email resmi, NIP, serta jabatan kedinasan Anda.'],
            ['title' => 'Keamanan Password', 'desc' => 'Ubah password akun secara berkala untuk menjaga kerahasiaan hak akses.'],
            ['title' => 'Pengesahan Otomatis', 'desc' => 'Setiap dokumen resmi yang Anda terbitkan akan diverifikasi otomatis menggunakan QR Code Publik.']
        ]"
    />

    <!-- Header -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 font-black text-xl flex items-center justify-center border border-emerald-200">
                {{ strtoupper(substr($nama ?: 'U', 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-stone-800">{{ $nama }}</h2>
                <p class="text-xs text-stone-500 font-medium">@ {{ $username }} • <span class="capitalize bg-stone-100 px-2 py-0.5 rounded text-stone-700 font-semibold">{{ str_replace('_', ' ', Auth::user()->role->nama ?? 'User') }}</span></p>
            </div>
        </div>
        <div class="text-xs text-stone-500">
            NIP: <span class="font-bold text-stone-700">{{ $nip ?: '-' }}</span> • Jabatan: <span class="font-bold text-stone-700">{{ $jabatan ?: '-' }}</span>
        </div>
    </div>

    <!-- Alert Banners -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('password_message'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-semibold flex items-center justify-between">
            <span>{{ session('password_message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form: Profil Pengguna -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="border-b border-stone-200 pb-3">
                <h3 class="text-base font-bold text-stone-800">Informasi Profil Pengguna</h3>
                <p class="text-xs text-stone-500">Kelola data diri pribadi, NIP, dan jabatan kedinasan resmi Anda.</p>
            </div>

            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                        <input wire:model="nama" type="text" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-medium" />
                        @error('nama') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Alamat Email</label>
                        <input wire:model="email" type="email" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('email') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- NIP / NIK -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">NIP / NIK / ID Resmi</label>
                        <input wire:model="nip" type="text" placeholder="Contoh: 19820415 200801 2 004" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-mono" />
                        @error('nip') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Jabatan Resmi</label>
                        <input wire:model="jabatan" type="text" placeholder="Contoh: Bendahara Keuangan / Guru Pengajar" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('jabatan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-stone-200 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-emerald-600/10 flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar: Form Change Password -->
        <div class="space-y-6">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-stone-800 border-b border-stone-200 pb-2">Ganti Password Akun</h3>
                
                <form wire:submit.prevent="updatePassword" class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Password Saat Ini</label>
                        <input wire:model="current_password" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('current_password') <span class="text-red-600 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Password Baru</label>
                        <input wire:model="new_password" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('new_password') <span class="text-red-600 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Konfirmasi Password Baru</label>
                        <input wire:model="new_password_confirmation" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <x-lucide-key class="w-3.5 h-3.5" />
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
