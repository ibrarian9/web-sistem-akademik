<!-- Form Floating Modal (Create / Edit Siswa) -->
<x-floating-card 
    :show="$isFormOpen ? true : false"
    :title="$siswaId ? 'Edit Data Siswa & Penempatan Rombel' : 'Tambah Santri Baru'"
    subtitle="Kelola biodata lengkap santri, penempatan rombel kelas umum & tahfizh, serta penugasan guru pendamping khusus (ABK)."
    badge="DATA SISWA"
    badgeVariant="emerald"
    icon="user-check"
    maxWidth="max-w-4xl"
    closeAction="$set('isFormOpen', false)"
>
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-2 text-xs shadow-2xs mb-4">
            <div class="flex items-center gap-2 font-extrabold text-rose-900">
                <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 shrink-0" />
                <span>Mohon Perbaiki Isian Formulir Berikut:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] font-bold text-rose-700 space-y-0.5 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" action="javascript:void(0);" class="space-y-6 text-xs">
        
        <!-- SECTION 1: PENEMPATAN KELAS & GURU PENDAMPING -->
        <div class="bg-gradient-to-br from-stone-50 via-white to-stone-50 border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-200/80 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="p-1.5 bg-emerald-100 text-emerald-800 rounded-lg">
                        <x-lucide-layers class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">1. Penempatan Rombel & Pembimbing</h4>
                        <p class="text-[11px] text-stone-500 font-medium">Tentukan rombel kelas reguler, kelompok halaqah tahfizh, dan guru pendamping.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-full border border-stone-200 w-fit">
                    Fleksibel / Opsional
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                <!-- 1. Kelas Umum -->
                <div class="p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                    <div class="space-y-0.5">
                        <label class="text-[11px] font-bold text-emerald-950 uppercase flex items-center gap-1.5">
                            <x-lucide-book-open class="w-3.5 h-3.5 text-emerald-700 shrink-0" />
                            <span>Kelas Umum (Reguler)</span>
                        </label>
                        <p class="text-[10px] text-emerald-800/80">Wali kelas & kurikulum umum</p>
                    </div>
                    <div>
                        <select wire:model="kelas_id" class="w-full px-3 py-2 bg-white border border-emerald-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="">-- Belum Ditempatkan --</option>
                            @foreach ($kelasesUmum as $kls)
                                <option value="{{ $kls->id }}">Kelas {{ $kls->nama_kelas }} (Wali: {{ $kls->guruUmum->user->nama ?? 'Admin' }})</option>
                            @endforeach
                        </select>
                        @error('kelas_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- 2. Kelas Tahfizh -->
                <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                    <div class="space-y-0.5">
                        <label class="text-[11px] font-bold text-amber-950 uppercase flex items-center gap-1.5">
                            <x-lucide-bookmark class="w-3.5 h-3.5 text-amber-700 shrink-0" />
                            <span>Kelas Tahfizh (Halaqah)</span>
                        </label>
                        <p class="text-[10px] text-amber-800/80">Kelompok setoran Al-Qur'an</p>
                    </div>
                    <div>
                        <select wire:model="kelas_tahfidz_id" class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-amber-600 shadow-2xs">
                            <option value="">-- Belum Ditempatkan --</option>
                            @foreach ($kelasesTahfidz as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} (Pengampu: {{ $kls->guruTahfidz->user->nama ?? 'Admin' }})</option>
                            @endforeach
                        </select>
                        @error('kelas_tahfidz_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- 3. Guru Pendamping Khusus (Shadow Teacher) -->
                <div class="p-3 bg-indigo-50/60 border border-indigo-200/80 rounded-xl space-y-2 flex flex-col justify-between">
                    <div class="space-y-0.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-indigo-950 uppercase flex items-center gap-1.5">
                                <x-lucide-user-check class="w-3.5 h-3.5 text-indigo-700 shrink-0" />
                                <span>Guru Pendamping (Shadow)</span>
                            </label>
                            <span class="text-[9px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded border border-indigo-200">ABK</span>
                        </div>
                        <p class="text-[10px] text-indigo-800/80">Khusus murid berkebutuhan khusus</p>
                    </div>
                    <div>
                        <select wire:model="shadow_teacher_id" class="w-full px-3 py-2 bg-white border border-indigo-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                            <option value="">-- Tanpa Guru Pendamping --</option>
                            @forelse ($shadowTeachers as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->user->nama ?? 'Guru' }} — Guru Pendamping ({{ $guru->nip ?: 'NIP -' }})</option>
                            @empty
                                <option value="" disabled>Belum ada guru berkategori Pendamping (Shadow Teacher)</option>
                            @endforelse
                        </select>
                        @error('shadow_teacher_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: IDENTITAS POKOK SISWA -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                <div class="p-1.5 bg-stone-100 text-stone-700 rounded-lg">
                    <x-lucide-user class="w-4 h-4" />
                </div>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">2. Data Pokok & Identitas Siswa</h4>
                    <p class="text-[11px] text-stone-500 font-medium">Informasi nama resmi, nomor induk, jenis kelamin, serta status keaktifan sekolah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                <!-- Nama Lengkap (col-span-2) -->
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap Siswa <span class="text-rose-600">*</span></label>
                    <input wire:model="nama" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: Muhammad Al-Fatih" required />
                    @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin <span class="text-rose-600">*</span></label>
                    <select wire:model="jenis_kelamin" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="L">Laki-laki (Ikhwan)</option>
                        <option value="P">Perempuan (Akhwat)</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIS -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NIS (Nomor Induk Siswa) <span class="text-rose-600">*</span></label>
                    <input wire:model="nis" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: 1001" required />
                    @error('nis') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NISN -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NISN <span class="text-stone-400 text-[10px] font-normal lowercase">(opsional)</span></label>
                    <input wire:model="nisn" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: 009812345" />
                    @error('nisn') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Masuk -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk Sekolah <span class="text-rose-600">*</span></label>
                    <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                    @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tempat Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                    <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Yogyakarta" />
                    @error('tempat_lahir') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                    <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('tanggal_lahir') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Status Keaktifan (hanya saat edit) -->
                @if ($siswaId)
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Status Keaktifan</label>
                        <select wire:model="status" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="aktif">Aktif Belajar</option>
                            <option value="lulus">Lulus (Alumni)</option>
                            <option value="pindah">Pindah Sekolah</option>
                            <option value="keluar">Keluar / DO</option>
                        </select>
                        @error('status') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 3: DATA WALI MURID & ALAMAT -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                <div class="p-1.5 bg-stone-100 text-stone-700 rounded-lg">
                    <x-lucide-home class="w-4 h-4" />
                </div>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">3. Data Orang Tua / Wali & Alamat Tinggal</h4>
                    <p class="text-[11px] text-stone-500 font-medium">Informasi wali murid untuk notifikasi kehadiran, tagihan SPP, dan laporan akademik.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Nama Wali -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap Orang Tua / Wali</label>
                    <input wire:model="nama_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: Hendra Gunawan" />
                    @error('nama_wali') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- No HP Wali -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">No. WhatsApp / HP Wali Murid</label>
                    <input wire:model="no_hp_wali" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="081234567890" />
                    @error('no_hp_wali') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Alamat Rumah (col-span-2) -->
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap Rumah</label>
                    <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none" placeholder="Alamat rumah lengkap santri beserta kota/kabupaten..."></textarea>
                    @error('alamat') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 4: AKUN LOGIN PORTAL SANTRI/WALI -->
        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2.5 border-b border-stone-200/80 pb-3">
                <div class="p-1.5 bg-stone-200 text-stone-800 rounded-lg">
                    <x-lucide-key-round class="w-4 h-4" />
                </div>
                <div>
                    <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">4. Akun Login Portal Siswa & Wali</h4>
                    <p class="text-[11px] text-stone-500 font-medium">Akun kredensial untuk siswa dan orang tua melihat buku rapor, absensi, dan tagihan keuangan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Username -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                    <input wire:model="username" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="fauzi1001" required />
                    @error('username') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Email <span class="text-stone-400 text-[10px] font-normal lowercase">(opsional)</span></label>
                    <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="siswa@mail.com" />
                    @error('email') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">
                        {{ $siswaId ? 'Ganti Password' : 'Password Login' }}
                        @if(!$siswaId)<span class="text-rose-600">*</span>@endif
                    </label>
                    <input wire:model="password" type="password" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="{{ $siswaId ? '•••••• (Kosongkan bila tetap)' : 'Min. 6 karakter' }}" {{ !$siswaId ? 'required' : '' }} />
                    <p class="text-[10px] text-stone-500 italic">
                        {{ $siswaId ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 6 karakter.' }}
                    </p>
                    @error('password') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Modal Action Buttons Footer -->
        <div class="flex items-center justify-between gap-3 border-t border-stone-200 pt-4">
            <span class="text-[11px] text-stone-500 font-medium">
                Tanda <span class="text-rose-600 font-black">*</span> wajib diisi
            </span>
            <div class="flex items-center gap-2.5">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    {{ $siswaId ? 'Simpan Perubahan' : 'Daftarkan Siswa' }}
                </x-button>
            </div>
        </div>
    </form>
</x-floating-card>
