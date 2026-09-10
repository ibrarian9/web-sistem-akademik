<!-- Modal Create Tagihan -->
<x-floating-card 
    :show="$showCreateModal"
    title="Tambah Tagihan Baru"
    :subtitle="'Penerima: ' . ($siswa->user->nama ?? 'Siswa') . ' (' . ($siswa->kelas->nama_kelas ?? '-') . ')'"
    badge="FORM TAGIHAN"
    badgeVariant="emerald"
    icon="plus-circle"
    maxWidth="max-w-xl"
    closeAction="closeCreateModal"
>
    <form wire:submit.prevent="createTagihan" class="space-y-4 text-xs">
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Jenis Kategori Tagihan <span class="text-rose-600">*</span></label>
            <select wire:model.live="jenis_tagihan_id" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                <option value="">Pilih Jenis Tagihan</option>
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt['id'] }}">{{ $jt['nama'] }} (Default: Rp {{ number_format($jt['default_nominal'] ?? 0, 0, ',', '.') }})</option>
                @endforeach
            </select>
            @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Pilihan Periode Tagihan -->
        <div class="space-y-1.5">
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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @if ($periodeTipe === 'single')
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Bulan Tagihan <span class="text-rose-600">*</span></label>
                    <select wire:model="bulan" class="w-full bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold p-2.5 focus:ring-2 focus:ring-emerald-600">
                        @foreach ($bulanOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                    @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            @elseif ($periodeTipe !== 'custom_range')
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Cakupan Otomatis</label>
                    <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 font-bold flex items-center gap-2">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>12 Bulan Sekaligus</span>
                    </div>
                </div>
            @endif

            <div class="{{ $periodeTipe === 'custom_range' ? 'sm:col-span-2' : '' }}">
                <label class="block text-xs font-bold text-stone-700 mb-1">Jatuh Tempo</label>
                <div class="p-2.5 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs text-amber-900 font-medium flex items-center gap-2">
                    <x-lucide-calendar class="w-4 h-4 text-amber-600 shrink-0" />
                    <span>Fix tgl <strong>10</strong> setiap bulannya</span>
                </div>
            </div>
        </div>

        <x-input-currency
            wire:model="nominal"
            id="nominal"
            name="nominal"
            label="Nominal Tagihan (Rp)"
            placeholder="Contoh: 350.000 (Isi 0 jika Siswa Bebas SPP atau Beasiswa)"
            hint="Jika diisi Rp 0, tagihan otomatis berstatus Lunas."
            required
        />

        <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button type="button" variant="secondary" size="md" wire:click="closeCreateModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="createTagihan">
                Simpan Tagihan
            </x-button>
        </div>
    </form>
</x-floating-card>
