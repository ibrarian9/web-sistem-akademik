@props([
    'model' => 'filterPeriode',
    'startDateModel' => 'startDate',
    'endDateModel' => 'endDate',
    'label' => null,
])

<div class="space-y-2 w-full sm:w-auto" x-data="{ showCustom: @entangle($model).live === 'custom' }">
    @if ($label)
        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">{{ $label }}</label>
    @endif

    <div class="flex flex-wrap items-center gap-2">
        <!-- Preset Selector Dropdown / Pills -->
        <div class="relative inline-block text-left">
            <select wire:model.live="{{ $model }}" 
                    class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs cursor-pointer">
                <option value="semua">Semua Periode</option>
                <option value="hari_ini">Hari Ini</option>
                <option value="kemarin">Kemarin</option>
                <option value="minggu_ini">Minggu Ini</option>
                <option value="bulan_ini">Bulan Ini</option>
                <option value="custom">Rentang Kustom...</option>
            </select>
        </div>

        <!-- Custom Date Range Inputs (Visible when Custom is selected) -->
        <div x-show="$wire.{{ $model }} === 'custom'" x-cloak class="flex items-center gap-2 bg-stone-50 border border-stone-200 p-1.5 rounded-2xl shadow-2xs animate-fade-in">
            <input 
                type="date" 
                wire:model.live="{{ $startDateModel }}" 
                class="px-2.5 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                title="Tanggal Mulai"
            />
            <span class="text-xs font-bold text-stone-400">s/d</span>
            <input 
                type="date" 
                wire:model.live="{{ $endDateModel }}" 
                class="px-2.5 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                title="Tanggal Akhir"
            />
        </div>
    </div>
</div>
