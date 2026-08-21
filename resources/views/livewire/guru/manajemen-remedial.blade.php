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
        <x-table loadingTarget="filterKelas, filterStatus">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-48">Tanggal &amp; Waktu</x-table.th>
                    <x-table.th class="w-44">Kelas &amp; Mapel</x-table.th>
                    <x-table.th class="min-w-[160px]">Topik TP / Kategori</x-table.th>
                    <x-table.th class="w-44">Target Santri</x-table.th>
                    <x-table.th class="min-w-[150px]">Ruangan / Catatan</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($remedialList as $item)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</div>
                            <div class="text-[10px] text-stone-500 font-bold mt-0.5 flex items-center gap-1">
                                <x-lucide-clock class="w-3 h-3 text-stone-400" />
                                <span>{{ substr($item->waktu_mulai, 0, 5) }} - {{ substr($item->waktu_selesai, 0, 5) }} WIB</span>
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200 inline-block mb-1 text-[11px]">
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </div>
                            <div class="font-bold text-stone-900 text-xs">{{ $item->mapel->nama_mapel ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-stone-900 text-xs">{{ $item->topik_tp }}</div>
                            <div class="mt-1">
                                @if ($item->kategori === 'harian_tp')
                                    <x-badge variant="sky" size="xs">Nilai Harian / TP</x-badge>
                                @elseif ($item->kategori === 'mid_sts')
                                    <x-badge variant="amber" size="xs">Mid Semester (STS)</x-badge>
                                @else
                                    <x-badge variant="stone" size="xs">Umum</x-badge>
                                @endif
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if ($item->siswa)
                                <div class="font-bold text-stone-900 text-xs">{{ $item->siswa->user->nama ?? '-' }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">NIS: {{ $item->siswa->nis }}</div>
                            @else
                                <x-badge variant="purple" size="xs">Seluruh Kelas</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-stone-900 text-xs flex items-center gap-1">
                                <x-lucide-map-pin class="w-3.5 h-3.5 text-rose-500 shrink-0" />
                                <span>{{ $item->ruangan }}</span>
                            </div>
                            @if ($item->catatan)
                                <div class="text-[10px] text-stone-500 italic mt-1 truncate max-w-xs">{{ $item->catatan }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->status === 'dijadwalkan')
                                <x-badge variant="amber" size="xs">Dijadwalkan</x-badge>
                            @elseif ($item->status === 'selesai')
                                <x-badge variant="emerald" size="xs">✓ Selesai</x-badge>
                            @else
                                <x-badge variant="stone" size="xs">✕ Batal</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button variant="outline" size="xs" icon="edit" wire:click="openEdit({{ $item->id }})" title="Edit Jadwal" />
                                @if ($item->status === 'dijadwalkan')
                                    <x-button variant="primary" size="xs" icon="check" wire:click="updateStatus({{ $item->id }}, 'selesai')" title="Tandai Selesai" />
                                    <x-button variant="secondary" size="xs" icon="x" wire:click="updateStatus({{ $item->id }}, 'dibatalkan')" title="Batalkan" />
                                @endif
                                <x-button variant="danger-outline" size="xs" icon="trash-2" wire:click="delete({{ $item->id }})" wire:confirm="Hapus jadwal remedial ini?" title="Hapus" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Belum ada jadwal remedial" message="Belum ada agenda remedial yang dijadwalkan." />
                @endforelse
            </tbody>
        </x-table>
        @if ($remedialList->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $remedialList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Create & Edit Remedial -->
    <x-floating-card 
        :show="$showModal"
        :title="$isEdit ? 'Edit Jadwal Remedial' : 'Buat Jadwal Remedial Baru'"
        subtitle="Atur waktu, target santri, dan materi evaluasi remedial."
        badge="REMEDIAL"
        badgeVariant="emerald"
        icon="clock"
        maxWidth="max-w-lg"
        closeAction="$set('showModal', false)"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                    <select wire:model.live="kelas_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k['id'] }}">{{ $k['nama_kelas'] }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <select wire:model.live="mapel_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach ($mapelList as $m)
                            <option value="{{ $m['id'] }}">{{ $m['nama_mapel'] }}</option>
                        @endforeach
                    </select>
                    @error('mapel_id') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Target Santri (Opsional)</label>
                <select wire:model.live="siswa_id" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs">
                    <option value="">-- Seluruh Santri di Kelas --</option>
                    @foreach ($siswaList as $s)
                        <option value="{{ $s['id'] }}">{{ $s['user']['nama'] ?? '-' }} (NIS: {{ $s['nis'] }})</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-stone-500">Biarkan kosong jika remedial berlaku untuk seluruh rombel kelas.</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Topik TP / Materi <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="topik_tp" placeholder="misal: TP 1 Surah Al-Fajr / Perhitungan Mid" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" />
                    @error('topik_tp') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Kategori Evaluasi <span class="text-rose-500">*</span></label>
                    <select wire:model="kategori" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs">
                        <option value="harian_tp">Nilai Harian / Per-TP</option>
                        <option value="mid_sts">Mid Semester (STS)</option>
                        <option value="umum">Umum</option>
                    </select>
                    @error('kategori') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" wire:model="tanggal" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" />
                    @error('tanggal') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Waktu Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" wire:model="waktu_mulai" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" />
                    @error('waktu_mulai') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Waktu Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" wire:model="waktu_selesai" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" />
                    @error('waktu_selesai') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Ruangan / Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="ruangan" placeholder="misal: Ruang Kelas 3A / Lab" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs" />
                    @error('ruangan') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Status Remedial <span class="text-rose-500">*</span></label>
                    <select wire:model="status" class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs">
                        <option value="dijadwalkan">Dijadwalkan</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    @error('status') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Catatan / Instruksi Tambahan</label>
                <textarea wire:model="catatan" rows="2" placeholder="Catatan seperti: Membawa modul latihan & alat tulis..." class="w-full bg-stone-50 border border-stone-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:bg-white shadow-xs"></textarea>
                @error('catatan') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-2">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('showModal', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" loadingTarget="save">
                    Simpan Jadwal Remedial
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
