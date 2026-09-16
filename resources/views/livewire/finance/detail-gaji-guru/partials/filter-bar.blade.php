<!-- Filter Bar & Bulk Actions Bar -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <div>
        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Status Pembayaran</label>
        <select wire:model.live="filterStatus" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Status</option>
            <option value="dibayar">Dibayar (Selesai)</option>
            <option value="draft">Draft (Menunggu)</option>
        </select>
    </div>

    <div>
        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Bulan</label>
        <select wire:model.live="filterBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Bulan</option>
            @foreach ($listBulan as $b)
                <option value="{{ $b }}">{{ $b }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Tahun</label>
        <select wire:model.live="filterTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
            <option value="">Semua Tahun</option>
            @for ($y = intval(date('Y')) + 1; $y >= intval(date('Y')) - 4; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <div>
        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Cari Keterangan</label>
        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari catatan / sumber dana..." />
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
                <div class="text-xs font-black tracking-wide text-white">{{ count($selectedGajiIds) }} Riwayat Gaji Terpilih</div>
                <div class="text-[11px] text-emerald-200/80 font-medium">Pilih aksi untuk mengunduh slip PDF atau menghapus data terpilih.</div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-end">
            <x-button 
                variant="primary" 
                size="sm" 
                icon="download" 
                href="{{ route('finance.gaji-guru.bulk-slip', ['ids' => implode(',', $selectedGajiIds)]) }}"
                :wireNavigate="false"
                target="_blank"
            >
                Unduh ({{ count($selectedGajiIds) }}) Slip PDF
            </x-button>

            @if(!auth()->user()->isSuperAdmin2())
            <x-button 
                variant="danger" 
                size="sm" 
                icon="trash-2" 
                wire:click="deleteSelected"
                data-confirm="Apakah Anda yakin ingin memproses penghapusan {{ count($selectedGajiIds) }} riwayat gaji yang dipilih? Untuk data berstatus dibayar, staf keuangan akan mengajukan permohonan persetujuan ke Super Admin."
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
