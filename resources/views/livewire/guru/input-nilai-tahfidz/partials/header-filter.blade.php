<!-- Quick Module Switcher Navigation -->
<x-guru-module-switcher active="tahfidz" />

<!-- Info & Tutorial Box -->
<x-info-tutorial-box 
    title="Petunjuk Pengisian Mutaba'ah Harian Guru Tahfizh SD TAHFIZH F3"
    :steps="[
        ['title' => 'Pilih Tanggal Sekolah', 'desc' => 'Tentukan tanggal pengisian setoran harian (default: Hari Ini). Anda juga dapat melihat rekap Mingguan & Bulanan.'],
        ['title' => 'Isi Mutaba\'ah Santri', 'desc' => 'Klik tombol + Input Mutaba\'ah Santri atau klik baris nama santri untuk menginput Tahsin, Muraja\'ah, Kitabah, & Ziyadah.'],
        ['title' => 'Otomatis Terintegrasi', 'desc' => 'Setoran harian otomatis tersimpan per tanggal dan terakumulasi ke lembar Rapor Tahfizh & Portal Wali Murid.']
    ]"
    notes="Gunakan Tab Breakdown (Harian, Mingguan, Bulanan) untuk melihat rekapitulasi keaktifan setoran santri."
/>

<!-- Main Control Header Card -->
<div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-6">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MUTABA'AH HARIAN GURU TAHFIZH
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Lembar Mutaba'ah & Setoran Hafalan Santri</h1>
            <p class="text-stone-600 text-xs font-semibold mt-1">Pencatatan setoran harian per tanggal sekolah: Tahsin, Muraja'ah, Kitabah, dan Ziyadah.</p>
        </div>
        <x-button variant="primary" size="md" icon="plus" wire:click.prevent="openScoreModal" class="self-start lg:self-auto">
            Input Mutaba'ah Santri
        </x-button>
    </div>

    <!-- Filter Controls & Date Selector -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-stone-200">
        <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Halaqah Tahfizh / Kelas Bimbingan</label>
            <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }} {{ strtolower($k->jenis_kelas) === 'tahfidz' ? '(Halaqah Tahfizh)' : '(Kelas Akademik)' }} - Pengampu: {{ $k->guruTahfidz->user->nama ?? 'Admin TU' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Semester & Tahun Ajaran</label>
            <select wire:model.live="semester_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '' }} - {{ ucfirst($sem->semester) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Tanggal Setoran Sekolah</label>
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="tanggal" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                <x-button variant="secondary" size="xs" wire:click="setTanggalToday" title="Set Tanggal Hari Ini" class="whitespace-nowrap shrink-0">
                    Hari Ini
                </x-button>
                <x-button variant="secondary" size="xs" wire:click="setTanggalYesterday" title="Set Tanggal Kemarin" class="whitespace-nowrap shrink-0">
                    Kemarin
                </x-button>
            </div>
        </div>
    </div>

    <!-- Filter Livewire Loading Bar Indicator -->
    <div wire:loading.delay wire:target="kelas_id, semester_id, tanggal, search, selectedMonth, viewTab" class="w-full">
        <x-loading-state type="bar" target="kelas_id, semester_id, tanggal, search, selectedMonth, viewTab" />
    </div>
</div>
