{{-- Filter Controls & Export Actions --}}
<div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
    <div class="sm:col-span-1">
        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama pegawai / jabatan..." />
    </div>
    
    <div>
        <select wire:model.live="filterStatus" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="dibayar">Dibayar</option>
        </select>
    </div>

    <div>
        <select wire:model.live="filterBulan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Bulan</option>
            @foreach ($listBulan as $b)
                <option value="{{ $b }}">{{ $b }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <input wire:model.live="filterTahun" type="number" placeholder="Tahun" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" />
    </div>
</div>

<!-- Action & Export Toolbar Row -->
<div class="flex items-center justify-between gap-3 pt-3 border-t border-stone-100 flex-wrap">
    <div class="text-xs text-stone-500 font-semibold">
        Menampilkan data penggajian pegawai sesuai filter aktif.
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        @if ($filterBulan)
            <a href="{{ route('finance.gaji-guru.bulk-slip', ['bulan' => $filterBulan, 'tahun' => $filterTahun, 'status' => $filterStatus]) }}" 
               target="_blank" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300 rounded-xl text-xs font-bold transition shadow-2xs">
                <x-lucide-printer class="w-3.5 h-3.5 text-stone-600" />
                <span>Cetak Semua Slip PDF</span>
            </a>
        @endif

        <a href="{{ route('finance.gaji-guru.rekap-pdf', array_filter(['bulan' => $filterBulan, 'tahun' => $filterTahun, 'status' => $filterStatus, 'search' => $search])) }}" 
           target="_blank" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs">
            <x-lucide-file-text class="w-4 h-4 text-rose-600" />
            <span>Rekap PDF</span>
        </a>

        <a href="{{ route('finance.gaji-guru.rekap-excel', array_filter(['bulan' => $filterBulan, 'tahun' => $filterTahun, 'status' => $filterStatus, 'search' => $search])) }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-2xs">
            <x-lucide-file-spreadsheet class="w-4 h-4 text-emerald-600" />
            <span>Rekap Excel</span>
        </a>
    </div>
</div>

<!-- Bulk Actions Floating / Top Bar (When items are selected) -->
@if (count($selectedGajiIds) > 0)
    <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-4 shadow-md flex flex-col sm:flex-row items-center justify-between gap-4 border border-emerald-700">
        <div class="flex items-center gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-600/80 text-white flex items-center justify-center font-black text-sm border border-emerald-500 shadow-2xs shrink-0">
                {{ count($selectedGajiIds) }}
            </div>
            <div>
                <div class="text-xs font-black tracking-wide text-white">{{ count($selectedGajiIds) }} Data Gaji Pegawai Terpilih</div>
                <div class="text-[11px] text-emerald-200/80 font-medium">Pilih aksi untuk mengunduh rekap PDF/Excel, mencetak slip, atau menghapus data sekaligus.</div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-end">
            <x-button 
                variant="primary" 
                size="sm" 
                icon="printer" 
                href="{{ route('finance.gaji-guru.bulk-slip', ['ids' => implode(',', $selectedGajiIds)]) }}"
                :wireNavigate="false"
                target="_blank"
            >
                Slip ({{ count($selectedGajiIds) }}) PDF
            </x-button>

            <a 
                href="{{ route('finance.gaji-guru.rekap-pdf', ['ids' => implode(',', $selectedGajiIds)]) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-xs"
            >
                <x-lucide-file-text class="w-3.5 h-3.5" />
                <span>Rekap PDF ({{ count($selectedGajiIds) }})</span>
            </a>

            <a 
                href="{{ route('finance.gaji-guru.rekap-excel', ['ids' => implode(',', $selectedGajiIds)]) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-xs"
            >
                <x-lucide-file-spreadsheet class="w-3.5 h-3.5" />
                <span>Rekap Excel ({{ count($selectedGajiIds) }})</span>
            </a>

            @if(!auth()->user()->isSuperAdmin2())
            <x-button 
                variant="danger" 
                size="sm" 
                icon="trash-2" 
                wire:click="deleteSelected"
                data-confirm="Apakah Anda yakin ingin memproses penghapusan {{ count($selectedGajiIds) }} data gaji yang dipilih? Untuk data berstatus dibayar, staf keuangan akan mengajukan permohonan persetujuan ke Super Admin."
            >
                Hapus ({{ count($selectedGajiIds) }}) Terpilih
            </x-button>
            @endif

            <button 
                type="button" 
                wire:click="$set('selectedGajiIds', []); $set('selectAll', false)" 
                class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-200 hover:text-white bg-white/10 hover:bg-white/20 transition cursor-pointer"
            >
                Batal
            </button>
        </div>
    </div>
@endif
