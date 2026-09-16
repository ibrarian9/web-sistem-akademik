<!-- Breakdown View Tabs: Per Hari, Per Minggu, Per Bulan -->
<div class="flex items-center justify-between border-b border-stone-200 pb-2 flex-wrap gap-3">
    <div class="grid grid-cols-3 sm:inline-flex items-center gap-1.5 w-full sm:w-auto">
        <button wire:click="selectTab('daily')" 
            class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 border cursor-pointer text-center sm:text-left {{ $viewTab === 'daily' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
            <svg class="w-4 h-4 shrink-0 {{ $viewTab === 'daily' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="hidden sm:inline">{{ $viewTab === 'daily' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Hari</span>
            <span class="sm:hidden text-[11px] truncate">Harian</span>
            <span class="hidden md:inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $viewTab === 'daily' ? 'bg-emerald-900 text-white' : 'bg-stone-100 text-stone-600' }}">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M') }}
            </span>
        </button>

        <button wire:click="selectTab('weekly')" 
            class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 border cursor-pointer text-center sm:text-left {{ $viewTab === 'weekly' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
            <svg class="w-4 h-4 shrink-0 {{ $viewTab === 'weekly' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            <span class="hidden sm:inline">{{ $viewTab === 'weekly' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Minggu</span>
            <span class="sm:hidden text-[11px] truncate">Mingguan</span>
        </button>

        <button wire:click="selectTab('monthly')" 
            class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center sm:justify-start gap-1 sm:gap-2 border cursor-pointer text-center sm:text-left {{ $viewTab === 'monthly' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50' }}">
            <svg class="w-4 h-4 shrink-0 {{ $viewTab === 'monthly' ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="hidden sm:inline">{{ $viewTab === 'monthly' ? 'text-white' : 'text-stone-700' }}">Tampilan Per Bulan</span>
            <span class="sm:hidden text-[11px] truncate">Bulanan</span>
        </button>
    </div>

    @if($viewTab === 'monthly')
        <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-stone-600">Pilih Bulan:</label>
            <input type="month" wire:model.live="selectedMonth" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-xs font-bold text-stone-900 shadow-xs">
        </div>
    @endif
</div>
