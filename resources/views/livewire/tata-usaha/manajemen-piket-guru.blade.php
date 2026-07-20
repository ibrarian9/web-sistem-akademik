<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Kelola Jadwal Piket Guru</h2>
            <p class="text-xs text-stone-500">Atur jadwal penugasan piket guru harian. Guru Tahfidz piket diwajibkan check-in pada pukul 06:30 (non-piket: 06:45).</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-alert-triangle class="w-4 h-4 text-rose-600" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ADD PIKET FORM CARD -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4 text-indigo-600" />
            <span>Tambah Penugasan Piket Baru</span>
        </h3>

        <form wire:submit.prevent="addPiket" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-600">Pilih Guru</label>
                <select wire:model="selectedGuruId" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} ({{ ucfirst($g->jenis_guru) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-600">Pilih Hari Piket</label>
                <select wire:model="selectedHari" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500 uppercase">
                    @foreach ($days as $d)
                        <option value="{{ $d }}">{{ strtoupper($d) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Simpan Jadwal Piket</span>
                </button>
            </div>
        </form>
    </div>

    <!-- DAYS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach ($days as $day)
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                    <h4 class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider">{{ strtoupper($day) }}</h4>
                    <span class="px-2 py-0.5 bg-stone-100 text-stone-600 rounded text-[10px] font-bold">
                        {{ count($piketSchedules[$day] ?? []) }} Guru
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse ($piketSchedules[$day] as $p)
                        <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl flex items-center justify-between group">
                            <div>
                                <h5 class="text-xs font-bold text-stone-800 leading-tight">{{ $p->guru->user->nama ?? '-' }}</h5>
                                <span class="text-[9px] text-indigo-600 font-semibold uppercase">{{ $p->guru->jenis_guru ?? 'guru' }}</span>
                            </div>
                            <button wire:click="deletePiket({{ $p->id }})" class="text-stone-400 hover:text-rose-600 p-1 rounded-lg transition" title="Hapus Jadwal Piket">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @empty
                        <div class="py-6 text-center text-[10px] text-stone-400 italic">
                            Belum ada piket.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
