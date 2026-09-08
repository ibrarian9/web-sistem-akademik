<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Ekstrakurikuler & Penugasan Pembina" 
        subtitle="Kelola data entitas ekstrakurikuler, penetapan guru pembimbing, dan pendaftaran anggota santri."
        badge="TATA USAHA"
        badgeVariant="emerald"
        icon="award"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreate">
                Tambah Ekstrakurikuler
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Ekstrakurikuler oleh Tata Usaha"
        :steps="[
            ['title' => 'Penugasan Pembina', 'desc' => 'Klik Edit pada salah satu ekstrakurikuler untuk menetapkan atau mengubah guru pembimbing/pembina.'],
            ['title' => 'Plotting Santri Terdaftar', 'desc' => 'Klik tombol Anggota Santri untuk mendaftarkan atau mengeluarkan santri dari roster ekstrakurikuler.'],
            ['title' => 'Pengaturan Jadwal', 'desc' => 'Hari, jam, dan lokasi ruangan kegiatan dikelola secara terpusat oleh Admin pada menu Jadwal.']
        ]"
        notes="Struktur ekstrakurikuler mengadopsi model kelas dengan guru pembimbing dan daftar anggota terdaftar."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari ekstrakurikuler atau nama pembina..." />
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="10">10 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <x-table loadingTarget="search, perPage">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[200px]">Ekstrakurikuler</x-table.th>
                    <x-table.th class="min-w-[220px]">Guru Pembina / Pembimbing</x-table.th>
                    <x-table.th class="w-40 text-center">Anggota / Kuota</x-table.th>
                    <x-table.th class="min-w-[180px]">Jadwal & Lokasi (Admin)</x-table.th>
                    <x-table.th align="center" class="w-28">Status</x-table.th>
                    <x-table.th align="center" class="w-44">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($ekskuls as $item)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-sm flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black">
                                    <x-lucide-award class="w-4 h-4" />
                                </span>
                                <div>
                                    <div>{{ $item->nama }}</div>
                                    <div class="text-[11px] text-stone-500 font-normal line-clamp-1">{{ $item->deskripsi ?: 'Tidak ada deskripsi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if ($item->pembina)
                                <div class="font-bold text-stone-900 text-xs">{{ $item->pembina->user->nama ?? '-' }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">NIP/NIY: {{ $item->pembina->nip ?? ($item->pembina->niy ?? '-') }}</div>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg">
                                    <x-lucide-alert-triangle class="w-3.5 h-3.5" />
                                    <span>Belum Ditugaskan</span>
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200 text-center">
                            <div class="font-extrabold text-stone-900 text-xs">
                                {{ $item->siswa_ekskul_count }} / {{ $item->kuota }}
                            </div>
                            <div class="w-full bg-stone-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                @php $pct = $item->kuota > 0 ? ($item->siswa_ekskul_count / $item->kuota) * 100 : 0; @endphp
                                <div class="h-1.5 rounded-full {{ $pct >= 100 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if ($item->hari)
                                <div class="text-xs font-extrabold text-stone-800 flex items-center gap-1.5">
                                    <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>{{ ucfirst($item->hari) }}, {{ $item->jam_mulai ?: '00:00' }} - {{ $item->jam_selesai ?: '00:00' }}</span>
                                </div>
                                <div class="text-[11px] text-stone-500 flex items-center gap-1 mt-0.5">
                                    <x-lucide-map-pin class="w-3 h-3 text-stone-400" />
                                    <span>{{ $item->tempat ?: 'Tempat belum ditentukan' }}</span>
                                </div>
                            @else
                                <span class="text-xs text-stone-400 italic">Belum dijadwalkan oleh Admin</span>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200 text-center">
                            @if ($item->status_aktif)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase bg-stone-200 text-stone-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="openRosterModal({{ $item->id }})" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition"
                                    title="Plotting Santri">
                                    <x-lucide-users class="w-3.5 h-3.5" />
                                    <span>Santri</span>
                                </button>
                                <button wire:click="openEdit({{ $item->id }})" 
                                    class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition"
                                    title="Edit & Penugasan Pembina">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </button>
                                <button wire:click="delete({{ $item->id }})" 
                                    wire:confirm="Yakin ingin menghapus ekstrakurikuler ini?"
                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition"
                                    title="Hapus">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-stone-400">
                            <x-lucide-inbox class="w-10 h-10 mx-auto text-stone-300 mb-2" />
                            <div class="font-extrabold text-stone-600 text-sm">Belum Ada Ekstrakurikuler</div>
                            <p class="text-xs text-stone-400 mt-1">Klik tombol Tambah Ekstrakurikuler untuk membuat kegiatan baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-4">
            {{ $ekskuls->links() }}
        </div>
    </div>

    <!-- ================= MODAL TAMBAH / EDIT EKSKUL & PENUGASAN PEMBINA ================= -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-stone-200 overflow-hidden">
                <div class="p-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                    <h3 class="font-extrabold text-stone-900 text-base">
                        {{ $ekskulId ? 'Ubah Ekstrakurikuler & Pembina' : 'Tambah Ekstrakurikuler Baru' }}
                    </h3>
                    <button wire:click="resetForm" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nama Ekstrakurikuler *</label>
                        <input type="text" wire:model="nama" placeholder="Contoh: Pramuka, Futsal, Robotik, Tahfidz" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                        @error('nama') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Penugasan Guru Pembina (TU) *</label>
                        <select wire:model="pembina_guru_id" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="">-- Pilih Guru Pembimbing / Pembina --</option>
                            @foreach ($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} (NIP: {{ $g->nip ?: ($g->niy ?: '-') }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-stone-500 mt-1">Staf Tata Usaha berwenang menetapkan guru pembimbing kegiatan ekstrakurikuler ini.</p>
                        @error('pembina_guru_id') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Batas Kuota Santri *</label>
                            <input type="number" wire:model="kuota" min="1" max="200" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                            @error('kuota') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Status Keaktifan *</label>
                            <select wire:model="status_aktif" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            @error('status_aktif') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Deskripsi & Tujuan Kegiatan</label>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan keterangan singkat mengenai kegiatan ekstrakurikuler..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
                        @error('deskripsi') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-2">
                        <button type="button" wire:click="resetForm" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-100 text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs">
                            Simpan Ekstrakurikuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL PLOTTING ROSTER SANTRI ================= -->
    @if ($isRosterModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-stone-200 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-stone-900 text-base">Plotting Santri Terdaftar</h3>
                        <p class="text-xs text-stone-500 mt-0.5">
                            Ekstrakurikuler: <strong class="text-emerald-800">{{ $currentEkskul->nama ?? '-' }}</strong> &bull; Pembina: {{ $currentEkskul->pembina->user->nama ?? 'Belum Ada' }}
                        </p>
                    </div>
                    <button wire:click="closeRosterModal" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    @if (session()->has('roster_success'))
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-bold flex items-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                            <span>{{ session('roster_success') }}</span>
                        </div>
                    @endif

                    @if (session()->has('roster_error'))
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs font-bold flex items-center gap-2">
                            <x-lucide-alert-circle class="w-4 h-4 text-rose-600" />
                            <span>{{ session('roster_error') }}</span>
                        </div>
                    @endif

                    <!-- Add Student Form -->
                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200 space-y-3">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Tambah Santri ke Ekstrakurikuler</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div class="sm:col-span-2">
                                <select wire:model="selectedSiswaIdToAdd" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                                    <option value="">-- Pilih Santri Aktif --</option>
                                    @foreach ($availableSiswas as $s)
                                        <option value="{{ $s->id }}">NIS: {{ $s->nis }} - {{ $s->user->nama ?? '-' }} ({{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="button" wire:click="addSiswaToEkskul" class="w-full h-full py-2 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs">
                                    <x-lucide-user-plus class="w-4 h-4" />
                                    <span>Daftarkan</span>
                                </button>
                            </div>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchSiswaQuery" placeholder="Cari santri berdasarkan nama atau NIS..." class="w-full rounded-xl border border-stone-200 px-3 py-1.5 text-xs text-stone-700 bg-white">
                    </div>

                    <!-- Enrolled Students Table -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-extrabold text-stone-900 text-xs uppercase tracking-wider">
                                Daftar Santri Terdaftar ({{ $rosterList->count() }} / {{ $currentEkskul->kuota ?? 30 }})
                            </h4>
                        </div>

                        <div class="border border-stone-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-stone-100 text-stone-600 font-extrabold uppercase text-[10px]">
                                    <tr>
                                        <th class="p-2.5">NIS</th>
                                        <th class="p-2.5">Nama Santri</th>
                                        <th class="p-2.5">Kelas</th>
                                        <th class="p-2.5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200">
                                    @forelse ($rosterList as $r)
                                        <tr class="hover:bg-stone-50">
                                            <td class="p-2.5 font-bold text-stone-700">{{ $r->siswa->nis ?? '-' }}</td>
                                            <td class="p-2.5 font-extrabold text-stone-900">{{ $r->siswa->user->nama ?? '-' }}</td>
                                            <td class="p-2.5 text-stone-600">{{ $r->siswa->kelas->nama_kelas ?? '-' }}</td>
                                            <td class="p-2.5 text-center">
                                                <button type="button" wire:click="removeSiswaFromEkskul({{ $r->id }})" 
                                                    wire:confirm="Keluarkan santri ini dari ekstrakurikuler?"
                                                    class="p-1 rounded-lg text-rose-600 hover:bg-rose-50 transition" title="Keluarkan">
                                                    <x-lucide-user-minus class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-stone-400">
                                                Belum ada santri terdaftar pada ekstrakurikuler ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-stone-50 border-t border-stone-200 flex justify-end">
                    <button type="button" wire:click="closeRosterModal" class="px-4 py-2 rounded-xl bg-stone-200 hover:bg-stone-300 text-stone-800 text-xs font-bold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
