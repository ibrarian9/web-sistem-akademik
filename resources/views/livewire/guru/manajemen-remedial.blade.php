<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                AKADEMIK &amp; EVALUASI BELAJAR
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Manajemen Jadwal Remedial Guru</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Kelola dan jadwalkan sesi remedial per-TP &amp; Mid Semester untuk santri pengampu Anda.</p>
        </div>
        <button type="button" wire:click="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4" />
            <span>Buat Jadwal Remedial</span>
        </button>
    </div>

    <!-- Alert Flash Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Toolbar Filter -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Filter Kelas:</span>
                <select wire:model.live="filterKelas" class="bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k['id'] }}">{{ $k['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Status:</span>
                <select wire:model.live="filterStatus" class="bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="">Semua Status</option>
                    <option value="dijadwalkan">Dijadwalkan</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Remedial Schedule Cards / Table -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal &amp; Waktu</th>
                        <th class="px-5 py-3.5">Kelas &amp; Mapel</th>
                        <th class="px-5 py-3.5">Topik TP / Kategori</th>
                        <th class="px-5 py-3.5">Target Santri</th>
                        <th class="px-5 py-3.5">Ruangan / Catatan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 font-medium text-stone-800">
                    @forelse ($remedialList as $item)
                        <tr class="hover:bg-stone-50 transition duration-150">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-stone-900">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</div>
                                <div class="text-[11px] text-stone-500 font-bold mt-0.5 flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3 text-stone-400" />
                                    <span>{{ substr($item->waktu_mulai, 0, 5) }} - {{ substr($item->waktu_selesai, 0, 5) }} WIB</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 inline-block mb-1">
                                    {{ $item->kelas->nama_kelas ?? '-' }}
                                </div>
                                <div class="font-bold text-stone-900">{{ $item->mapel->nama_mapel ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-stone-900">{{ $item->topik_tp }}</div>
                                <div class="mt-1">
                                    @if ($item->kategori === 'harian_tp')
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md text-[10px] font-extrabold uppercase">Nilai Harian / TP</span>
                                    @elseif ($item->kategori === 'mid_sts')
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-900 rounded-md text-[10px] font-extrabold uppercase">Mid Semester (STS)</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-stone-100 text-stone-800 rounded-md text-[10px] font-extrabold uppercase">Umum</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($item->siswa)
                                    <div class="font-bold text-stone-900">{{ $item->siswa->user->nama ?? '-' }}</div>
                                    <div class="text-[10px] text-stone-500 font-medium">NIS: {{ $item->siswa->nis }}</div>
                                @else
                                    <span class="px-2.5 py-1 bg-purple-50 border border-purple-200 text-purple-800 rounded-lg font-extrabold text-[11px]">
                                        Seluruh Kelas
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-stone-900 flex items-center gap-1">
                                    <x-lucide-map-pin class="w-3.5 h-3.5 text-rose-500 shrink-0" />
                                    <span>{{ $item->ruangan }}</span>
                                </div>
                                @if ($item->catatan)
                                    <div class="text-[11px] text-stone-500 italic mt-1 truncate max-w-xs">{{ $item->catatan }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($item->status === 'dijadwalkan')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-extrabold text-[11px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                        Dijadwalkan
                                    </span>
                                @elseif ($item->status === 'selesai')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-extrabold text-[11px] inline-flex items-center gap-1">
                                        ✓ Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-stone-200 text-stone-700 border border-stone-300 rounded-full font-extrabold text-[11px] inline-flex items-center gap-1">
                                        ✕ Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click="openEdit({{ $item->id }})" class="p-1.5 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold border border-amber-300 transition" title="Edit Schedule">
                                        <x-lucide-edit class="w-3.5 h-3.5" />
                                    </button>
                                    @if ($item->status === 'dijadwalkan')
                                        <button type="button" wire:click="updateStatus({{ $item->id }}, 'selesai')" class="p-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-lg font-bold border border-emerald-300 transition" title="Tandai Selesai">
                                            <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-700" />
                                        </button>
                                    @endif
                                    <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus jadwal remedial ini?" class="p-1.5 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold border border-rose-300 transition" title="Hapus Schedule">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-stone-500 font-semibold">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <x-lucide-calendar class="w-8 h-8 text-stone-300" />
                                    <p>Belum ada Jadwal Remedial yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($remedialList->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $remedialList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Create & Edit Remedial -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-stone-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-base font-extrabold text-stone-900">
                        {{ $isEdit ? 'Edit Jadwal Remedial' : 'Buat Jadwal Remedial Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-stone-400 hover:text-stone-700 p-1 rounded-xl hover:bg-stone-100">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                            <select wire:model.live="kelas_id" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k['id'] }}">{{ $k['nama_kelas'] }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                            <select wire:model.live="mapel_id" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach ($mapelList as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['nama_mapel'] }}</option>
                                @endforeach
                            </select>
                            @error('mapel_id') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Target Santri (Opsional)</label>
                        <select wire:model.live="siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Seluruh Santri di Kelas --</option>
                            @foreach ($siswaList as $s)
                                <option value="{{ $s['id'] }}">{{ $s['user']['nama'] ?? '-' }} (NIS: {{ $s['nis'] }})</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-stone-500">Biarkan kosong jika remedial berlaku untuk seluruh kelas.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Topik TP / Materi <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="topik_tp" placeholder="misal: TP 1 Surah Al-Fajr / Perhitungan Mid" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600" />
                            @error('topik_tp') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Kategori Evaluasi <span class="text-rose-500">*</span></label>
                            <select wire:model="kategori" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="harian_tp">Nilai Harian / Per-TP</option>
                                <option value="mid_sts">Mid Semester (STS)</option>
                                <option value="umum">Umum</option>
                            </select>
                            @error('kategori') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Tanggal <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="tanggal" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Waktu Mulai <span class="text-rose-500">*</span></label>
                            <input type="time" wire:model="waktu_mulai" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600" />
                            @error('waktu_mulai') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Waktu Selesai <span class="text-rose-500">*</span></label>
                            <input type="time" wire:model="waktu_selesai" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600" />
                            @error('waktu_selesai') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Ruangan / Lokasi <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="ruangan" placeholder="misal: Ruang Kelas 3A / Lab" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600" />
                            @error('ruangan') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Status Remedial <span class="text-rose-500">*</span></label>
                            <select wire:model="status" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="dijadwalkan">Dijadwalkan</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                            @error('status') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Catatan / Instruksi Tambahan</label>
                        <textarea wire:model="catatan" rows="2" placeholder="Catatan seperti: Membawa modul latihan & alat tulis..." class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600"></textarea>
                        @error('catatan') <span class="text-[11px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-xl text-xs transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition shadow-xs">
                            Simpan Jadwal Remedial
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
