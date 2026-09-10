<!-- FLOATING CARD: FORM RILIS TAGIHAN SISWA (SINGLE & BULK INPUT) -->
<x-floating-card 
    :show="$showCreateModal" 
    title="{{ $releaseMode === 'bulk' ? 'Rilis Tagihan Massal' : 'Rilis Tagihan Siswa' }}" 
    subtitle="{{ $releaseMode === 'bulk' ? 'Terbitkan tagihan serentak untuk seluruh siswa per kelas atau seluruh sekolah.' : 'Terbitkan tagihan baru dengan nominal spesifik untuk siswa perorangan.' }}"
    badge="{{ $releaseMode === 'bulk' ? 'RILIS MASSAL' : 'RILIS PERORANGAN' }}"
    badgeVariant="{{ $releaseMode === 'bulk' ? 'indigo' : 'emerald' }}"
    icon="{{ $releaseMode === 'bulk' ? 'layers' : 'plus-circle' }}"
    maxWidth="max-w-xl"
    closeAction="closeCreateModal"
>
    <!-- Mode Switcher Tabs -->
    <div class="flex items-center p-1 bg-stone-100 rounded-2xl border border-stone-200 mb-5">
        <button 
            type="button" 
            wire:click="setReleaseMode('single')" 
            class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $releaseMode === 'single' ? 'bg-white text-emerald-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-user class="w-4 h-4" />
            <span>Perorangan (1 Siswa)</span>
        </button>
        <button 
            type="button" 
            wire:click="setReleaseMode('bulk')" 
            class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $releaseMode === 'bulk' ? 'bg-white text-indigo-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
            <x-lucide-layers class="w-4 h-4" />
            <span>Rilis Massal (Bulk)</span>
        </button>
    </div>

    @if ($releaseMode === 'single')
        <!-- FORM SINGLE / PERORANGAN -->
        <form wire:submit.prevent="createSingleTagihan" class="space-y-4">
            <!-- Filter Kelas & Search Autocomplete -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Pilih Siswa Penerima Tagihan <span class="text-rose-500">*</span>
                </label>

                @if ($single_siswa_id)
                    <!-- Selected Student Pill / Card -->
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                {{ strtoupper(substr($selectedStudentName, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-extrabold text-stone-900 text-xs">{{ $selectedStudentName }}</div>
                                <div class="text-[11px] text-emerald-700 font-mono font-bold">
                                    NIS: {{ $selectedStudentNis }} • Kelas {{ $selectedStudentKelas }}
                                </div>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            wire:click="clearSelectedStudent" 
                            class="px-3 py-1.5 bg-white border border-stone-200 hover:bg-rose-50 hover:text-rose-700 text-stone-600 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                            Ganti Siswa
                        </button>
                    </div>
                @else
                    <!-- Filter Kelas & Live Search Input -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 relative">
                        <!-- Filter Kelas -->
                        <div class="sm:col-span-1">
                            <select wire:model.live="release_kelas_id" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                                <option value="">Semua Kelas</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Live Search Input -->
                        <div class="sm:col-span-2 relative">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.250ms="studentSearch" 
                                    placeholder="Ketik nama siswa atau NIS..." 
                                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                                    <x-lucide-search class="w-4 h-4" />
                                </div>
                            </div>

                            <!-- Live Autocomplete Dropdown List -->
                            @if (count($searchedStudents) > 0)
                                <div class="absolute top-full left-0 right-0 z-50 mt-1.5 bg-white border border-stone-200 rounded-2xl shadow-xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-stone-100">
                                    @foreach ($searchedStudents as $s)
                                        <button 
                                            type="button" 
                                            wire:click="selectStudent({{ $s->id }})" 
                                            class="w-full px-3.5 py-2.5 text-left hover:bg-emerald-50 transition flex items-center justify-between gap-3 cursor-pointer group">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-lg bg-stone-100 group-hover:bg-emerald-600 group-hover:text-white font-bold text-stone-700 text-[10px] flex items-center justify-center shrink-0 transition">
                                                    {{ strtoupper(substr($s->user->nama ?? 'S', 0, 2)) }}
                                                </span>
                                                <div>
                                                    <span class="text-xs font-extrabold text-stone-900 group-hover:text-emerald-900 block leading-tight">
                                                        {{ $s->user->nama ?? '-' }}
                                                    </span>
                                                    <span class="text-[10px] text-stone-500 font-mono">
                                                        NIS: {{ $s->nis }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 bg-stone-100 text-stone-700 text-[10px] font-extrabold rounded-md border border-stone-200">
                                                Kelas {{ $s->kelas->nama_kelas ?? '-' }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif (strlen(trim($studentSearch)) >= 2)
                                <div class="absolute top-full left-0 right-0 z-50 mt-1.5 bg-white border border-stone-200 rounded-2xl shadow-xl p-3 text-center text-xs text-stone-500 font-medium">
                                    Tidak ada siswa ditemukan dengan kata kunci "{{ $studentSearch }}".
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @error('single_siswa_id') 
                    <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Jenis Tagihan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Tagihan <span class="text-rose-500">*</span></label>
                <select wire:model.live="jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Kategori Tagihan --</option>
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                    @endforeach
                </select>
                @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Pilihan Periode Tagihan -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Pilihan Periode Tagihan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'full_year_jan_des' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-bold ring-2 ring-emerald-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="full_year_jan_des" class="text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-xs">1 Thn (Jan - Des)</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'full_year_juli_juni' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-bold ring-2 ring-emerald-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="full_year_juli_juni" class="text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-xs">1 T.A. (Juli - Juni)</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'custom_range' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-bold ring-2 ring-emerald-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="custom_range" class="text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-xs">Rentang Bulan</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'single' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-bold ring-2 ring-emerald-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="single" class="text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-xs">1 Bulan Saja</span>
                    </label>
                </div>
            </div>

            @if ($periodeTipe === 'custom_range')
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-1">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider">Atur Rentang Bulan</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" wire:click="setPresetRange('Juli', 'Desember')" class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                                Sem. Ganjil (Jul - Des)
                            </button>
                            <button type="button" wire:click="setPresetRange('Januari', 'Juni')" class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                                Sem. Genap (Jan - Jun)
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 mb-1">Dari Bulan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="bulan_mulai" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                @foreach ($standardMonths as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('bulan_mulai') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 mb-1">Sampai Bulan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="bulan_selesai" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                @foreach ($standardMonths as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('bulan_selesai') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @php
                        $previewMonths = $this->getTargetMonths();
                    @endphp
                    <div class="text-xs text-emerald-900 bg-emerald-50 border border-emerald-200 p-2 rounded-lg flex items-center gap-2">
                        <x-lucide-calendar-range class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="font-bold">{{ count($previewMonths) }} Bulan Terpilih:</span>
                        <span class="text-[11px] truncate">{{ implode(', ', $previewMonths) }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($periodeTipe === 'single')
                    <!-- Bulan Tagihan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan Tagihan <span class="text-rose-500">*</span></label>
                        <select wire:model="bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            @foreach ($bulanOptions as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                @elseif ($periodeTipe !== 'custom_range')
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Cakupan Otomatis</label>
                        <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 font-bold flex items-center gap-2">
                            <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>12 Bulan Sekaligus</span>
                        </div>
                    </div>
                @endif

                <!-- Jatuh Tempo (Fix Tanggal 10) -->
                <div class="space-y-1.5 {{ $periodeTipe === 'custom_range' ? 'sm:col-span-2' : '' }}">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jatuh Tempo</label>
                    <div class="p-2.5 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs text-amber-900 font-medium flex items-center gap-2">
                        <x-lucide-calendar class="w-4 h-4 text-amber-600 shrink-0" />
                        <span>Fix tgl <strong>10</strong> setiap bulannya</span>
                    </div>
                </div>
            </div>

            <!-- Nominal Tagihan -->
            <x-input-currency 
                label="Nominal Tagihan Siswa (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 350.000 (Isi 0 jika Bebas Biaya atau Beasiswa)" 
                hint="Dapat disesuaikan secara fleksibel untuk siswa beasiswa atau pembebasan SPP (Isi 0 untuk otomatis Lunas)."
                required 
            />

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="createSingleTagihan">
                    Terbitkan Tagihan
                </x-button>
            </div>
        </form>
    @else
        <!-- FORM BULK / RILIS MASSAL & LINTAS KELAS -->
        <form wire:submit.prevent="createBulkTagihan" class="space-y-4">
            <!-- Pilihan Target Massal -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Target Penerima Tagihan <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'custom' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="bulkTarget" value="custom" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                        <div>
                            <span class="text-xs font-extrabold text-stone-900 block">Pilih Siswa (Lintas Kelas)</span>
                            <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Pilih 2-3 atau lebih siswa dengan SPP sama</span>
                        </div>
                    </label>

                    <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'class' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="bulkTarget" value="class" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                        <div>
                            <span class="text-xs font-extrabold text-stone-900 block">Per Kelas Tertentu</span>
                            <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Rilis untuk 1 rombel kelas</span>
                        </div>
                    </label>

                    <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'all' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="bulkTarget" value="all" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                        <div>
                            <span class="text-xs font-extrabold text-stone-900 block">Seluruh Siswa Aktif</span>
                            <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Rilis ke seluruh sekolah</span>
                        </div>
                    </label>
                </div>

                @error('bulkTarget') 
                    <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                @enderror
            </div>

            @if ($bulkTarget === 'custom')
                <!-- Multi-Select Siswa Lintas Kelas -->
                <div class="p-4 bg-stone-50/80 border border-stone-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-stone-800">Cari & Pilih Siswa Penerima</span>
                        <span class="text-[11px] font-black text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">
                            {{ count($bulkSelectedSiswaIds) }} Siswa Dipilih
                        </span>
                    </div>

                    <!-- Class filter & search -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <select wire:model.live="bulkSearchKelasId" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                            @endforeach
                        </select>

                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="bulkSearchStudent" 
                            placeholder="Ketik nama atau NIS siswa..." 
                            class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs"
                        />
                    </div>

                    <!-- Search Results for selection -->
                    @if (!empty($bulkSearchedStudents) && count($bulkSearchedStudents) > 0)
                        <div class="max-h-48 overflow-y-auto border border-stone-200 rounded-xl bg-white divide-y divide-stone-100 shadow-2xs">
                            @foreach ($bulkSearchedStudents as $res)
                                @php $isSelected = in_array($res->id, $bulkSelectedSiswaIds); @endphp
                                <div class="p-2.5 flex items-center justify-between hover:bg-stone-50 transition {{ $isSelected ? 'bg-indigo-50/40' : '' }}">
                                    <div>
                                        <span class="text-xs font-extrabold text-stone-900 block leading-tight">{{ $res->user->nama ?? '-' }}</span>
                                        <span class="text-[10px] text-stone-500 font-mono">NIS: {{ $res->nis }} • Kelas {{ $res->kelas->nama_kelas ?? '-' }}</span>
                                    </div>

                                    @if ($isSelected)
                                        <button 
                                            type="button" 
                                            wire:click="removeSiswaFromBulk({{ $res->id }})" 
                                            class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 text-[11px] font-extrabold rounded-lg transition cursor-pointer">
                                            ✕ Hapus
                                        </button>
                                    @else
                                        <button 
                                            type="button" 
                                            wire:click="addSiswaToBulk({{ $res->id }})" 
                                            class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-extrabold rounded-lg transition cursor-pointer">
                                            + Pilih
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif (strlen(trim($bulkSearchStudent)) >= 2)
                        <div class="bg-white border border-stone-200 rounded-xl p-2.5 text-center text-xs text-stone-500">
                            Tidak ada siswa ditemukan dengan kata kunci "{{ $bulkSearchStudent }}".
                        </div>
                    @endif
                </div>
            @endif

            <!-- Pilihan Kelas jika mode Per Kelas -->
            @if ($bulkTarget === 'class')
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Target Kelas <span class="text-rose-500">*</span></label>
                    <select wire:model.live="bulk_kelas_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                        <option value="">-- Pilih Kelas Target --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                        @endforeach
                    </select>
                    @error('bulk_kelas_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Jenis Tagihan Massal -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Tagihan <span class="text-rose-500">*</span></label>
                <select wire:model.live="jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                    <option value="">-- Pilih Kategori Tagihan --</option>
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                    @endforeach
                </select>
                @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Pilihan Periode Tagihan Massal -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Pilihan Periode Tagihan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'full_year_jan_des' ? 'border-indigo-600 bg-indigo-50 text-indigo-950 font-bold ring-2 ring-indigo-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="full_year_jan_des" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-xs">1 Thn (Jan - Des)</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'full_year_juli_juni' ? 'border-indigo-600 bg-indigo-50 text-indigo-950 font-bold ring-2 ring-indigo-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="full_year_juli_juni" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-xs">1 T.A. (Juli - Juni)</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'custom_range' ? 'border-indigo-600 bg-indigo-50 text-indigo-950 font-bold ring-2 ring-indigo-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="custom_range" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-xs">Rentang Bulan</span>
                    </label>
                    <label class="p-2 border rounded-xl flex items-center gap-2 cursor-pointer transition {{ $periodeTipe === 'single' ? 'border-indigo-600 bg-indigo-50 text-indigo-950 font-bold ring-2 ring-indigo-500/20' : 'border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="periodeTipe" value="single" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-xs">1 Bulan Saja</span>
                    </label>
                </div>
            </div>

            @if ($periodeTipe === 'custom_range')
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-1">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider">Atur Rentang Bulan</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" wire:click="setPresetRange('Juli', 'Desember')" class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition">
                                Sem. Ganjil (Jul - Des)
                            </button>
                            <button type="button" wire:click="setPresetRange('Januari', 'Juni')" class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                                Sem. Genap (Jan - Jun)
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 mb-1">Dari Bulan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="bulan_mulai" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600">
                                @foreach ($standardMonths as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('bulan_mulai') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 mb-1">Sampai Bulan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="bulan_selesai" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600">
                                @foreach ($standardMonths as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('bulan_selesai') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @php
                        $previewMonths = $this->getTargetMonths();
                    @endphp
                    <div class="text-xs text-indigo-900 bg-indigo-50 border border-indigo-200 p-2 rounded-lg flex items-center gap-2">
                        <x-lucide-calendar-range class="w-4 h-4 text-indigo-600 shrink-0" />
                        <span class="font-bold">{{ count($previewMonths) }} Bulan Terpilih:</span>
                        <span class="text-[11px] truncate">{{ implode(', ', $previewMonths) }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($periodeTipe === 'single')
                    <!-- Bulan Tagihan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan Tagihan <span class="text-rose-500">*</span></label>
                        <select wire:model="bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                            @foreach ($bulanOptions as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                @elseif ($periodeTipe !== 'custom_range')
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Cakupan Otomatis</label>
                        <div class="p-2.5 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 font-bold flex items-center gap-2">
                            <x-lucide-check-circle-2 class="w-4 h-4 text-indigo-600 shrink-0" />
                            <span>12 Bulan Sekaligus</span>
                        </div>
                    </div>
                @endif

                <!-- Jatuh Tempo (Fix Tanggal 10) -->
                <div class="space-y-1.5 {{ $periodeTipe === 'custom_range' ? 'sm:col-span-2' : '' }}">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jatuh Tempo</label>
                    <div class="p-2.5 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs text-amber-900 font-medium flex items-center gap-2">
                        <x-lucide-calendar class="w-4 h-4 text-amber-600 shrink-0" />
                        <span>Fix tgl <strong>10</strong> setiap bulannya</span>
                    </div>
                </div>
            </div>

            <!-- Nominal Tagihan -->
            <x-input-currency 
                label="Nominal Tagihan yang Diterapkan (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 350.000 (Isi 0 jika Bebas Biaya atau Beasiswa)" 
                hint="Nominal ini akan diterapkan serentak ke seluruh siswa yang dipilih di atas (Isi 0 untuk otomatis Lunas)."
                required 
            />

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="createBulkTagihan">
                    Terbitkan Tagihan ({{ $bulkStudentCount }} Siswa)
                </x-button>
            </div>
        </form>
    @endif
</x-floating-card>
