@props([
    'active' => 'tagihan', // 'tagihan', 'kasir', 'overview'
])

<div class="flex items-center gap-1.5 p-1.5 bg-stone-100/90 border border-stone-200/90 rounded-2xl w-full sm:w-fit overflow-x-auto custom-scrollbar shadow-2xs">
    <!-- Tab 1: Manajemen Tagihan & SPP -->
    <a href="{{ route('finance.tagihan') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $active === 'tagihan' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-extrabold ring-1 ring-emerald-600/10' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
        <div class="w-5 h-5 rounded-lg flex items-center justify-center {{ $active === 'tagihan' ? 'bg-emerald-100 text-emerald-700' : 'text-stone-400' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <span>Manajemen Tagihan & SPP</span>
    </a>

    <!-- Tab 2: Kasir Pembayaran Siswa -->
    <a href="{{ route('finance.input-pembayaran') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $active === 'kasir' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-extrabold ring-1 ring-emerald-600/10' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
        <div class="w-5 h-5 rounded-lg flex items-center justify-center {{ $active === 'kasir' ? 'bg-emerald-100 text-emerald-700' : 'text-stone-400' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <span>Kasir Pembayaran</span>
    </a>

    <!-- Tab 3: Overview Monitoring Pembayaran -->
    <a href="{{ route('finance.overview-pembayaran') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $active === 'overview' ? 'bg-white text-emerald-800 shadow-xs border border-stone-200/80 font-extrabold ring-1 ring-emerald-600/10' : 'text-stone-600 hover:text-stone-900 hover:bg-white/60' }}">
        <div class="w-5 h-5 rounded-lg flex items-center justify-center {{ $active === 'overview' ? 'bg-emerald-100 text-emerald-700' : 'text-stone-400' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </div>
        <span>Overview Monitoring</span>
    </a>
</div>
