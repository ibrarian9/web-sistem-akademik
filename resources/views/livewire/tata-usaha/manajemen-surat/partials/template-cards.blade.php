<!-- SELECT TEMPLATE CARDS -->
<div class="space-y-4">
    <h3 class="text-xs font-extrabold text-stone-700 uppercase tracking-wider">PILIH TEMPLATE SURAT YANG INGIN DIBUAT</h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Template 1: Aktif Sekolah -->
        <button type="button" wire:click="$set('jenis_surat', 'aktif_sekolah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $jenis_surat === 'aktif_sekolah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center font-black">
                    <x-lucide-graduation-cap class="w-5 h-5 text-emerald-800" />
                </div>
                @if($jenis_surat === 'aktif_sekolah') <x-badge variant="emerald" size="xs">Terpilih</x-badge> @endif
            </div>
            <div>
                <h4 class="font-extrabold text-stone-900 text-xs">Surat Keterangan Aktif Sekolah</h4>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Untuk siswa terdaftar aktif di SD Tahfizh F3.</p>
            </div>
        </button>

        <!-- Template 2: Pengalaman Kerja -->
        <button type="button" wire:click="$set('jenis_surat', 'pengalaman_kerja')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $jenis_surat === 'pengalaman_kerja' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center font-black">
                    <x-lucide-briefcase class="w-5 h-5 text-emerald-800" />
                </div>
                @if($jenis_surat === 'pengalaman_kerja') <x-badge variant="emerald" size="xs">Terpilih</x-badge> @endif
            </div>
            <div>
                <h4 class="font-extrabold text-stone-900 text-xs">Surat Pengalaman Kerja</h4>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan masa kerja & tugas guru / karyawan.</p>
            </div>
        </button>

        <!-- Template 3: Menerima Pindah -->
        <button type="button" wire:click="$set('jenis_surat', 'menerima_pindah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $jenis_surat === 'menerima_pindah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black">
                    <x-lucide-arrow-down-left class="w-5 h-5 text-amber-800" />
                </div>
                @if($jenis_surat === 'menerima_pindah') <x-badge variant="amber" size="xs">Terpilih</x-badge> @endif
            </div>
            <div>
                <h4 class="font-extrabold text-stone-900 text-xs">Mutasi: Menerima Pindah</h4>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan menerima siswa pindahan dari luar.</p>
            </div>
        </button>

        <!-- Template 4: Pindah Sekolah -->
        <button type="button" wire:click="$set('jenis_surat', 'pindah_sekolah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $jenis_surat === 'pindah_sekolah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-300 text-rose-900 flex items-center justify-center font-black">
                    <x-lucide-arrow-up-right class="w-5 h-5 text-rose-800" />
                </div>
                @if($jenis_surat === 'pindah_sekolah') <x-badge variant="rose" size="xs">Terpilih</x-badge> @endif
            </div>
            <div>
                <h4 class="font-extrabold text-stone-900 text-xs">Mutasi: Pindah Sekolah</h4>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan mengajukan pindah sekolah keluar.</p>
            </div>
        </button>
    </div>
</div>
