<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        :title="$canManage ? 'Kelola Jadwal Piket & Jam Masuk Guru' : 'Jadwal Piket Guru'" 
        subtitle="Pengaturan jam check-in piket ({{ $jamMasukPiket }} WIB) & non-piket ({{ $jamMasukNonPiket }} WIB) serta penugasan piket harian."
        badge="JADWAL PIKET GURU"
        badgeVariant="emerald"
        icon="clock"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        :title="$canManage ? 'Petunjuk Manajemen Jadwal Piket Guru & Jam Masuk' : 'Petunjuk Jadwal Piket Guru'"
        :steps="$canManage ? [
            ['title' => 'Pengaturan Jam Masuk Fleksibel', 'desc' => 'Staf Tata Usaha dapat mengubah target jam check-in piket (default 06:30) & non-piket (default 06:45) secara dinamis.'],
            ['title' => 'Penugasan Piket Harian', 'desc' => 'Pilih nama guru dan hari piket (Senin - Jumat) lalu klik Simpan Jadwal Piket.'],
            ['title' => 'Hapus Penugasan', 'desc' => 'Klik tombol Hapus pada nama guru di kolom hari untuk menghapus jadwal piket.']
        ] : [
            ['title' => 'Jadwal Harian', 'desc' => 'Lihat penugasan piket guru per hari kerja (Senin - Jumat).'],
            ['title' => 'Ketentuan Jam Hadir', 'desc' => 'Batas jam check-in guru piket dan non-piket ditentukan oleh Tata Usaha.']
        ]"
        notes="Sistem absensi mandiri guru secara otomatis menyesuaikan batas keterlambatan berdasarkan penugasan piket ini."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    @if ($canManage)
    <!-- FLEXIBLE CHECK-IN TIME CONFIGURATION CARD -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-clock class="w-4 h-4 text-emerald-600" />
                <span>Pengaturan Fleksibel Jam Check-In Presensi Guru</span>
            </h3>
            <x-badge variant="emerald" size="xs">Wewenang Tata Usaha</x-badge>
        </div>

        <form wire:submit.prevent="updateJamSettings" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">1. Target Jam Guru Piket</label>
                <input type="time" wire:model="jamMasukPiket" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 06:30 WIB</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">2. Target Jam Guru Non-Piket</label>
                <input type="time" wire:model="jamMasukNonPiket" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 06:45 WIB</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">3. Target Jam Guru Umum</label>
                <input type="time" wire:model="jamMasukGuruUmum" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                <span class="text-[10px] text-stone-500 font-medium block">Default: 09:30 WIB</span>
            </div>

            <div class="space-y-1.5">
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="updateJamSettings" class="w-full">
                    Simpan Jam Masuk
                </x-button>
            </div>
        </form>
    </div>

    <!-- ADD PIKET FORM CARD -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4 text-emerald-600" />
            <span>Tambah Penugasan Piket Baru</span>
        </h3>

        <form wire:submit.prevent="addPiket" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">Pilih Guru <span class="text-rose-600">*</span></label>
                <select wire:model="selectedGuruId" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} ({{ ucfirst($g->jenis_guru) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase">Pilih Hari Piket <span class="text-rose-600">*</span></label>
                <select wire:model="selectedHari" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600 uppercase shadow-2xs">
                    @foreach ($days as $d)
                        <option value="{{ $d }}">{{ strtoupper($d) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <x-button type="submit" variant="primary" size="md" icon="plus" loadingTarget="addPiket" class="w-full">
                    Simpan Penugasan
                </x-button>
            </div>
        </form>
    </div>
    @endif

    <!-- DAYS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach ($days as $day)
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                    <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">{{ strtoupper($day) }}</h4>
                    <x-badge variant="emerald" size="xs">
                        {{ count($piketSchedules[$day] ?? []) }} Guru
                    </x-badge>
                </div>

                <div class="space-y-2">
                    @forelse ($piketSchedules[$day] ?? [] as $p)
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between gap-2 group hover:border-emerald-300 transition">
                            <div class="min-w-0">
                                <h5 class="text-xs font-extrabold text-stone-900 leading-tight truncate">{{ $p->guru->user->nama ?? '-' }}</h5>
                                <span class="text-[10px] text-emerald-700 font-bold uppercase block mt-0.5">{{ $p->guru->jenis_guru ?? 'guru' }}</span>
                            </div>
                            @if ($canManage)
                                <x-button variant="danger" size="xs" icon="trash-2" wire:click="deletePiket({{ $p->id }})" title="Hapus Jadwal Piket {{ $p->guru->user->nama ?? '' }}">
                                    Hapus
                                </x-button>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-stone-400 italic">
                            Belum ada piket.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
