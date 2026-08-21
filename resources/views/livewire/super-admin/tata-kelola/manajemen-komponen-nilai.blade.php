<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Master Komponen Penilaian Akademik" 
        subtitle="Kelola master komponen penilaian untuk Mapel Umum (Tugas, UH, PTS, PAS) dan Mapel Tahfidz (Hafalan Baru, Muraja'ah, Tajwid)."
        badge="KOMPONEN PENILAIAN"
        badgeVariant="emerald"
        icon="check-square"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreate">
                Tambah Komponen Nilai
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Master Komponen Nilai"
        :steps="[
            ['title' => 'Pilih Kategori Mapel', 'desc' => 'Gunakan tab filter untuk membedakan komponen nilai Mapel Umum, Mapel Tahfidz, atau Semua Mapel.'],
            ['title' => 'Tambah / Edit Komponen', 'desc' => 'Klik tombol Tambah Komponen atau Edit untuk membuat nama komponen penilaian baru.'],
            ['title' => 'Pembobotan Nilai', 'desc' => 'Tata Usaha hanya mendaftarkan master komponen nilai. Persentase bobot sepenuhnya ditentukan oleh Guru Pengampu pada menu Bobot Penilaian.']
        ]"
        notes="Komponen nilai yang sudah digunakan pada data nilai siswa tidak dapat dihapus demi menjaga integritas rapor."
    />

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-stone-200 pb-3 overflow-x-auto">
            <x-button type="button" :variant="$filterBerlaku === 'semua' ? 'primary' : 'secondary'" size="sm" wire:click="$set('filterBerlaku', 'semua')">
                Semua Komponen
            </x-button>
            <x-button type="button" :variant="$filterBerlaku === 'umum' ? 'primary' : 'secondary'" size="sm" icon="book-open" wire:click="$set('filterBerlaku', 'umum')">
                Mapel Umum
            </x-button>
            <x-button type="button" :variant="$filterBerlaku === 'tahfidz' ? 'primary' : 'secondary'" size="sm" icon="bookmark" wire:click="$set('filterBerlaku', 'tahfidz')">
                Mapel Tahfizh
            </x-button>
        </div>

        <div class="divide-y divide-stone-100 border border-stone-200 rounded-xl overflow-hidden bg-white">
            @forelse ($komponens as $index => $komponen)
                @php
                    $berlakuVariant = match($komponen['berlaku_untuk']) {
                        'tahfidz' => 'emerald',
                        'umum' => 'blue',
                        default => 'purple',
                    };
                    $berlakuText = match($komponen['berlaku_untuk']) {
                        'tahfidz' => 'Mapel Tahfidz',
                        'umum' => 'Mapel Umum',
                        default => 'Semua Mapel',
                    };
                @endphp
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-stone-50 transition">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="w-8 h-8 rounded-xl bg-stone-100 text-stone-700 font-bold text-xs flex items-center justify-center shrink-0 border border-stone-200">
                            #{{ $index + 1 }}
                        </span>
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-stone-900 text-sm">{{ $komponen['nama'] }}</span>
                                <x-badge :variant="$berlakuVariant" size="xs">
                                    {{ $berlakuText }}
                                </x-badge>
                            </div>
                            <div class="text-xs text-stone-500 capitalize">
                                Kategori Penilaian: <strong class="text-stone-700 font-semibold">{{ $komponen['kategori'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1.5">
                        <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEdit({{ $komponen['id'] }})">
                            Edit
                        </x-button>
                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $komponen['id'] }})" data-confirm="Yakin ingin menghapus komponen nilai ini?">
                            Hapus
                        </x-button>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-stone-400 text-xs">
                    Belum ada komponen penilaian untuk kategori ini.
                </div>
            @endforelse
        </div>

        @if (count($komponens) > 0)
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-2">
                <span class="text-xs text-stone-600 font-semibold">
                    Total Komponen Ditampilkan: <strong>{{ count($komponens) }}</strong>
                </span>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl">
                    ✓ Komponen aktif dan siap digunakan oleh Guru
                </span>
            </div>
        @endif
    </div>

    <!-- Modal Form Floating Create / Edit -->
    <x-floating-card 
        :show="$isModalOpen ? true : false"
        :title="$editingId ? 'Edit Komponen Nilai' : 'Tambah Komponen Nilai Baru'"
        subtitle="Lengkapi nama komponen, tipe pelajaran, dan kategori penilaian."
        badge="KOMPONEN NILAI"
        badgeVariant="emerald"
        icon="check-square"
        maxWidth="max-w-md"
        closeAction="closeModal"
    >
        <form wire:submit.prevent="saveForm" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Nama Komponen Nilai <span class="text-rose-600">*</span></label>
                <input wire:model="nama" type="text" placeholder="Contoh: Hafalan Baru / Ulangan Harian / PTS" 
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 font-bold shadow-2xs" required />
                @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tipe Pelajaran <span class="text-rose-600">*</span></label>
                    <select wire:model="berlaku_untuk" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 font-bold shadow-2xs">
                        <option value="umum">Mapel Umum</option>
                        <option value="tahfidz">Mapel Tahfidz</option>
                        <option value="semua">Semua Mapel</option>
                    </select>
                    @error('berlaku_untuk') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Kategori Nilai <span class="text-rose-600">*</span></label>
                    <select wire:model="kategori" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 font-bold shadow-2xs">
                        <option value="pengetahuan">Pengetahuan</option>
                        <option value="keterampilan">Keterampilan</option>
                        <option value="keagamaan">Keagamaan</option>
                        <option value="sikap">Sikap</option>
                    </select>
                    @error('kategori') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="saveForm">
                    Simpan Komponen
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
