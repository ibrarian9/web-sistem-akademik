<!-- Employee Profile Banner -->
<div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-3xl p-5 sm:p-6 shadow-md border border-emerald-700/50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-white font-black flex items-center justify-center text-xl border border-white/20 shadow-inner shrink-0">
            {{ strtoupper(substr($guru->user->nama ?? 'G', 0, 2)) }}
        </div>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base sm:text-lg font-black tracking-tight">{{ $guru->user->nama ?? '-' }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-700 text-emerald-100 border border-emerald-500/40">
                    {{ $guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : ($guru->jabatan ?: 'Guru Pengajar') }}
                </span>
                @if ($activeLoan)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/80 text-white border border-rose-400/40 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3 h-3" />
                        Kasbon Aktif: Rp {{ number_format($activeLoan->sisa_pinjaman, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            <div class="text-xs text-emerald-100/80 font-medium mt-1 flex items-center gap-2 flex-wrap">
                <span>NIY: {{ $guru->niy ?? ($guru->nip ?? '-') }}</span>
                <span>&bull;</span>
                <span>Email: {{ $guru->user->email ?? '-' }}</span>
                <span>&bull;</span>
                <span>Pendidikan: {{ $guru->pendidikan ?: 'S1' }}</span>
                <span>&bull;</span>
                <span>TMT: {{ $guru->tanggal_masuk ? \Carbon\Carbon::parse($guru->tanggal_masuk)->format('d M Y') : '-' }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 self-end lg:self-center">
        @if ($guru->no_hp)
            <a 
                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guru->no_hp) }}" 
                target="_blank" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition cursor-pointer"
            >
                <x-lucide-phone class="w-3.5 h-3.5 text-emerald-300" />
                <span>WhatsApp</span>
            </a>
        @endif
    </div>
</div>
