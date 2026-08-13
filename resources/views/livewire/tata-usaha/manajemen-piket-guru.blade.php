<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        :title="$canManage ? 'Petunjuk Manajemen Jadwal Piket Guru & Jam Masuk' : 'Petunjuk Jadwal Piket Guru'"
        :steps="$canManage ? [
            ['title' => 'Pengaturan Jam Masuk Fleksibel', 'desc' => 'Staf Tata Usaha dapat mengubah target jam check-in piket (default 06:30) & non-piket (default 06:45) secara dinamis.'],
            ['title' => 'Penugasan Piket Harian', 'desc' => 'Pilih nama guru dan hari piket (Senin - Jumat) lalu klik Simpan Jadwal Piket.'],
            ['title' => 'Hapus Penugasan', 'desc' => 'Klik ikon tempat sampah pada nama guru di kolom hari untuk menghapus jadwal piket.']
        ] : [
            ['title' => 'Jadwal Harian', 'desc' => 'Lihat penugasan piket guru per hari kerja (Senin - Jumat).'],
            ['title' => 'Ketentuan Jam Hadir', 'desc' => 'Batas jam check-in guru piket dan non-piket ditentukan oleh Tata Usaha.']
        ]"
        notes="Sistem absensi mandiri guru secara otomatis menyesuaikan batas keterlambatan berdasarkan penugasan piket ini."
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">{{ $canManage ? 'Kelola Jadwal Piket & Jam Masuk Guru' : 'Jadwal Piket Guru' }}</h2>
            <p class="text-xs text-stone-500">
                Pengaturan jam check-in piket ({{ $jamMasukPiket }} WIB) &amp; non-piket ({{ $jamMasukNonPiket }} WIB) serta penugasan piket harian.
            </p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-alert-triangle class="w-4 h-4 text-rose-600" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($canManage)
    <!-- FLEXIBLE CHECK-IN TIME CONFIGURATION CARD -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-clock class="w-4 h-4 text-emerald-600" />
                <span>Pengaturan Fleksibel Jam Check-In Presensi Guru</span>
            </h3>
            <span class="px-2.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-[10px] font-bold">
                Wewenang Tata Usaha
            </span>
        </div>

        <form wire:submit.prevent="updateJamSettings" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">1. Target Jam Guru Piket</label>
                <input type="time" wire:model="jamMasukPiket" class="w-full bg-stone-50 border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 h-[38px]" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 06:30 WIB</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">2. Target Jam Guru Non-Piket</label>
                <input type="time" wire:model="jamMasukNonPiket" class="w-full bg-stone-50 border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 h-[38px]" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 06:45 WIB</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">3. Target Jam Guru Umum</label>
                <input type="time" wire:model="jamMasukGuruUmum" class="w-full bg-stone-50 border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 h-[38px]" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 09:30 WIB</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase opacity-0 pointer-events-none hidden md:block">Aksi</label>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-md h-[38px]">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Simpan Jam Masuk</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ADD PIKET FORM CARD -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4 text-indigo-600" />
            <span>Tambah Penugasan Piket Baru</span>
        </h3>

        <form wire:submit.prevent="addPiket" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-600">Pilih Guru</label>
                <select wire:model="selectedGuruId" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500 font-semibold h-[38px]">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} ({{ ucfirst($g->jenis_guru) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-600">Pilih Hari Piket</label>
                <select wire:model="selectedHari" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500 uppercase font-semibold h-[38px]">
                    @foreach ($days as $d)
                        <option value="{{ $d }}">{{ strtoupper($d) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-600 opacity-0 pointer-events-none hidden md:block">Aksi</label>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm h-[38px]">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Simpan Penugasan Piket</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- DAYS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach ($days as $day)
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                    <h4 class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider">{{ strtoupper($day) }}</h4>
                    <span class="px-2 py-0.5 bg-stone-100 text-stone-600 rounded text-[10px] font-bold">
                        {{ count($piketSchedules[$day] ?? []) }} Guru
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse ($piketSchedules[$day] ?? [] as $p)
                        <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between group">
                            <div>
                                <h5 class="text-xs font-bold text-stone-800 leading-tight">{{ $p->guru->user->nama ?? '-' }}</h5>
                                <span class="text-[9px] text-indigo-600 font-semibold uppercase">{{ $p->guru->jenis_guru ?? 'guru' }}</span>
                            </div>
                            @if ($canManage)
                            <button wire:click="deletePiket({{ $p->id }})" class="text-stone-400 hover:text-rose-600 p-1 rounded-lg transition" title="Hapus Jadwal Piket">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            </button>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-[10px] text-stone-400 italic">
                            Belum ada piket.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
