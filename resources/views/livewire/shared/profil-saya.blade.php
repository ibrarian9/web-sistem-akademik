<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Profil Pengguna & Kelembagaan"
        :steps="[
            ['title' => 'Biodata & Identitas Akun', 'desc' => 'Perbarui nama lengkap, email resmi, NIP, serta jabatan kedinasan resmi Anda.'],
            ['title' => 'Identitas Sekolah / Instansi', 'desc' => 'Kelola nama resmi sekolah, alamat instansi, dan nomor kontak yang tercantum pada kop surat PDF resmi.'],
            ['title' => 'Keamanan Password', 'desc' => 'Ubah password akun secara berkala untuk menjaga kerahasiaan hak akses sistem.']
        ]"
    />

    <!-- Header -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 font-black text-xl flex items-center justify-center border border-emerald-200 shadow-2xs">
                {{ strtoupper(substr($nama ?: 'U', 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-stone-900">{{ $nama }}</h2>
                <p class="text-xs text-stone-500 font-medium">@ {{ $username }} • <span class="capitalize bg-stone-100 px-2.5 py-0.5 rounded-lg text-stone-700 font-bold">{{ str_replace('_', ' ', Auth::user()->role->nama ?? 'User') }}</span></p>
            </div>
        </div>
        <div class="text-xs text-stone-500 bg-stone-50 px-4 py-2.5 rounded-xl border border-stone-200">
            NIP / ID: <span class="font-bold text-stone-800">{{ $nip ?: '-' }}</span> • Jabatan: <span class="font-bold text-stone-800">{{ $jabatan ?: '-' }}</span>
        </div>
    </div>

    <!-- Alert Banners -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('school_message'))
        <x-alert-banner type="success" :message="session('school_message')" />
    @endif

    @if (session()->has('password_message'))
        <x-alert-banner type="success" :message="session('password_message')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Column: Profil Pengguna & Pengaturan Sekolah -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Form: Profil Pengguna Pribadi -->
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-6">
                <div class="border-b border-stone-200 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                            <x-lucide-user class="w-4 h-4 text-emerald-600" />
                            <span>Informasi Profil Pengguna</span>
                        </h3>
                        <p class="text-xs text-stone-500 mt-0.5">Kelola data diri pribadi, NIP, dan jabatan kedinasan resmi Anda.</p>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                        Akun Saya
                    </span>
                </div>

                <form wire:submit.prevent="saveProfile" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                            <input wire:model="nama" type="text" placeholder="Contoh: Siti Aminah, S.E." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            @error('nama') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Alamat Email Resmi</label>
                            <input wire:model="email" type="email" placeholder="nama@sekolah.sch.id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            @error('email') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIP / NIK -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">NIP / NIK / ID Resmi</label>
                            <input wire:model="nip" type="text" placeholder="Contoh: 19820415 200801 2 004" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs font-mono" />
                            @error('nip') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Jabatan Kedinasan</label>
                            <input wire:model="jabatan" type="text" placeholder="Contoh: Bendahara Keuangan / Staf Tata Usaha" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            @error('jabatan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 border-t border-stone-100 flex justify-end">
                        <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="saveProfile">
                            Simpan Perubahan Profil
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Form: Pengaturan Identitas Sekolah / Instansi (Bendahara & Tata Usaha & Super Admin) -->
            @if ($canEditSchool)
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-6">
                    <div class="border-b border-stone-200 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                                <x-lucide-building-2 class="w-4 h-4 text-emerald-600" />
                                <span>Identitas & Informasi Sekolah / Lembaga</span>
                            </h3>
                            <p class="text-xs text-stone-500 mt-0.5">Nama dan alamat sekolah ini akan dicantumkan secara otomatis pada kop surat seluruh laporan resmi & PDF.</p>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                            Otoritas Kelembagaan
                        </span>
                    </div>

                    <form wire:submit.prevent="saveSchoolProfile" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Sekolah -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Nama Resmi Sekolah / Yayasan</label>
                                <input wire:model="nama_sekolah" type="text" placeholder="Contoh: PONDOK PESANTREN & SEKOLAH ISLAM TERPADU" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                                @error('nama_sekolah') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- No Telepon -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Nomor Telepon / Kontak Resmi</label>
                                <input wire:model="no_telepon" type="text" placeholder="Contoh: (0761) 123456 / 08123456789" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                                @error('no_telepon') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Alamat Lengkap -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Alamat Lengkap Instansi / Sekolah</label>
                                <textarea wire:model="alamat_sekolah" rows="2" placeholder="Contoh: Jl. Pendidikan Karakter Islami, Pekanbaru, Riau" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                                @error('alamat_sekolah') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3 border-t border-stone-100 flex items-center justify-between gap-3">
                            <div class="text-[11px] text-stone-500 flex items-center gap-1.5">
                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                <span>Perubahan langsung tersinkron ke kop surat & tanda tangan PDF</span>
                            </div>
                            <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="saveSchoolProfile">
                                Simpan Informasi Sekolah
                            </x-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar: Form Change Password -->
        <div class="space-y-6">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="border-b border-stone-200 pb-2">
                    <h3 class="text-sm font-bold text-stone-900 flex items-center gap-2">
                        <x-lucide-key class="w-4 h-4 text-amber-600" />
                        <span>Ganti Password Akun</span>
                    </h3>
                    <p class="text-[11px] text-stone-500 mt-0.5">Amankan akun Anda dengan password kombinasi.</p>
                </div>
                
                <form wire:submit.prevent="updatePassword" class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Password Saat Ini</label>
                        <input wire:model="current_password" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        @error('current_password') <span class="text-rose-600 text-[11px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Password Baru</label>
                        <input wire:model="new_password" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        @error('new_password') <span class="text-rose-600 text-[11px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <input wire:model="new_password_confirmation" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="secondary" size="md" icon="lock" loadingTarget="updatePassword" class="w-full">
                            Perbarui Password
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
