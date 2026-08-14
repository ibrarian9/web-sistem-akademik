<div class="space-y-6 font-sans">
    @php
        $isTu = auth()->user()->role?->nama === 'tata_usaha';
        $routeKelas = $isTu ? route('tata-usaha.kelas') : route('super-admin.kelas');
        $routeSiswa = $isTu ? route('tata-usaha.siswa') : route('super-admin.siswa');
        $routePlotting = $isTu ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas');
    @endphp

    <!-- Quick Switcher Bar -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-xs">
        <a href="{{ $routeKelas }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>1. Buat &amp; Kelola Kelas (Umum &amp; Tahfizh)</span>
        </a>
        <a href="{{ $routeSiswa }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>2. Data Siswa</span>
        </a>
        <a href="{{ $routePlotting }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-sm flex items-center gap-2.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>3. Plotting Siswa Per-Kelas</span>
        </a>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Plotting / Pengelompokan Murid Per-Kelas"
        :steps="[
            ['title' => 'Pilih Kelas Target', 'desc' => 'Pilih Kelas Umum (1A, 1B, 2A, dst) atau Kelas Tahfizh (Halaqah Ustadz/ah) yang ingin dikelola.'],
            ['title' => 'Lihat Daftar Murid', 'desc' => 'Sistem langsung menampilkan daftar seluruh siswa terdaftar pada kelas tersebut.'],
            ['title' => 'Masukkan Murid Kolektif', 'desc' => 'Klik tombol Masukkan Murid Ke Kelas Ini untuk memilih & mencentang beberapa siswa sekaligus (1x simpan).']
        ]"
        notes="Setiap siswa wajib memiliki 1 Kelas Umum dan 1 Kelas Tahfizh."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Class Selector Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                    PLOTTING KELAS &amp; HALAQAH
                </span>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Daftar Anggota Kelas &amp; Input Kolektif</h1>
                <p class="text-xs text-stone-600 font-semibold mt-1">Pilih kelas di bawah ini untuk melihat murid terdaftar dan memasukkan murid secara kolektif.</p>
            </div>
            
            @if($selectedKelas)
                <button type="button" wire:click.prevent="openAddModal" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                    <x-lucide-user-plus class="w-4 h-4" />
                    <span>Masukkan Murid Ke {{ $selectedKelas->nama_kelas }}</span>
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Filter Jenis Kelas</label>
                <select wire:model.live="filter_jenis" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    <option value="semua">Semua Jenis Kelas</option>
                    <option value="umum">Kelas Umum (1 - 6)</option>
                    <option value="tahfidz">Kelas Tahfizh (Halaqah)</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Pilih Kelas Target Yang Ingin Dikelola</label>
                <select wire:model.live="selected_kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2.5 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600">
                    @forelse($kelases as $k)
                        @php
                            $isT = $k->jenis_kelas === 'tahfidz';
                            $labelJenis = $isT ? '[Tahfizh]' : '[Umum]';
                            $pengampu = $isT ? ($k->guruTahfidz->user->nama ?? 'Admin') : ($k->guruUmum->user->nama ?? 'Admin');
                            $cnt = $isT ? $k->siswasTahfidz()->count() : $k->siswas()->count();
                        @endphp
                        <option value="{{ $k->id }}">
                            {{ $labelJenis }} {{ $k->nama_kelas }} — Pengampu: {{ $pengampu }} ({{ $cnt }} Siswa)
                        </option>
                    @empty
                        <option value="">-- Belum Ada Kelas Dibuat --</option>
                    @endforelse
                </select>
            </div>
        </div>

        @if($selectedKelas)
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center shadow-xs">
                        @if($selectedKelas->jenis_kelas === 'tahfidz')
                            <x-lucide-bookmark class="w-5 h-5 text-white" />
                        @else
                            <x-lucide-book-open class="w-5 h-5 !text-white" />
                        @endif
                    </span>
                    <div>
                        <div class="font-extrabold text-emerald-950 text-sm flex items-center gap-1.5">
                            <span>{{ $selectedKelas->nama_kelas }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $selectedKelas->jenis_kelas === 'tahfidz' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-900 border border-emerald-300' }}">
                                {{ $selectedKelas->jenis_kelas === 'tahfidz' ? 'Kelas Tahfizh' : 'Kelas Umum' }}
                            </span>
                        </div>
                        <div class="text-[11px] text-emerald-800 font-medium py-0.5">
                            Pengampu: <strong>{{ $selectedKelas->jenis_kelas === 'tahfidz' ? ($selectedKelas->guruTahfidz->user->nama ?? 'Admin') : ($selectedKelas->guruUmum->user->nama ?? 'Admin') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="text-right font-black text-emerald-950 bg-white px-3 py-2 rounded-xl border border-emerald-300">
                    <span class="text-base text-emerald-800">{{ $selectedKelas->jenis_kelas === 'tahfidz' ? $selectedKelas->siswasTahfidz()->count() : $selectedKelas->siswas()->count() }}</span> Santri Terdaftar
                </div>
            </div>
        @endif
    </div>

    <!-- Student Roster Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">
                Daftar Murid Terdaftar di {{ $selectedKelas->nama_kelas ?? 'Kelas' }}
            </h3>

            <div class="relative max-w-xs w-full">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search_roster" 
                    placeholder="Cari nama / NISN murid..." 
                    class="w-full pl-9 pr-4 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" 
                />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2 pointer-events-none" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3 text-center border-r border-emerald-700 w-12">NO</th>
                        <th class="p-3 border-r border-emerald-700 w-32">NIS / NISN</th>
                        <th class="p-3 border-r border-emerald-700 min-w-[200px]">NAMA SISWA</th>
                        <th class="p-3 border-r border-emerald-700 w-16 text-center">L/P</th>
                        <th class="p-3 border-r border-emerald-700 min-w-[180px]">
                            {{ ($selectedKelas && $selectedKelas->jenis_kelas === 'tahfidz') ? 'KELAS UMUM' : 'KELAS TAHFIZH' }}
                        </th>
                        <th class="p-3 text-center min-w-[120px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($roster as $index => $s)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3 text-center font-bold text-stone-500 border-r border-stone-200">
                                {{ $roster->firstItem() + $index }}
                            </td>
                            <td class="p-3 border-r border-stone-200">
                                <div class="font-bold text-stone-900">{{ $s->nis }}</div>
                                <div class="text-[10px] text-stone-500">NISN: {{ $s->nisn ?: '-' }}</div>
                            </td>
                            <td class="p-3 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? '-') }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">Username: {{ $s->user->username ?? '-' }}</div>
                            </td>
                            <td class="p-3 text-center font-bold text-stone-700 border-r border-stone-200">
                                {{ strtoupper($s->jenis_kelamin ?? 'L') }}
                            </td>
                            <td class="p-3 border-r border-stone-200">
                                @if($selectedKelas && $selectedKelas->jenis_kelas === 'tahfidz')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-book-open class="w-3.5 h-3.5 text-emerald-700 !text-white" />
                                        <span>{{ $s->kelas->nama_kelas ?? 'Belum Set' }}</span>
                                    </span>
                                @else
                                    @if($s->kelasTahfidz)
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded text-xs font-bold inline-flex items-center gap-1">
                                            <x-lucide-bookmark class="w-3.5 h-3.5 text-amber-700" />
                                            <span>{{ $s->kelasTahfidz->nama_kelas }}</span>
                                        </span>
                                    @else
                                        <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                                    @endif
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <button type="button" wire:click.prevent="unassignSiswa({{ $s->id }})" data-confirm="Apakah Anda yakin ingin mengeluarkan {{ $s->user->nama ?? 'siswa ini' }} dari {{ $selectedKelas->nama_kelas }}?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs inline-flex items-center gap-1">
                                    <x-lucide-user-minus class="w-3.5 h-3.5 text-rose-600" />
                                    <span>Keluarkan</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                Belum ada murid terdaftar di {{ $selectedKelas->nama_kelas ?? 'kelas ini' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $roster->links() }}
        </div>
    </div>

    <!-- MODAL KOLEKTIF TAMBAH MURID -->
    @if($showAddModal && $selectedKelas)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 pt-20 sm:pt-8 pb-8 overflow-y-auto">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-2xl w-full space-y-4 max-h-[85vh] my-auto overflow-y-auto">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                                <x-lucide-user-plus class="w-3.5 h-3.5 text-emerald-900" />
                            </span>
                            <span>Masukkan Murid Ke {{ $selectedKelas->nama_kelas }}</span>
                        </h3>
                        <p class="text-[11px] text-stone-500 font-semibold mt-0.5">Centang beberapa siswa di bawah ini untuk dimasukkan sekaligus.</p>
                    </div>
                    <button type="button" wire:click.prevent="closeAddModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <!-- Search Input for Candidates -->
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.250ms="search_candidates" 
                        placeholder="Cari nama / NISN murid yang akan dimasukkan..." 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" 
                    />
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5 pointer-events-none" />
                </div>

                <!-- Candidate List Table with Checkboxes -->
                <div class="overflow-y-auto max-h-[350px] border border-stone-200 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs text-stone-800">
                        <thead class="bg-stone-100 text-stone-700 font-bold uppercase border-b border-stone-200 sticky top-0 bg-stone-100 z-10">
                            <tr>
                                <th class="p-2.5 text-center w-10">PILIH</th>
                                <th class="p-2.5">NISN &amp; NAMA SISWA</th>
                                <th class="p-2.5">KELAS SAAT INI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse($candidates as $c)
                                @php
                                    $currClass = $selectedKelas->jenis_kelas === 'tahfidz' 
                                        ? ($c->kelasTahfidz->nama_kelas ?? 'Belum ada halaqah') 
                                        : ($c->kelas->nama_kelas ?? 'Belum ada kelas');
                                @endphp
                                <tr class="hover:bg-emerald-50/60 cursor-pointer">
                                    <td class="p-2.5 text-center">
                                        <input type="checkbox" wire:model="selected_siswa_ids" value="{{ $c->id }}" class="w-4 h-4 text-emerald-700 rounded border-stone-300 focus:ring-emerald-600 cursor-pointer">
                                    </td>
                                    <td class="p-2.5">
                                        <div class="font-extrabold text-stone-900">{{ strtoupper($c->user->nama ?? '-') }}</div>
                                        <div class="text-[10px] text-stone-500 font-medium">NISN: {{ $c->nisn ?: '-' }}</div>
                                    </td>
                                    <td class="p-2.5">
                                        <span class="px-2 py-0.5 bg-stone-100 border border-stone-300 rounded text-[11px] font-bold text-stone-700">
                                            {{ $currClass }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-6 text-center text-stone-500 italic font-medium">
                                        Tidak ada siswa tersedia untuk ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t border-stone-200 pt-3">
                    <span class="text-xs font-bold text-stone-600">
                        Terpilih: <strong class="text-emerald-800 text-sm">{{ count($selected_siswa_ids) }} Siswa</strong>
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click.prevent="closeAddModal" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="button" wire:click.prevent="assignSiswaToKelas" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Masukkan {{ count($selected_siswa_ids) }} Siswa Ke {{ $selectedKelas->nama_kelas }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
