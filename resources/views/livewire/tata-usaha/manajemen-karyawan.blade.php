<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Direktori Karyawan &amp; Staff</h2>
            <p class="text-xs text-stone-500">Daftar seluruh tenaga pendidik (guru) dan kependidikan (TU, Finance, Koordinator, Kepala Sekolah) terdaftar.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / email..." 
                    class="bg-stone-50 border border-stone-300 text-stone-800 placeholder-stone-400 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:border-indigo-500 w-64" />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
            </div>

            <select wire:model.live="filterRole" class="bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                <option value="semua">Semua Role</option>
                <option value="guru">Guru</option>
                <option value="tata_usaha">Tata Usaha</option>
                <option value="finance">Finance</option>
                <option value="koordinator">Koordinator</option>
                <option value="kepala_sekolah">Kepala Sekolah</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($karyawanList as $k)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-indigo-300 transition">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm select-none">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-stone-800 leading-snug">{{ $k->nama }}</h3>
                                <p class="text-[10px] text-stone-500">@ {{ $k->username }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-stone-100 text-stone-700 border border-stone-200 rounded-lg text-[10px] font-extrabold uppercase">
                            {{ str_replace('_', ' ', $k->role->nama ?? '-') }}
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-stone-100 text-xs">
                        @if ($k->guru)
                            <div class="flex justify-between text-stone-500">
                                <span class="text-[10px]">NIP</span>
                                <span class="text-stone-800 font-semibold text-[10px]">{{ $k->guru->nip }}</span>
                            </div>
                            <div class="flex justify-between text-stone-500">
                                <span class="text-[10px]">Jenis Guru</span>
                                <span class="text-indigo-600 font-semibold text-[10px] uppercase">{{ $k->guru->jenis_guru }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px]">Email</span>
                            <span class="text-stone-700 text-[10px] truncate max-w-[140px]">{{ $k->email ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-stone-500">
                            <span class="text-[10px]">No. Telepon</span>
                            <span class="text-stone-700 text-[10px]">{{ $k->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-100 flex justify-between items-center text-[10px]">
                    <span class="text-stone-500">Status Akun</span>
                    <span class="px-2 py-0.5 rounded-md font-extrabold uppercase {{ $k->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $k->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-stone-400 text-xs">
                Tidak ada data karyawan yang sesuai kriteria pencarian.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $karyawanList->links() }}
    </div>
</div>
