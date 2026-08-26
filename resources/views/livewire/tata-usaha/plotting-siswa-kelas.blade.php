<div class="space-y-6 font-sans">
    @php
        $isTu = auth()->user()->role?->nama === 'tata_usaha';
        $routeKelas = $isTu ? route('tata-usaha.kelas') : route('super-admin.kelas');
        $routeSiswa = $isTu ? route('tata-usaha.siswa') : route('super-admin.siswa');
        $routePlotting = $isTu ? route('tata-usaha.plotting-kelas') : route('super-admin.plotting-kelas');
    @endphp

    <!-- Quick Switcher Bar -->
    <div class="flex items-center gap-2 bg-white border border-stone-200 p-2 rounded-2xl overflow-x-auto shadow-2xs">
        <a href="{{ $routeKelas }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
            <x-lucide-layers class="w-4 h-4 text-emerald-600" />
            <span>1. Buat & Kelola Kelas (Umum & Tahfizh)</span>
        </a>
        <a href="{{ $routeSiswa }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-2 whitespace-nowrap">
            <x-lucide-users class="w-4 h-4 text-emerald-600" />
            <span>2. Data Siswa</span>
        </a>
        <a href="{{ $routePlotting }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-2xs flex items-center gap-2 whitespace-nowrap">
            <x-lucide-user-plus class="w-4 h-4 text-emerald-100" />
            <span>3. Plotting Siswa Per-Kelas</span>
        </a>
    </div>

    <!-- Header Title Bar -->
    <x-page-header 
        title="Daftar Anggota Kelas & Input Kolektif" 
        subtitle="Pilih kelas di bawah ini untuk melihat murid terdaftar dan memasukkan murid secara kolektif."
        badge="PLOTTING KELAS & HALAQAH"
        badgeVariant="emerald"
        icon="users"
    >
        @if($selectedKelas)
            <x-slot:actions>
                <x-button variant="primary" size="md" icon="user-plus" wire:click.prevent="openAddModal">
                    Masukkan Murid Ke {{ $selectedKelas->nama_kelas }}
                </x-button>
            </x-slot:actions>
        @endif
    </x-page-header>

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
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-xs space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Filter Jenis Kelas</label>
                <select wire:model.live="filter_jenis" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Jenis Kelas</option>
                    <option value="umum">Kelas Umum (1 - 6)</option>
                    <option value="tahfidz">Kelas Tahfizh (Halaqah)</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Pilih Kelas Target Yang Ingin Dikelola</label>
                <select wire:model.live="selected_kelas_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 px-3.5 py-2.5 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
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
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center shadow-xs">
                        @if($selectedKelas->jenis_kelas === 'tahfidz')
                            <x-lucide-bookmark class="w-5 h-5 text-white" />
                        @else
                            <x-lucide-book-open class="w-5 h-5 text-white" />
                        @endif
                    </span>
                    <div>
                        <div class="font-extrabold text-emerald-950 text-sm flex items-center gap-1.5">
                            <span>{{ $selectedKelas->nama_kelas }}</span>
                            <x-badge :variant="$selectedKelas->jenis_kelas === 'tahfidz' ? 'amber' : 'emerald'" size="xs">
                                {{ $selectedKelas->jenis_kelas === 'tahfidz' ? 'Kelas Tahfizh' : 'Kelas Umum' }}
                            </x-badge>
                        </div>
                        <div class="text-[11px] text-emerald-800 font-medium py-0.5">
                            Pengampu: <strong>{{ $selectedKelas->jenis_kelas === 'tahfidz' ? ($selectedKelas->guruTahfidz->user->nama ?? 'Admin') : ($selectedKelas->guruUmum->user->nama ?? 'Admin') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="text-right font-black text-emerald-950 bg-white px-3.5 py-2 rounded-xl border border-emerald-300 shadow-2xs">
                    <span class="text-base text-emerald-800">{{ $selectedKelas->jenis_kelas === 'tahfidz' ? $selectedKelas->siswasTahfidz()->count() : $selectedKelas->siswas()->count() }}</span> Santri Terdaftar
                </div>
            </div>
        @endif
    </div>

    <!-- Student Roster Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">
                Daftar Murid Terdaftar di {{ $selectedKelas->nama_kelas ?? 'Kelas' }}
            </h3>

            <div class="max-w-xs w-full">
                <x-search-input wire:model.live.debounce.250ms="search_roster" placeholder="Cari nama / NISN murid..." />
            </div>
        </div>

        <x-table loadingTarget="selected_kelas_id, filter_jenis, search_roster">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th align="center" class="w-12">NO</x-table.th>
                    <x-table.th class="w-36">NIS / NISN</x-table.th>
                    <x-table.th class="min-w-[200px]">NAMA SISWA</x-table.th>
                    <x-table.th align="center" class="w-16">L/P</x-table.th>
                    <x-table.th class="min-w-[180px]">
                        {{ ($selectedKelas && $selectedKelas->jenis_kelas === 'tahfidz') ? 'KELAS UMUM' : 'KELAS TAHFIZH' }}
                    </x-table.th>
                    <x-table.th align="center" class="w-32">AKSI</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($roster as $index => $s)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 text-center font-bold text-stone-500 border-r border-stone-200">
                            {{ $roster->firstItem() + $index }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-stone-900">{{ $s->nis }}</div>
                            <div class="text-[10px] text-stone-500">NISN: {{ $s->nisn ?: '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($s->user->nama ?? '-') }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">Username: {{ $s->user->username ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 text-center font-bold text-stone-700 border-r border-stone-200">
                            {{ strtoupper($s->jenis_kelamin ?? 'L') }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if($selectedKelas && $selectedKelas->jenis_kelas === 'tahfidz')
                                <x-badge variant="emerald" size="xs">
                                    {{ $s->kelas->nama_kelas ?? 'Belum Set' }}
                                </x-badge>
                            @else
                                @if($s->kelasTahfidz)
                                    <x-badge variant="amber" size="xs">
                                        {{ $s->kelasTahfidz->nama_kelas }}
                                    </x-badge>
                                @else
                                    <span class="text-stone-400 italic text-[11px]">- Belum Set -</span>
                                @endif
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <x-button type="button" variant="danger" size="xs" icon="user-minus" wire:click.prevent="unassignSiswa({{ $s->id }})" data-confirm="Apakah Anda yakin ingin mengeluarkan {{ $s->user->nama ?? 'siswa ini' }} dari {{ $selectedKelas->nama_kelas }}?">
                                Keluarkan
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Belum ada murid terdaftar di {{ $selectedKelas->nama_kelas ?? 'kelas ini' }}" subtitle="Gunakan tombol Masukkan Murid di atas untuk menambahkan siswa." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $roster->links() }}
        </div>
    </div>

    <!-- Floating Modal: Kolektif Tambah Murid -->
    <x-floating-card 
        :show="($showAddModal && $selectedKelas) ? true : false"
        :title="'Masukkan Murid Ke ' . ($selectedKelas->nama_kelas ?? 'Kelas')"
        subtitle="Centang beberapa siswa di bawah ini untuk dimasukkan sekaligus."
        badge="INPUT KOLEKTIF"
        badgeVariant="emerald"
        icon="user-plus"
        maxWidth="max-w-2xl"
        closeAction="closeAddModal"
    >
        <div class="space-y-4 text-xs">
            <!-- Search Input for Candidates -->
            <x-search-input wire:model.live.debounce.250ms="search_candidates" placeholder="Cari nama / NISN murid yang akan dimasukkan..." />
            
            <x-table loadingTarget="search_candidates">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900 sticky top-0 z-10">
                    <tr>
                        <x-table.th align="center" class="w-12">PILIH</x-table.th>
                        <x-table.th>NISN & NAMA SISWA</x-table.th>
                        <x-table.th class="w-48">KELAS SAAT INI</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($candidates as $c)
                        @php
                            $currClass = $selectedKelas->jenis_kelas === 'tahfidz' 
                                ? ($c->kelasTahfidz->nama_kelas ?? 'Belum ada halaqah') 
                                : ($c->kelas->nama_kelas ?? 'Belum ada kelas');
                        @endphp
                        <tr class="hover:bg-emerald-50/60 transition">
                            <td class="p-3 text-center border-r border-stone-200">
                                <input type="checkbox" wire:model="selected_siswa_ids" value="{{ $c->id }}" class="w-4 h-4 text-emerald-700 rounded border-stone-300 focus:ring-emerald-600 cursor-pointer">
                            </td>
                            <td class="p-3 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($c->user->nama ?? '-') }}</div>
                                <div class="text-[10px] text-stone-500 font-mono font-bold">NISN: {{ $c->nisn ?: '-' }}</div>
                            </td>
                            <td class="p-3">
                                <x-badge variant="stone" size="xs">
                                    {{ $currClass }}
                                </x-badge>
                            </td>
                        </tr>
                    @empty
                        <x-table.empty :colspan="3" title="Tidak ada siswa tersedia" message="Tidak ada calon siswa yang ditemukan untuk dimasukkan." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="flex items-center justify-between border-t border-stone-200 pt-3">
                <span class="text-xs font-bold text-stone-600">
                    Terpilih: <strong class="text-emerald-800 text-sm">{{ count($selected_siswa_ids) }} Siswa</strong>
                </span>

                <div class="flex items-center gap-2">
                    <x-button type="button" variant="secondary" size="md" wire:click.prevent="closeAddModal">
                        Batal
                    </x-button>
                    <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="assignSiswaToKelas" loadingTarget="assignSiswaToKelas">
                        Masukkan {{ count($selected_siswa_ids) }} Siswa
                    </x-button>
                </div>
            </div>
        </div>
    </x-floating-card>
</div>
